<?php

namespace App\Http\Controllers;

use App\Models\FpoGateEntry;
use App\Models\FpoGateExit;
use App\Models\FpoInventoryPosting;
use App\Models\FpoOperationAuditLog;
use App\Models\FpoOperationalException;
use App\Models\FpoOrganization;
use App\Models\FpoQualityApproval;
use App\Models\FpoSample;
use App\Models\FpoWeightment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminGateOperationsController extends Controller
{
    /**
     * Cross-organization administrative oversight dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Unauthorized. Platform Admin privileges required.');
        }

        $fpos = FpoOrganization::all();

        $query = FpoGateEntry::with(['fpoOrganization', 'farmer', 'sourceProduceLot', 'acceptedWeightment', 'latestApproval'])
            ->orderBy('id', 'desc');

        if ($request->filled('fpo_id')) {
            $query->where('fpo_organization_id', $request->input('fpo_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('entry_number', 'like', "%{$search}%")
                    ->orWhere('vehicle_number', 'like', "%{$search}%")
                    ->orWhere('delivering_party_name', 'like', "%{$search}%");
            });
        }

        $entries = $query->paginate(15)->withQueryString();

        $selectedFpoId = $request->input('fpo_id');
        $baseEntryQuery = FpoGateEntry::when($selectedFpoId, fn ($q) => $q->where('fpo_organization_id', $selectedFpoId));
        $baseExceptionQuery = FpoOperationalException::when($selectedFpoId, fn ($q) => $q->where('fpo_organization_id', $selectedFpoId));

        $stats = [
            'total_fpos' => $fpos->count(),
            'total_gate_entries' => (clone $baseEntryQuery)->count(),
            'entries_today' => (clone $baseEntryQuery)->whereDate('entry_date_time', now()->toDateString())->count(),
            'open_weight_discrepancies' => (clone $baseExceptionQuery)->where('exception_type', FpoOperationalException::TYPE_WEIGHT_DISCREPANCY)->where('status', 'open')->count(),
            'pending_samples' => (clone $baseEntryQuery)->whereIn('status', [FpoGateEntry::STATUS_WEIGHED, FpoGateEntry::STATUS_SAMPLING_PENDING])->count(),
            'pending_approvals' => (clone $baseEntryQuery)->where('status', FpoGateEntry::STATUS_QUALITY_PENDING_APPROVAL)->count(),
            'rejected_deliveries' => (clone $baseEntryQuery)->where('status', FpoGateEntry::STATUS_REJECTED)->count(),
            'quarantined_deliveries' => (clone $baseEntryQuery)->where('status', FpoGateEntry::STATUS_QUARANTINED)->count(),
            'posted_inventory_count' => FpoInventoryPosting::when($selectedFpoId, fn ($q) => $q->where('fpo_organization_id', $selectedFpoId))->where('status', 'posted')->count(),
            'reversed_postings_count' => FpoInventoryPosting::when($selectedFpoId, fn ($q) => $q->where('fpo_organization_id', $selectedFpoId))->where('status', 'reversed')->count(),
            'exits_awaiting_authorization' => FpoGateExit::when($selectedFpoId, fn ($q) => $q->where('fpo_organization_id', $selectedFpoId))->where('status', FpoGateExit::STATUS_AUTHORIZED)->count(),
            'total_open_exceptions' => (clone $baseExceptionQuery)->whereIn('status', ['open', 'under_review'])->count(),
        ];

        // Recent audit trail across organizations
        $recentAuditLogs = FpoOperationAuditLog::with(['fpoOrganization', 'actor', 'gateEntry'])
            ->when($selectedFpoId, fn ($q) => $q->where('fpo_organization_id', $selectedFpoId))
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        return view('pages.admin-gate-operations', compact('entries', 'stats', 'fpos', 'recentAuditLogs', 'user'));
    }
}
