<?php

namespace App\Http\Controllers;

use App\Models\FpoGateEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FpoReportController extends Controller
{
    /**
     * Show comprehensive individual gate entry formal report.
     */
    public function show(FpoGateEntry $entry)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $this->authorize('exportReport', $entry);

        $entry->load([
            'fpoOrganization',
            'farmer',
            'creator',
            'purchaseOrder',
            'sourceProduceLot',
            'collectiveListing',
            'items.produceLot',
            'items.weightments',
            'items.samples.qualityTests',
            'items.approvals',
            'weightments.originalWeightment',
            'weightments.resolver',
            'weightments.creator',
            'samples.qualityTests.tester',
            'samples.collector',
            'approvals.approver',
            'inventoryPostings.poster',
            'gateExits.authorizer',
            'exceptions.creator',
            'exceptions.resolver',
            'auditLogs.actor',
        ]);

        $reportNumber = 'REP-'.date('Ymd').'-'.str_pad($entry->id, 5, '0', STR_PAD_LEFT);
        $generatedAt = now();

        return view('pages.fpo-gate-entry-report', compact('entry', 'reportNumber', 'generatedAt', 'user'));
    }

    /**
     * Server-side PDF export of individual gate entry report.
     */
    public function downloadPdf(FpoGateEntry $entry)
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        $this->authorize('exportReport', $entry);

        $entry->load([
            'fpoOrganization',
            'farmer',
            'creator',
            'purchaseOrder',
            'sourceProduceLot',
            'collectiveListing',
            'items.produceLot',
            'items.weightments',
            'items.samples.qualityTests',
            'items.approvals',
            'weightments.originalWeightment',
            'weightments.resolver',
            'samples.qualityTests.tester',
            'samples.collector',
            'approvals.approver',
            'inventoryPostings.poster',
            'gateExits.authorizer',
            'exceptions.creator',
            'exceptions.resolver',
            'auditLogs.actor',
        ]);

        $reportNumber = 'REP-'.date('Ymd').'-'.str_pad($entry->id, 5, '0', STR_PAD_LEFT);
        $generatedAt = now();

        try {
            $pdf = Pdf::loadView('pages.fpo-gate-entry-pdf', compact('entry', 'reportNumber', 'generatedAt', 'user'))
                ->setPaper('a4', 'portrait')
                ->setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

            return $pdf->download("Gate_Entry_Report_{$entry->entry_number}.pdf");
        } catch (\Throwable $e) {
            // Graceful fallback to print-friendly web view if PDF rendering environment encounters any issue
            return redirect()->route('fpo.reports.show', $entry->id)
                ->with('info', 'Serving print-optimized formal report view.');
        }
    }

    /**
     * Standalone print-optimized web view.
     */
    public function printView(FpoGateEntry $entry)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $this->authorize('exportReport', $entry);

        $entry->load([
            'fpoOrganization',
            'farmer',
            'creator',
            'purchaseOrder',
            'sourceProduceLot',
            'collectiveListing',
            'items.produceLot',
            'weightments',
            'samples.qualityTests.tester',
            'approvals.approver',
            'inventoryPostings',
            'gateExits',
            'exceptions',
            'auditLogs.actor',
        ]);

        $reportNumber = 'REP-'.date('Ymd').'-'.str_pad($entry->id, 5, '0', STR_PAD_LEFT);
        $generatedAt = now();

        return view('pages.fpo-gate-entry-print', compact('entry', 'reportNumber', 'generatedAt', 'user'));
    }
}
