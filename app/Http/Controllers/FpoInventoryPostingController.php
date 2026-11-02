<?php

namespace App\Http\Controllers;

use App\Models\FpoCollectiveListing;
use App\Models\FpoGateEntry;
use App\Models\FpoGateEntryItem;
use App\Models\FpoInventoryPosting;
use App\Models\FpoOperationAuditLog;
use App\Models\FpoQualityApproval;
use App\Models\ProduceLot;
use App\Notifications\InventoryPostedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FpoInventoryPostingController extends Controller
{
    /**
     * Idempotent posting of approved produce into inventory.
     */
    public function post(Request $request, FpoGateEntry $entry)
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        $this->authorize('postInventory', $entry);

        // 1. Strict Idempotency Check: prevent duplicate posting on retries
        $idempotencyKey = "POST-GE-{$entry->id}";
        $existingPosting = FpoInventoryPosting::where('idempotency_key', $idempotencyKey)->first();

        if ($existingPosting) {
            return redirect()->route('fpo.gate-entries.show', $entry->id)
                ->with('info', "Inventory has already been posted for this gate entry (Ref #{$existingPosting->posting_number}).");
        }

        // 2. Enforce that gate entry must be approved or partially accepted
        if (! in_array($entry->status, [FpoGateEntry::STATUS_APPROVED, FpoGateEntry::STATUS_PARTIALLY_ACCEPTED])) {
            throw ValidationException::withMessages([
                'inventory' => "Cannot post inventory: Gate entry status is '{$entry->getStatusLabel()}'. Only authorized approved or partially accepted deliveries can be posted.",
            ]);
        }

        $latestApproval = $entry->latestApproval;
        if (! $latestApproval) {
            throw ValidationException::withMessages([
                'inventory' => 'Cannot post inventory without an authorized quality approval sign-off.',
            ]);
        }

        $acceptedQtyKg = (float) $latestApproval->accepted_quantity;
        if ($acceptedQtyKg <= 0) {
            throw ValidationException::withMessages([
                'inventory' => 'No accepted produce quantity available to post into inventory (Accepted: 0 kg).',
            ]);
        }

        $postingNumber = 'POST-'.date('Ymd').'-'.rand(1000, 9999);

        $posting = DB::transaction(function () use ($entry, $acceptedQtyKg, $postingNumber, $idempotencyKey, $user) {
            // Atomic state check to prevent race conditions
            $affected = FpoGateEntry::where('id', $entry->id)
                ->whereIn('status', [FpoGateEntry::STATUS_APPROVED, FpoGateEntry::STATUS_PARTIALLY_ACCEPTED])
                ->update(['status' => FpoGateEntry::STATUS_INVENTORY_POSTED]);

            if ($affected === 0) {
                throw ValidationException::withMessages([
                    'inventory' => 'This entry has already been posted or its operational state has changed.',
                ]);
            }

            $sourceBeforeKg = 0.0;
            $sourceAfterKg = 0.0;

            // Target A: Source Produce Lot
            if ($entry->source_produce_lot_id) {
                $lot = ProduceLot::where('id', $entry->source_produce_lot_id)->lockForUpdate()->firstOrFail();
                $sourceBeforeKg = (float) $lot->available_quantity_kg;

                $lot->confirmed_quantity_kg = round($lot->confirmed_quantity_kg + $acceptedQtyKg, 2);
                $lot->available_quantity_kg = round($lot->available_quantity_kg + $acceptedQtyKg, 2);

                if (in_array($lot->status, [ProduceLot::STATUS_APPROVED_FOR_PROCUREMENT, ProduceLot::STATUS_PURCHASE_CONFIRMED, ProduceLot::STATUS_DRAFT, ProduceLot::STATUS_SUBMITTED])) {
                    $lot->status = ProduceLot::STATUS_INVENTORY_AVAILABLE;
                }

                $lot->save();
                $sourceAfterKg = (float) $lot->available_quantity_kg;
            } elseif ($entry->fpo_collective_listing_id) {
                // Target B: FPO Collective Bulk Listing
                $collective = FpoCollectiveListing::where('id', $entry->fpo_collective_listing_id)->lockForUpdate()->firstOrFail();
                $sourceBeforeKg = (float) $collective->available_quantity_kg;

                $collective->total_aggregated_quantity_kg = round($collective->total_aggregated_quantity_kg + $acceptedQtyKg, 2);
                $collective->available_quantity_kg = round($collective->available_quantity_kg + $acceptedQtyKg, 2);
                if ($collective->status === FpoCollectiveListing::STATUS_DRAFT) {
                    $collective->status = FpoCollectiveListing::STATUS_PUBLISHED;
                }
                $collective->save();
                $sourceAfterKg = (float) $collective->available_quantity_kg;
            }

            // Create Inventory Posting Record
            $postingRecord = FpoInventoryPosting::create([
                'posting_number' => $postingNumber,
                'fpo_organization_id' => $entry->fpo_organization_id,
                'fpo_gate_entry_id' => $entry->id,
                'fpo_gate_entry_item_id' => $entry->items()->first()?->id,
                'produce_lot_id' => $entry->source_produce_lot_id,
                'fpo_collective_listing_id' => $entry->fpo_collective_listing_id,
                'posted_quantity_kg' => $acceptedQtyKg,
                'source_stock_before_kg' => $sourceBeforeKg,
                'source_stock_after_kg' => $sourceAfterKg,
                'status' => FpoInventoryPosting::STATUS_POSTED,
                'posted_by' => $user->id,
                'posted_at' => now(),
                'idempotency_key' => $idempotencyKey,
            ]);

            // Update line items status
            foreach ($entry->items as $item) {
                $item->update(['status' => FpoGateEntryItem::STATUS_INVENTORY_POSTED]);
            }

            FpoOperationAuditLog::log(
                fpoOrgId: $entry->fpo_organization_id,
                gateEntryId: $entry->id,
                entityType: 'inventory_posting',
                entityId: $postingRecord->id,
                action: 'posted',
                actor: $user,
                fromState: FpoGateEntry::STATUS_APPROVED,
                toState: FpoGateEntry::STATUS_INVENTORY_POSTED,
                reason: "Inventory posted: {$acceptedQtyKg} kg made available for marketplace procurement.",
                metadata: [
                    'posting_number' => $postingNumber,
                    'stock_before_kg' => $sourceBeforeKg,
                    'stock_after_kg' => $sourceAfterKg,
                ]
            );

            return $postingRecord;
        });

        // Notifications
        if ($entry->farmer) {
            $entry->farmer->notify(new InventoryPostedNotification($entry, $posting));
        }

        return redirect()->route('fpo.gate-entries.show', $entry->id)
            ->with('success', "Inventory posted successfully: {$acceptedQtyKg} kg is now available in inventory (Ref: #{$posting->posting_number}).");
    }

    /**
     * Documented reversal/correction workflow: safely reverts posted stock.
     */
    public function reversal(Request $request, FpoGateEntry $entry, FpoInventoryPosting $posting)
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        $this->authorize('postInventory', $entry);

        $validated = $request->validate([
            'reversal_reason' => 'required|string|max:500',
        ]);

        if ($posting->status === FpoInventoryPosting::STATUS_REVERSED) {
            throw ValidationException::withMessages([
                'reversal' => 'This inventory posting has already been reversed.',
            ]);
        }

        $reversedQtyKg = (float) $posting->posted_quantity_kg;

        DB::transaction(function () use ($posting, $entry, $reversedQtyKg, $validated, $user) {
            // Revert stock from source
            if ($posting->produce_lot_id) {
                $lot = ProduceLot::where('id', $posting->produce_lot_id)->lockForUpdate()->first();
                if ($lot) {
                    $lot->available_quantity_kg = max(0, round($lot->available_quantity_kg - $reversedQtyKg, 2));
                    $lot->confirmed_quantity_kg = max(0, round($lot->confirmed_quantity_kg - $reversedQtyKg, 2));
                    $lot->save();
                }
            } elseif ($posting->fpo_collective_listing_id) {
                $collective = FpoCollectiveListing::where('id', $posting->fpo_collective_listing_id)->lockForUpdate()->first();
                if ($collective) {
                    $collective->available_quantity_kg = max(0, round($collective->available_quantity_kg - $reversedQtyKg, 2));
                    $collective->total_aggregated_quantity_kg = max(0, round($collective->total_aggregated_quantity_kg - $reversedQtyKg, 2));
                    $collective->save();
                }
            }

            $posting->update([
                'status' => FpoInventoryPosting::STATUS_REVERSED,
                'reversal_reason' => $validated['reversal_reason'],
                'reversed_by' => $user->id,
                'reversed_at' => now(),
            ]);

            $entry->status = FpoGateEntry::STATUS_APPROVED;
            $entry->save();

            FpoOperationAuditLog::log(
                fpoOrgId: $entry->fpo_organization_id,
                gateEntryId: $entry->id,
                entityType: 'inventory_posting',
                entityId: $posting->id,
                action: 'reversed',
                actor: $user,
                fromState: FpoGateEntry::STATUS_INVENTORY_POSTED,
                toState: FpoGateEntry::STATUS_APPROVED,
                reason: "Inventory posting reversed: {$reversedQtyKg} kg deducted. Reason: {$validated['reversal_reason']}"
            );
        });

        return redirect()->route('fpo.gate-entries.show', $entry->id)
            ->with('success', "Inventory posting #{$posting->posting_number} has been reversed. Stock has been adjusted.");
    }

    /**
     * Stock Reconciliation view comparing declared, measured, accepted, rejected, and posted stock.
     */
    public function reconciliation(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $this->authorize('viewAny', FpoGateEntry::class);

        $org = $user->isAdmin() ? FpoOrganization::first() : $user->primaryFpoOrganization();

        $query = FpoGateEntry::with(['fpoOrganization', 'sourceProduceLot', 'acceptedWeightment', 'latestApproval', 'inventoryPostings'])
            ->where('fpo_organization_id', $org?->id)
            ->orderBy('id', 'desc');

        if ($request->filled('crop')) {
            $query->where('crop_name', $request->input('crop'));
        }

        $entries = $query->paginate(15)->withQueryString();

        return view('pages.fpo-reconciliation', compact('entries', 'org', 'user'));
    }
}
