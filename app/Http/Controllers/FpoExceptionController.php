<?php

namespace App\Http\Controllers;

use App\Models\FpoGateEntry;
use App\Models\FpoMember;
use App\Models\FpoOperationAuditLog;
use App\Models\FpoOperationalException;
use App\Models\FpoOrganization;
use App\Notifications\OperationalExceptionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FpoExceptionController extends Controller
{
    /**
     * Resolve active FPO user and organization context.
     */
    protected function getFpoContext(): array
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        if ($user->isAdmin()) {
            $org = FpoOrganization::first();

            return [$user, $org];
        }

        if (! $user->isFpo()) {
            abort(403, 'Unauthorized');
        }

        $org = $user->primaryFpoOrganization();
        if (! $org) {
            $org = FpoOrganization::firstOrCreate(
                ['slug' => 'saurashtra-kisan-producer-co'],
                [
                    'name' => 'Saurashtra Kisan Producer Company Ltd',
                    'organization_type' => 'Farmer Producer Company (FPC)',
                    'registration_number' => 'U01100GJ2023PTC140221',
                    'district' => 'Rajkot',
                    'state' => 'Gujarat',
                    'pincode' => '360311',
                    'authorized_representative_name' => $user->name,
                    'contact_email' => $user->email,
                    'contact_phone' => '9876543250',
                    'verification_status' => FpoOrganization::STATUS_APPROVED,
                    'verified_at' => now(),
                ]
            );

            FpoMember::firstOrCreate(
                ['fpo_organization_id' => $org->id, 'user_id' => $user->id],
                ['role_in_fpo' => 'admin', 'is_primary' => true]
            );
        }

        return [$user, $org];
    }

    /**
     * Display operational exceptions register.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        [$currentUser, $organization] = $this->getFpoContext();

        $query = FpoOperationalException::with(['fpoOrganization', 'gateEntry', 'creator', 'resolver', 'assignee'])
            ->orderBy('id', 'desc');

        if (! $user->isAdmin() && $organization) {
            $query->where('fpo_organization_id', $organization->id);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->input('severity'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('exception_type', $request->input('type'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('exception_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $exceptions = $query->paginate(12)->withQueryString();

        $stats = [
            'total' => (clone $query)->count(),
            'open' => (clone $query)->whereIn('status', [FpoOperationalException::STATUS_OPEN, FpoOperationalException::STATUS_UNDER_REVIEW])->count(),
            'critical' => (clone $query)->where('severity', FpoOperationalException::SEVERITY_CRITICAL)->where('status', 'open')->count(),
            'resolved' => (clone $query)->where('status', FpoOperationalException::STATUS_RESOLVED)->count(),
        ];

        return view('pages.fpo-exceptions-index', compact('exceptions', 'stats', 'organization', 'user'));
    }

    /**
     * Create manual operational exception.
     */
    public function store(Request $request, ?FpoGateEntry $entry = null)
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        [$currentUser, $organization] = $this->getFpoContext();

        $validated = $request->validate([
            'fpo_gate_entry_id' => 'nullable|exists:fpo_gate_entries,id',
            'exception_type' => 'required|string|in:weight_discrepancy,quality_failure,missing_sample,stock_mismatch,quantity_mismatch,unauthorized_exit',
            'severity' => 'required|string|in:low,medium,critical',
            'title' => 'required|string|max:150',
            'description' => 'required|string|max:1000',
        ]);

        $gateEntryId = $validated['fpo_gate_entry_id'] ?? $entry?->id;
        $exceptionNumber = 'EXC-'.date('Ymd').'-'.rand(1000, 9999);

        $exception = DB::transaction(function () use ($organization, $gateEntryId, $validated, $exceptionNumber, $user) {
            $exception = FpoOperationalException::create([
                'exception_number' => $exceptionNumber,
                'fpo_organization_id' => $organization->id,
                'fpo_gate_entry_id' => $gateEntryId,
                'exception_type' => $validated['exception_type'],
                'severity' => $validated['severity'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'status' => FpoOperationalException::STATUS_OPEN,
                'created_by' => $user->id,
            ]);

            FpoOperationAuditLog::log(
                fpoOrgId: $organization->id,
                gateEntryId: $gateEntryId,
                entityType: 'exception',
                entityId: $exception->id,
                action: 'created',
                actor: $user,
                fromState: null,
                toState: FpoOperationalException::STATUS_OPEN,
                reason: "Operational exception raised: {$exception->title} ({$exception->severity})"
            );

            return $exception;
        });

        return redirect()->back()
            ->with('success', "Exception #{$exception->exception_number} has been logged.");
    }

    /**
     * Resolve exception with documented action taken, reason, and actor.
     */
    public function resolve(Request $request, FpoOperationalException $exception)
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        $validated = $request->validate([
            'resolution_action' => 'required|string|max:500',
            'resolution_reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($exception, $validated, $user) {
            $exception->update([
                'status' => FpoOperationalException::STATUS_RESOLVED,
                'resolution_action' => $validated['resolution_action'],
                'resolution_reason' => $validated['resolution_reason'],
                'resolved_by' => $user->id,
                'resolved_at' => now(),
            ]);

            FpoOperationAuditLog::log(
                fpoOrgId: $exception->fpo_organization_id,
                gateEntryId: $exception->fpo_gate_entry_id,
                entityType: 'exception',
                entityId: $exception->id,
                action: 'resolved',
                actor: $user,
                fromState: FpoOperationalException::STATUS_OPEN,
                toState: FpoOperationalException::STATUS_RESOLVED,
                reason: "Exception resolved: Action: {$validated['resolution_action']}. Reason: {$validated['resolution_reason']}."
            );
        });

        return redirect()->back()
            ->with('success', "Exception #{$exception->exception_number} marked as resolved.");
    }
}
