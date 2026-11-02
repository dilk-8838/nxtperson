<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Gate Entry Report — {{ $entry->entry_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            margin: 20px;
        }
        .header-table, .data-table, .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .header-table td {
            vertical-align: top;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #065f46;
        }
        .subtitle {
            font-size: 11px;
            color: #334155;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 3px 8px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 4px;
            display: inline-block;
        }
        .badge-approved { background-color: #d1fae5; color: #065f46; border-color: #a7f3d0; }
        .badge-rejected { background-color: #ffe4e6; color: #9f1239; border-color: #fecdd3; }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: #475569;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin-bottom: 8px;
            margin-top: 10px;
        }
        .data-table th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 9px;
            text-transform: uppercase;
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
            font-weight: bold;
        }
        .data-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .highlight-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 12px;
        }
        .footer {
            border-top: 2px solid #0f172a;
            padding-top: 8px;
            font-size: 9px;
            color: #64748b;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    {{-- 1. Branding & Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="title">NxtPerson Agritech</div>
                <div class="subtitle">{{ $entry->fpoOrganization->name }}</div>
                <div style="font-size: 10px; color: #64748b;">
                    {{ $entry->fpoOrganization->registered_address ?? 'APMC Compound' }}, {{ $entry->fpoOrganization->district }}, Gujarat<br>
                    Registration: {{ $entry->fpoOrganization->registration_number ?? 'U01100GJ2023PTC140221' }} | Phone: {{ $entry->fpoOrganization->contact_phone ?? 'N/A' }}
                </div>
            </td>
            <td style="width: 40%;" class="text-right">
                <span class="badge">OFFICIAL GATE PROCUREMENT REPORT</span>
                <div style="font-size: 14px; font-weight: bold; margin-top: 5px;">{{ $entry->entry_number }}</div>
                <div style="font-size: 9px; color: #64748b;">Report ID: {{ $reportNumber }}</div>
                <div style="font-size: 9px; color: #64748b;">Generated: {{ $generatedAt->format('d M Y, h:i A') }}</div>
            </td>
        </tr>
    </table>

    {{-- 2. Delivery & Vehicle Summary --}}
    <div class="highlight-box">
        <table class="header-table" style="margin-bottom: 0;">
            <tr>
                <td style="width: 25%;">
                    <strong>Date & Time:</strong><br>
                    {{ $entry->entry_date_time->format('d/m/Y h:i A') }}
                </td>
                <td style="width: 25%;">
                    <strong>Gate Point:</strong><br>
                    {{ $entry->gate_point }}
                </td>
                <td style="width: 25%;">
                    <strong>Vehicle Number:</strong><br>
                    {{ $entry->vehicle_number ?? 'Walk-in / Cart' }}
                </td>
                <td style="width: 25%;">
                    <strong>Status:</strong><br>
                    <span class="badge badge-approved">{{ $entry->getStatusLabel() }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding-top: 6px;">
                    <strong>Delivering Party:</strong><br>
                    {{ $entry->delivering_party_name }}
                </td>
                <td style="padding-top: 6px;">
                    <strong>Party Type:</strong><br>
                    {{ ucfirst(str_replace('_', ' ', $entry->delivering_party_type)) }}
                </td>
                <td style="padding-top: 6px;">
                    <strong>Driver Name:</strong><br>
                    {{ $entry->driver_name ?? 'N/A' }}
                </td>
                <td style="padding-top: 6px;">
                    <strong>Registered By:</strong><br>
                    {{ $entry->creator->name ?? 'FPO Staff' }}
                </td>
            </tr>
        </table>
    </div>

    {{-- 3. Produce Details & Line Items --}}
    <div class="section-title">Produce Line Items & Declared Quantities</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Item Ref</th>
                <th>Produce / Crop</th>
                <th>Variety</th>
                <th>Declared Quantity</th>
                <th>Measured Net</th>
                <th>Source Lot / Traceability</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entry->items as $item)
                <tr>
                    <td>{{ $item->item_number }}</td>
                    <td><strong>{{ $item->crop_name }}</strong></td>
                    <td>{{ $item->variety ?? 'Standard' }}</td>
                    <td>{{ number_format((float)$item->declared_quantity, 1) }} {{ $item->declared_unit }}</td>
                    <td><strong>{{ $item->measured_net_quantity ? number_format((float)$item->measured_net_quantity, 1).' kg' : 'Pending' }}</strong></td>
                    <td>
                        @if($entry->sourceProduceLot)
                            Lot #{{ $entry->sourceProduceLot->lot_number }}
                        @elseif($entry->purchaseOrder)
                            PO #{{ $entry->purchaseOrder->po_number }}
                        @else
                            Direct Inbound
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 4. Weighbridge Measurements --}}
    <div class="section-title">Weighbridge Measurements & Net Weight</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Slip Ref</th>
                <th>Gross Weight</th>
                <th>Tare Weight</th>
                <th>Net Weight</th>
                <th>Method</th>
                <th>Operator</th>
                <th>Discrepancy</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entry->weightments as $wm)
                <tr>
                    <td>{{ $wm->weightment_number }}</td>
                    <td>{{ number_format((float)$wm->gross_weight, 1) }} {{ $wm->unit }}</td>
                    <td>{{ number_format((float)$wm->tare_weight, 1) }} {{ $wm->unit }}</td>
                    <td><strong>{{ number_format((float)$wm->net_weight, 1) }} {{ $wm->unit }}</strong></td>
                    <td>{{ ucfirst(str_replace('_', ' ', $wm->measurement_method)) }}</td>
                    <td>{{ $wm->operator_name }}</td>
                    <td>
                        @if($wm->discrepancy_flag)
                            {{ $wm->discrepancy_percentage }}% variance ({{ $wm->discrepancy_resolved ? 'Resolved' : 'Open' }})
                        @else
                            Within Tolerance
                        @endif
                    </td>
                    <td>{{ $wm->is_accepted ? 'Accepted' : 'Superseded' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No weightment recorded.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 5. Quality Laboratory Inspection --}}
    <div class="section-title">Quality Testing & Laboratory Parameters</div>
    @php
        $allTests = \App\Models\FpoQualityTest::whereHas('sample', fn($q) => $q->where('fpo_gate_entry_id', $entry->id))->get();
    @endphp
    <table class="data-table">
        <thead>
            <tr>
                <th>Test Ref</th>
                <th>Parameter Name</th>
                <th>Measured Value</th>
                <th>Acceptable Limit</th>
                <th>Outcome</th>
                <th>Tester</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            @forelse($allTests as $test)
                <tr>
                    <td>{{ $test->test_number }}</td>
                    <td><strong>{{ $test->parameter_name }}</strong></td>
                    <td><strong>{{ $test->measured_value }} {{ $test->unit }}</strong></td>
                    <td>{{ $test->max_acceptable_value ? 'Max '.$test->max_acceptable_value.' '.$test->unit : 'Standard' }}</td>
                    <td>{{ strtoupper($test->outcome) }}</td>
                    <td>{{ $test->tester_name }}</td>
                    <td>{{ $test->tested_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No quality tests recorded.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 6. Quality Approval & Sign-Off Attestation --}}
    <div class="section-title">Authorized Quality Decision & Commercial Sign-off</div>
    @if($entry->latestApproval)
        <div class="highlight-box">
            <table class="header-table" style="margin-bottom: 0;">
                <tr>
                    <td style="width: 25%;">
                        <strong>Decision:</strong><br>
                        <span class="badge {{ $entry->latestApproval->decision === 'approved' ? 'badge-approved' : 'badge-rejected' }}">
                            {{ strtoupper($entry->latestApproval->decision) }}
                        </span>
                    </td>
                    <td style="width: 25%;">
                        <strong>Commercial Grade:</strong><br>
                        {{ $entry->latestApproval->assigned_grade ?? 'Good' }}
                    </td>
                    <td style="width: 25%;">
                        <strong>Accepted Qty:</strong><br>
                        <strong>{{ number_format((float)$entry->latestApproval->accepted_quantity, 1) }} kg</strong>
                    </td>
                    <td style="width: 25%;">
                        <strong>Rejected Qty:</strong><br>
                        {{ number_format((float)$entry->latestApproval->rejected_quantity, 1) }} kg
                    </td>
                </tr>
            </table>
            <div style="font-size: 9px; margin-top: 6px; color: #475569;">
                <strong>Approver:</strong> {{ $entry->latestApproval->approver_name }} ({{ $entry->latestApproval->approver_role }}) on {{ $entry->latestApproval->decision_at->format('d M Y, h:i A') }}<br>
                <strong>Attestation:</strong> {{ $entry->latestApproval->signature_attestation }}
            </div>
        </div>
    @else
        <div class="highlight-box">Quality approval pending authorized officer review.</div>
    @endif

    {{-- 7. Inventory Posting & Gate Exit Summary --}}
    <table class="header-table">
        <tr>
            <td style="width: 50%; padding-right: 8px;">
                <div class="section-title">Inventory Posting Status</div>
                @if($entry->inventoryPostings->where('status', 'posted')->count() > 0)
                    @php $p = $entry->inventoryPostings->where('status', 'posted')->first(); @endphp
                    <div style="font-size: 10px;">
                        <strong>Posting Ref:</strong> #{{ $p->posting_number }}<br>
                        <strong>Quantity Posted:</strong> {{ number_format((float)$p->posted_quantity_kg, 1) }} kg<br>
                        <strong>Stock After:</strong> {{ number_format((float)$p->source_stock_after_kg, 1) }} kg<br>
                        <strong>Posted Date:</strong> {{ $p->posted_at->format('d M Y, h:i A') }}
                    </div>
                @else
                    <div style="font-size: 10px; color: #64748b;">Not yet posted to live marketplace inventory.</div>
                @endif
            </td>
            <td style="width: 50%; padding-left: 8px;">
                <div class="section-title">Gate Exit & Dispatch Details</div>
                @if($entry->latestExit)
                    <div style="font-size: 10px;">
                        <strong>Exit Pass Ref:</strong> #{{ $entry->latestExit->exit_number }}<br>
                        <strong>Movement:</strong> {{ ucfirst(str_replace('_', ' ', $entry->latestExit->movement_type)) }}<br>
                        <strong>Authorized by:</strong> {{ $entry->latestExit->authorizer_name }}<br>
                        <strong>Actual Departure:</strong> {{ $entry->latestExit->actual_exit_at ? $entry->latestExit->actual_exit_at->format('d M Y, h:i A') : 'Vehicle On-Site' }}
                    </div>
                @else
                    <div style="font-size: 10px; color: #64748b;">Vehicle on-site or completed delivery.</div>
                @endif
            </td>
        </tr>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <table class="header-table" style="margin-bottom: 0;">
            <tr>
                <td>NxtPerson Agritech Quality Procurement System &bull; Report ID: {{ $reportNumber }}</td>
                <td class="text-right">Generated on {{ $generatedAt->format('d M Y, h:i A') }}</td>
            </tr>
        </table>
    </div>

</body>
</html>
