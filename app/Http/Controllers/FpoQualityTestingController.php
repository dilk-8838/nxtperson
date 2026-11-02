<?php

namespace App\Http\Controllers;

use App\Models\FpoGateEntry;
use App\Models\FpoGateEntryItem;
use App\Models\FpoOperationAuditLog;
use App\Models\FpoQualityTest;
use App\Models\FpoSample;
use App\Notifications\QualityApprovalRequestedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class FpoQualityTestingController extends Controller
{
    /**
     * Collect sample for a gate entry or line item.
     */
    public function collectSample(Request $request, FpoGateEntry $entry)
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        $this->authorize('collectSample', $entry);

        $validated = $request->validate([
            'fpo_gate_entry_item_id' => 'nullable|exists:fpo_gate_entry_items,id',
            'sampling_method' => 'required|string|max:100',
            'sample_quantity' => 'required|numeric|min:0.05|max:100',
            'sample_unit' => 'required|string|in:kg,gm,ltr',
            'container_seal_number' => 'nullable|string|max:50',
            'destination' => 'required|string|max:100',
            'condition_observations' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'notes' => 'nullable|string|max:1000',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('fpo_samples', 'local');
        }

        $sampleNumber = 'SMP-'.date('Ymd').'-'.rand(1000, 9999);

        $sample = DB::transaction(function () use ($entry, $validated, $sampleNumber, $attachmentPath, $user) {
            $sample = FpoSample::create([
                'sample_number' => $sampleNumber,
                'fpo_gate_entry_id' => $entry->id,
                'fpo_gate_entry_item_id' => $validated['fpo_gate_entry_item_id'] ?? null,
                'crop_name' => $entry->crop_name,
                'variety' => $entry->variety,
                'sample_date_time' => now(),
                'collected_by' => $user->id,
                'collector_name' => $user->name,
                'sampling_method' => $validated['sampling_method'],
                'sample_quantity' => $validated['sample_quantity'],
                'sample_unit' => $validated['sample_unit'],
                'container_seal_number' => $validated['container_seal_number'] ?? null,
                'condition_observations' => $validated['condition_observations'] ?? null,
                'destination' => $validated['destination'],
                'status' => FpoSample::STATUS_COLLECTED,
                'attachment_path' => $attachmentPath,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Transition entry to testing_in_progress
            $entry->status = FpoGateEntry::STATUS_TESTING_IN_PROGRESS;
            $entry->save();

            FpoOperationAuditLog::log(
                fpoOrgId: $entry->fpo_organization_id,
                gateEntryId: $entry->id,
                entityType: 'sample',
                entityId: $sample->id,
                action: 'sample_collected',
                actor: $user,
                fromState: 'weighed',
                toState: 'testing_in_progress',
                reason: "Sample #{$sample->sample_number} collected ({$sample->sample_quantity} {$sample->sample_unit}). Seal: {$sample->container_seal_number}."
            );

            return $sample;
        });

        return redirect()->route('fpo.gate-entries.show', $entry->id)
            ->with('success', "Sample #{$sample->sample_number} successfully logged. Ready for quality testing.");
    }

    /**
     * Record quality inspection and test parameters.
     */
    public function recordTest(Request $request, FpoGateEntry $entry, FpoSample $sample)
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        $this->authorize('recordQualityTest', $entry);

        $validated = $request->validate([
            'test_type' => 'required|string|in:moisture,foreign_matter,grain_size,damage_spoilage,visual_condition',
            'parameter_name' => 'required|string|max:100',
            'measured_value' => 'required|numeric|min:0|max:100',
            'unit' => 'required|string|max:30',
            'min_acceptable_value' => 'nullable|numeric|min:0|max:100',
            'max_acceptable_value' => 'nullable|numeric|min:0|max:100',
            'outcome' => 'required|string|in:pass,fail,conditional',
            'notes' => 'nullable|string|max:1000',
            'retest_of_id' => 'nullable|exists:fpo_quality_tests,id',
        ]);

        $testNumber = 'QT-'.date('Ymd').'-'.rand(1000, 9999);

        $test = DB::transaction(function () use ($sample, $entry, $validated, $testNumber, $user) {
            // If this is a retest, mark prior test as superseded
            if (! empty($validated['retest_of_id'])) {
                $priorTest = FpoQualityTest::findOrFail($validated['retest_of_id']);
                $priorTest->update(['is_superseded' => true]);
            }

            $test = FpoQualityTest::create([
                'test_number' => $testNumber,
                'fpo_sample_id' => $sample->id,
                'fpo_gate_entry_item_id' => $sample->fpo_gate_entry_item_id,
                'test_type' => $validated['test_type'],
                'parameter_name' => $validated['parameter_name'],
                'measured_value' => $validated['measured_value'],
                'unit' => $validated['unit'],
                'min_acceptable_value' => $validated['min_acceptable_value'] ?? null,
                'max_acceptable_value' => $validated['max_acceptable_value'] ?? null,
                'outcome' => $validated['outcome'],
                'tested_by' => $user->id,
                'tester_name' => $user->name,
                'tested_at' => now(),
                'notes' => $validated['notes'] ?? null,
                'is_superseded' => false,
            ]);

            $sample->update(['status' => FpoSample::STATUS_TESTED]);

            FpoOperationAuditLog::log(
                fpoOrgId: $entry->fpo_organization_id,
                gateEntryId: $entry->id,
                entityType: 'quality_test',
                entityId: $test->id,
                action: 'test_recorded',
                actor: $user,
                reason: "{$test->parameter_name}: {$test->measured_value} {$test->unit} (Outcome: {$test->outcome})."
            );

            return $test;
        });

        return redirect()->route('fpo.gate-entries.show', $entry->id)
            ->with('success', "Quality Test #{$test->test_number} recorded ({$test->parameter_name}: {$test->measured_value} {$test->unit} - {$test->outcome}).");
    }

    /**
     * Submit delivery for authorized quality approval.
     */
    public function submitForApproval(FpoGateEntry $entry)
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        $this->authorize('update', $entry);

        // Ensure at least one test is recorded
        $hasTests = FpoQualityTest::whereHas('sample', fn ($q) => $q->where('fpo_gate_entry_id', $entry->id))->exists();
        if (! $hasTests) {
            throw ValidationException::withMessages([
                'tests' => 'Cannot submit for approval: At least one quality test must be recorded.',
            ]);
        }

        DB::transaction(function () use ($entry, $user) {
            $entry->status = FpoGateEntry::STATUS_QUALITY_PENDING_APPROVAL;
            $entry->save();

            FpoOperationAuditLog::log(
                fpoOrgId: $entry->fpo_organization_id,
                gateEntryId: $entry->id,
                entityType: 'gate_entry',
                entityId: $entry->id,
                action: 'status_changed',
                actor: $user,
                fromState: FpoGateEntry::STATUS_TESTING_IN_PROGRESS,
                toState: FpoGateEntry::STATUS_QUALITY_PENDING_APPROVAL,
                reason: 'Inspection complete. Submitted for authorized quality approval.'
            );
        });

        // Notify authorized FPO managers
        $fpoManagers = $entry->fpoOrganization->fpoMembers()
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter();

        foreach ($fpoManagers as $manager) {
            $manager->notify(new QualityApprovalRequestedNotification($entry));
        }

        return redirect()->route('fpo.gate-entries.show', $entry->id)
            ->with('success', "Gate Entry #{$entry->entry_number} submitted for authorized sign-off.");
    }
}
