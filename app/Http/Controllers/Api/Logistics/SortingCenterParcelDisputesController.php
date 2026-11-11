<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Admin\Shipment;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\SortingCenterParcelIssue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SortingCenterParcelDisputesController extends Controller
{
    private function center(Request $request): LogisticsBranch
    {
        return LogisticsBranch::query()->where('user_id', $request->user()->id)->firstOrFail();
    }

    public function index(Request $request): JsonResponse
    {
        $center = $this->center($request);
        $reports = SortingCenterParcelIssue::query()
            ->where('sorting_center_id', $center->id)
            ->latest('created_at')
            ->get()
            ->map(fn (SortingCenterParcelIssue $report): array => $this->payload($report));

        return response()->json([
            'data' => [
                'center' => ['id' => $center->id, 'name' => $center->name],
                'reports' => $reports,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tracking' => ['required', 'string', 'max:90'],
            'type' => ['required', 'string', Rule::in(['Lost Parcel', 'Damaged Parcel'])],
            'description' => ['required', 'string', 'min:1', 'max:1500'],
            'evidence' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        $center = $this->center($request);
        $evidence = $request->file('evidence');
        $evidencePath = $evidence?->store('sorting-center/issues');
        $recordedAt = now();
        $shipment = Shipment::query()
            ->with('order')
            ->where('tracking_number', trim($data['tracking']))
            ->where('logistics_branch_id', $center->id)
            ->first();
        $parcelValue = (float) ($shipment?->order?->total ?? 0);
        $amount = $parcelValue > 0 ? number_format($parcelValue, 2, '.', '') : '';
        $issueLabel = strtolower(str_replace(' Parcel', '', $data['type']));
        $accountability = [
            'party' => 'Logistics Partner',
            'resolution' => 'Full Parcel Value Reimbursement to Seller',
            'parcelAmount' => $amount,
            'compensation' => $amount,
            'notes' => 'Automatically assessed when a '.$issueLabel.' parcel report was recorded.',
            'at' => $recordedAt->toIso8601String(),
            'simulatedReimbursedAt' => $amount !== '' ? $recordedAt->toIso8601String() : null,
            'status' => $amount !== '' ? 'Reimbursed · UI Simulation' : 'Amount Verification Required · Demo',
            'autoVersion' => 3,
        ];

        $report = SortingCenterParcelIssue::query()->create([
            'sorting_center_id' => $center->id,
            'reported_by' => $request->user()->id,
            'tracking_number' => trim($data['tracking']),
            'issue_type' => $data['type'],
            'description' => trim($data['description']),
            'evidence_name' => $evidence?->getClientOriginalName(),
            'evidence_path' => $evidencePath,
            'status' => 'Open',
            'accountability' => $accountability,
        ]);

        return response()->json([
            'message' => 'Parcel issue report saved.',
            'data' => ['report' => $this->payload($report)],
        ], 201);
    }

    private function payload(SortingCenterParcelIssue $report): array
    {
        return [
            'id' => (string) $report->id,
            'tracking' => $report->tracking_number,
            'type' => $report->issue_type,
            'description' => $report->description,
            'evidenceName' => $report->evidence_name,
            'status' => $report->status,
            'at' => $report->created_at?->toIso8601String(),
            'centerId' => (string) $report->sorting_center_id,
            'accountability' => $report->accountability,
        ];
    }
}
