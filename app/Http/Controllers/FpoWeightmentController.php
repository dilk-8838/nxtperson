<?php

namespace App\Http\Controllers;

use App\Models\FpoGateEntry;
use App\Models\FpoGateEntryItem;
use App\Models\FpoOperationAuditLog;
use App\Models\FpoOperationalException;
use App\Models\FpoWeightment;
use App\Notifications\WeightDiscrepancyNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FpoWeightmentController extends Controller
{
    /**
     * Store new weighment record for a gate entry or line item.
     */
    public function store(Request $request, FpoGateEntry $entry)
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        $this->authorize('recordWeightment', $entry);

        $validated = $request->validate([
            'fpo_gate_entry_item_id' => 'nullable|exists:fpo_gate_entry_items,id',
            'gross_weight' => 'required|numeric|min:0.1|max:1000000',
            'tare_weight' => 'required|numeric|min:0|max:1000000',
            'unit' => 'required|string|in:kg,quintal,tonne',
            'weighbridge_reference' => 'nullable|string|max:100',
            'operator_name' => 'required|string|max:100',
            'measurement_method' => 'required|string|in:manual,automated_weighbridge',
            'notes' => 'nullable|string|max:1000',
        ]);

        $gross = (float) $validated['gross_weight'];
        $tare = (float) $validated['tare_weight'];

        if ($gross < $tare) {
            throw ValidationException::withMessages([
                'gross_weight' => 'Gross weight must be greater than or equal to Tare weight.',
            ]);
        }

        $net = round($gross - $tare, 2);
        if ($net < 0) {
            throw ValidationException::withMessages([
                'gross_weight' => 'Calculated net weight cannot be negative.',
            ]);
        }

        // Validate operational limits in kg
        $grossInKg = FpoWeightment::convertToKg($gross, $validated['unit']);
        $minKg = (float) config('agritech.weighbridge.min_gross_weight_kg', 10.0);
        $maxKg = (float) config('agritech.weighbridge.max_gross_weight_kg', 60000.0);

        if ($grossInKg < $minKg || $grossInKg > $maxKg) {
            throw ValidationException::withMessages([
                'gross_weight' => "Gross weight of {$grossInKg} kg is outside acceptable operational limits ({$minKg} kg - {$maxKg} kg).",
            ]);
        }

        // Reconcile with declared delivery quantity
        $declaredInKg = FpoWeightment::convertToKg((float) $entry->declared_quantity, $entry->declared_unit);
        $netInKg = FpoWeightment::convertToKg($net, $validated['unit']);

        $discrepancyPercentage = 0;
        if ($declaredInKg > 0) {
            $discrepancyPercentage = round(abs($netInKg - $declaredInKg) / $declaredInKg * 100, 2);
        }

        $tolerancePercent = (float) config('agritech.weight_discrepancy_tolerance_percentage', 5.0);
        $hasDiscrepancy = $discrepancyPercentage > $tolerancePercent;

        $weightmentNumber = 'WM-'.date('Ymd').'-'.rand(1000, 9999);
        $isManual = ($validated['measurement_method'] === 'manual');

        $weightment = DB::transaction(function () use (
            $entry,
            $validated,
            $gross,
            $tare,
            $net,
            $netInKg,
            $discrepancyPercentage,
            $hasDiscrepancy,
            $weightmentNumber,
            $isManual,
            $user
        ) {
            $seq = $entry->weightments()->count() + 1;

            $record = FpoWeightment::create([
                'weightment_number' => $weightmentNumber,
                'fpo_gate_entry_id' => $entry->id,
                'fpo_gate_entry_item_id' => $validated['fpo_gate_entry_item_id'] ?? null,
                'sequence_number' => $seq,
                'gross_weight' => $gross,
                'tare_weight' => $tare,
                'net_weight' => $net,
                'unit' => $validated['unit'],
                'weighbridge_reference' => $validated['weighbridge_reference'] ?? ($isManual ? 'Manual Certified Scale' : 'WB-Scale-01'),
                'operator_name' => $validated['operator_name'],
                'measurement_method' => $validated['measurement_method'],
                'is_manual_entry' => $isManual,
                'discrepancy_flag' => $hasDiscrepancy,
                'discrepancy_percentage' => $discrepancyPercentage,
                'discrepancy_reason' => $hasDiscrepancy ? "Variance of {$discrepancyPercentage}% exceeds allowable tolerance of ".config('agritech.weight_discrepancy_tolerance_percentage', 5.0).'%' : null,
                'discrepancy_resolved' => false,
                'is_accepted' => true,
                'notes' => $validated['notes'] ?? null,
                'created_by' => $user->id,
                'measured_at' => now(),
            ]);

            // Update line item measured quantity
            if (! empty($validated['fpo_gate_entry_item_id'])) {
                $item = FpoGateEntryItem::find($validated['fpo_gate_entry_item_id']);
                if ($item) {
                    $item->update([
                        'measured_net_quantity' => $net,
                        'status' => FpoGateEntryItem::STATUS_WEIGHED,
                    ]);
                }
            } else {
                // If single item, update the first line item
                $firstItem = $entry->items()->first();
                if ($firstItem) {
                    $firstItem->update([
                        'measured_net_quantity' => $net,
                        'status' => FpoGateEntryItem::STATUS_WEIGHED,
                    ]);
                }
            }

            // Update entry status
            $entry->status = FpoGateEntry::STATUS_WEIGHED;
            $entry->save();

            // If discrepancy exceeds tolerance, create operational exception
            if ($hasDiscrepancy) {
                FpoOperationalException::create([
                    'exception_number' => 'EXC-'.date('Ymd').'-'.rand(1000, 9999),
                    'fpo_organization_id' => $entry->fpo_organization_id,
                    'fpo_gate_entry_id' => $entry->id,
                    'fpo_gate_entry_item_id' => $validated['fpo_gate_entry_item_id'] ?? null,
                    'exception_type' => FpoOperationalException::TYPE_WEIGHT_DISCREPANCY,
                    'severity' => ($discrepancyPercentage > 15.0) ? FpoOperationalException::SEVERITY_CRITICAL : FpoOperationalException::SEVERITY_MEDIUM,
                    'title' => "Weight discrepancy of {$discrepancyPercentage}% on Entry #{$entry->entry_number}",
                    'description' => "Declared: {$entry->declared_quantity} {$entry->declared_unit}. Measured Net: {$net} {$validated['unit']}. Variance exceeds allowable tolerance.",
                    'status' => FpoOperationalException::STATUS_OPEN,
                    'created_by' => $user->id,
                ]);
            }

            FpoOperationAuditLog::log(
                fpoOrgId: $entry->fpo_organization_id,
                gateEntryId: $entry->id,
                entityType: 'weightment',
                entityId: $record->id,
                action: 'created',
                actor: $user,
                fromState: null,
                toState: 'weighed',
                reason: "Gross: {$gross} {$validated['unit']}, Tare: {$tare} {$validated['unit']}, Net: {$net} {$validated['unit']}. Method: {$validated['measurement_method']}.",
                metadata: [
                    'discrepancy_flag' => $hasDiscrepancy,
                    'discrepancy_percentage' => $discrepancyPercentage,
                ]
            );

            return $record;
        });

        // Discrepancy notification if flagged
        if ($hasDiscrepancy && $entry->farmer) {
            $entry->farmer->notify(new WeightDiscrepancyNotification($entry, $weightment));
        }

        return redirect()->route('fpo.gate-entries.show', $entry->id)
            ->with('success', "Weightment record #{$weightment->weightment_number} saved (Net Weight: {$weightment->net_weight} {$weightment->unit}).".($hasDiscrepancy ? ' NOTE: Discrepancy flagged and logged for review.' : ''));
    }

    /**
     * Reweighment: creates a traceable new measurement and supersedes prior one.
     */
    public function reweigh(Request $request, FpoGateEntry $entry)
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        $this->authorize('recordWeightment', $entry);

        $validated = $request->validate([
            'original_weightment_id' => 'required|exists:fpo_weightments,id',
            'gross_weight' => 'required|numeric|min:0.1|max:1000000',
            'tare_weight' => 'required|numeric|min:0|max:1000000',
            'unit' => 'required|string|in:kg,quintal,tonne',
            'operator_name' => 'required|string|max:100',
            'reweighment_reason' => 'required|string|max:500',
            'weighbridge_reference' => 'nullable|string|max:100',
            'measurement_method' => 'required|string|in:manual,automated_weighbridge',
        ]);

        $prior = FpoWeightment::where('id', $validated['original_weightment_id'])
            ->where('fpo_gate_entry_id', $entry->id)
            ->firstOrFail();

        $gross = (float) $validated['gross_weight'];
        $tare = (float) $validated['tare_weight'];

        if ($gross < $tare) {
            throw ValidationException::withMessages([
                'gross_weight' => 'Gross weight must be greater than or equal to Tare weight.',
            ]);
        }

        $net = round($gross - $tare, 2);
        $weightmentNumber = 'WM-'.date('Ymd').'-'.rand(1000, 9999);
        $isManual = ($validated['measurement_method'] === 'manual');

        $newWeightment = DB::transaction(function () use ($prior, $entry, $validated, $gross, $tare, $net, $weightmentNumber, $isManual, $user) {
            // Mark prior measurement as superseded
            $prior->update(['is_accepted' => false]);

            $record = FpoWeightment::create([
                'weightment_number' => $weightmentNumber,
                'fpo_gate_entry_id' => $entry->id,
                'fpo_gate_entry_item_id' => $prior->fpo_gate_entry_item_id,
                'sequence_number' => $prior->sequence_number + 1,
                'gross_weight' => $gross,
                'tare_weight' => $tare,
                'net_weight' => $net,
                'unit' => $validated['unit'],
                'weighbridge_reference' => $validated['weighbridge_reference'] ?? $prior->weighbridge_reference,
                'operator_name' => $validated['operator_name'],
                'measurement_method' => $validated['measurement_method'],
                'is_manual_entry' => $isManual,
                'discrepancy_flag' => false,
                'discrepancy_resolved' => true,
                'is_accepted' => true,
                'reweighment_of_id' => $prior->id,
                'notes' => "Reweighment: {$validated['reweighment_reason']}",
                'created_by' => $user->id,
                'measured_at' => now(),
            ]);

            // Update item net weight
            if ($prior->fpo_gate_entry_item_id) {
                FpoGateEntryItem::where('id', $prior->fpo_gate_entry_item_id)->update([
                    'measured_net_quantity' => $net,
                ]);
            }

            FpoOperationAuditLog::log(
                fpoOrgId: $entry->fpo_organization_id,
                gateEntryId: $entry->id,
                entityType: 'weightment',
                entityId: $record->id,
                action: 'reweighed',
                actor: $user,
                fromState: "Seq {$prior->sequence_number}",
                toState: "Seq {$record->sequence_number}",
                reason: $validated['reweighment_reason'],
                metadata: [
                    'superseded_weightment_id' => $prior->id,
                    'prior_net' => "{$prior->net_weight} {$prior->unit}",
                    'new_net' => "{$net} {$validated['unit']}",
                ]
            );

            return $record;
        });

        return redirect()->route('fpo.gate-entries.show', $entry->id)
            ->with('success', "Reweighment #{$newWeightment->weightment_number} completed. Prior measurement preserved in audit history.");
    }

    /**
     * Resolve weight discrepancy with authorized explanation or override.
     */
    public function resolveDiscrepancy(Request $request, FpoGateEntry $entry, FpoWeightment $weightment)
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        $this->authorize('update', $entry);

        $validated = $request->validate([
            'resolution_reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($weightment, $entry, $validated, $user) {
            $weightment->update([
                'discrepancy_resolved' => true,
                'discrepancy_resolved_by' => $user->id,
                'discrepancy_resolution_notes' => $validated['resolution_reason'],
            ]);

            // Resolve open weight discrepancy exception
            $exception = $entry->exceptions()
                ->where('exception_type', FpoOperationalException::TYPE_WEIGHT_DISCREPANCY)
                ->where('status', 'open')
                ->first();

            if ($exception) {
                $exception->update([
                    'status' => FpoOperationalException::STATUS_RESOLVED,
                    'resolution_action' => 'Authorized discrepancy override approved.',
                    'resolution_reason' => $validated['resolution_reason'],
                    'resolved_by' => $user->id,
                    'resolved_at' => now(),
                ]);
            }

            FpoOperationAuditLog::log(
                fpoOrgId: $entry->fpo_organization_id,
                gateEntryId: $entry->id,
                entityType: 'weightment',
                entityId: $weightment->id,
                action: 'discrepancy_resolved',
                actor: $user,
                reason: $validated['resolution_reason']
            );
        });

        return redirect()->route('fpo.gate-entries.show', $entry->id)
            ->with('success', 'Weight discrepancy has been resolved and approved.');
    }

    /**
     * Printable weighbridge slip.
     */
    public function printSlip(FpoGateEntry $entry, FpoWeightment $weightment)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $this->authorize('view', $entry);

        $entry->load(['fpoOrganization', 'farmer', 'sourceProduceLot', 'purchaseOrder']);
        $weightment->load(['gateEntryItem', 'originalWeightment', 'resolver', 'creator']);

        return view('pages.fpo-weightment-slip', compact('entry', 'weightment', 'user'));
    }
}
