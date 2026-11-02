<?php

namespace App\Http\Controllers;

use App\Models\FpoGateEntry;
use App\Models\FpoGateEntryItem;
use App\Models\FpoOperationAuditLog;
use App\Models\FpoOperationalException;
use App\Models\FpoQualityApproval;
use App\Models\FpoWeightment;
use App\Notifications\QualityDecisionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FpoApprovalController extends Controller
{
    /**
     * Authorized quality approval, partial acceptance, or rejection sign-off.
     */
    public function approve(Request $request, FpoGateEntry $entry)
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        $this->authorize('approveQuality', $entry);

        $validated = $request->validate([
            'decision' => 'required|string|in:approved,rejected,partially_accepted,quarantined',
            'assigned_grade' => 'nullable|string|in:Good,Intermediate,Bad,Grade A,Grade B,Commercial Grade',
            'accepted_quantity' => 'nullable|numeric|min:0|max:1000000',
            'rejected_quantity' => 'nullable|numeric|min:0|max:1000000',
            'quarantined_quantity' => 'nullable|numeric|min:0|max:1000000',
            'notes' => 'nullable|string|max:1000',
            'rejection_reason' => 'nullable|required_if:decision,rejected|string|max:1000',
            'signature_attestation' => 'nullable|string|max:500',
        ]);

        // Discrepancy check: If an unresolved weight discrepancy exception exists, block approval
        $unresolvedDiscrepancy = $entry->exceptions()
            ->where('exception_type', FpoOperationalException::TYPE_WEIGHT_DISCREPANCY)
            ->whereIn('status', ['open', 'under_review'])
            ->exists();

        if ($unresolvedDiscrepancy && $validated['decision'] === FpoQualityApproval::DECISION_APPROVED) {
            throw ValidationException::withMessages([
                'decision' => 'Cannot approve delivery with an open weight discrepancy. The discrepancy must be resolved or overridden by an authorized officer first.',
            ]);
        }

        // Measured net weight in kg
        $acceptedWeightment = $entry->acceptedWeightment;
        $totalMeasuredKg = $acceptedWeightment
            ? FpoWeightment::convertToKg((float) $acceptedWeightment->net_weight, $acceptedWeightment->unit)
            : FpoWeightment::convertToKg((float) $entry->declared_quantity, $entry->declared_unit);

        $decision = $validated['decision'];
        $acceptedKg = 0.0;
        $rejectedKg = 0.0;
        $quarantinedKg = 0.0;

        if ($decision === FpoQualityApproval::DECISION_APPROVED) {
            $acceptedKg = $totalMeasuredKg;
            $rejectedKg = 0.0;
        } elseif ($decision === FpoQualityApproval::DECISION_REJECTED) {
            $acceptedKg = 0.0;
            $rejectedKg = $totalMeasuredKg;
        } elseif ($decision === FpoQualityApproval::DECISION_QUARANTINED) {
            $acceptedKg = 0.0;
            $quarantinedKg = $totalMeasuredKg;
        } elseif ($decision === FpoQualityApproval::DECISION_PARTIALLY_ACCEPTED) {
            $acceptedKg = (float) ($validated['accepted_quantity'] ?? 0);
            $rejectedKg = (float) ($validated['rejected_quantity'] ?? 0);
            $quarantinedKg = (float) ($validated['quarantined_quantity'] ?? 0);

            if (($acceptedKg + $rejectedKg + $quarantinedKg) <= 0) {
                throw ValidationException::withMessages([
                    'accepted_quantity' => 'For partial acceptance, please specify the accepted and rejected quantities.',
                ]);
            }
        }

        $approvalNumber = 'APR-'.date('Ymd').'-'.rand(1000, 9999);
        $userRoleInFpo = $user->fpoMemberships()
            ->where('fpo_organization_id', $entry->fpo_organization_id)
            ->value('role_in_fpo') ?? ($user->isAdmin() ? 'Platform Administrator' : 'Quality Officer');

        $signatureText = $validated['signature_attestation']
            ?? "Digitally attested and authorized by {$user->name} ({$userRoleInFpo}) on ".now()->toDayDateTimeString();

        $approval = DB::transaction(function () use (
            $entry,
            $decision,
            $approvalNumber,
            $validated,
            $acceptedKg,
            $rejectedKg,
            $quarantinedKg,
            $userRoleInFpo,
            $signatureText,
            $user
        ) {
            $record = FpoQualityApproval::create([
                'approval_number' => $approvalNumber,
                'fpo_gate_entry_id' => $entry->id,
                'fpo_gate_entry_item_id' => null,
                'approver_id' => $user->id,
                'approver_name' => $user->name,
                'approver_role' => $userRoleInFpo,
                'decision' => $decision,
                'decision_at' => now(),
                'accepted_quantity' => $acceptedKg,
                'rejected_quantity' => $rejectedKg,
                'quarantined_quantity' => $quarantinedKg,
                'assigned_grade' => $validated['assigned_grade'] ?? 'Good',
                'notes' => $validated['notes'] ?? null,
                'rejection_reason' => $validated['rejection_reason'] ?? null,
                'signature_attestation' => $signatureText,
            ]);

            // Update Gate Entry Status
            $entryStatus = match ($decision) {
                FpoQualityApproval::DECISION_APPROVED => FpoGateEntry::STATUS_APPROVED,
                FpoQualityApproval::DECISION_REJECTED => FpoGateEntry::STATUS_REJECTED,
                FpoQualityApproval::DECISION_PARTIALLY_ACCEPTED => FpoGateEntry::STATUS_PARTIALLY_ACCEPTED,
                FpoQualityApproval::DECISION_QUARANTINED => FpoGateEntry::STATUS_QUARANTINED,
            };

            $entry->status = $entryStatus;
            $entry->save();

            // Update Line Items
            foreach ($entry->items as $item) {
                $item->update([
                    'accepted_quantity' => $acceptedKg,
                    'rejected_quantity' => $rejectedKg,
                    'quarantined_quantity' => $quarantinedKg,
                    'assigned_grade' => $record->assigned_grade,
                    'status' => $entryStatus,
                ]);
            }

            // If rejected, create an operational exception for audit & tracking
            if ($decision === FpoQualityApproval::DECISION_REJECTED) {
                FpoOperationalException::create([
                    'exception_number' => 'EXC-'.date('Ymd').'-'.rand(1000, 9999),
                    'fpo_organization_id' => $entry->fpo_organization_id,
                    'fpo_gate_entry_id' => $entry->id,
                    'exception_type' => FpoOperationalException::TYPE_QUALITY_FAILURE,
                    'severity' => FpoOperationalException::SEVERITY_CRITICAL,
                    'title' => "Delivery #{$entry->entry_number} rejected on quality inspection",
                    'description' => "Produce failed inspection criteria. Rejection Reason: {$record->rejection_reason}",
                    'status' => FpoOperationalException::STATUS_OPEN,
                    'created_by' => $user->id,
                ]);
            }

            FpoOperationAuditLog::log(
                fpoOrgId: $entry->fpo_organization_id,
                gateEntryId: $entry->id,
                entityType: 'approval',
                entityId: $record->id,
                action: 'approved',
                actor: $user,
                fromState: FpoGateEntry::STATUS_QUALITY_PENDING_APPROVAL,
                toState: $entryStatus,
                reason: "Quality sign-off: {$decision}. Accepted: {$acceptedKg} kg, Rejected: {$rejectedKg} kg. Grade: {$record->assigned_grade}.",
                metadata: [
                    'signature_attestation' => $signatureText,
                ]
            );

            return $record;
        });

        // Notifications
        if ($entry->farmer) {
            $entry->farmer->notify(new QualityDecisionNotification($entry, $approval));
        }

        return redirect()->route('fpo.gate-entries.show', $entry->id)
            ->with('success', "Quality decision recorded: {$approval->decision} (Ref: #{$approval->approval_number}).");
    }
}
