<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\SortingCenterParcelIssue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $report = DB::transaction(function () use ($request, $center, $data, $evidence, $evidencePath): SortingCenterParcelIssue {
            return SortingCenterParcelIssue::query()->create([
                'sorting_center_id' => $center->id,
                'reported_by' => $request->user()->id,
                'tracking_number' => trim($data['tracking']),
                'issue_type' => $data['type'],
                'description' => trim($data['description']),
                'evidence_name' => $evidence?->getClientOriginalName(),
                'evidence_path' => $evidencePath,
                'status' => 'Under Review',
                'accountability' => ['status' => 'Under Review'],
            ]);
        }, 3);

        return response()->json([
            'message' => 'Parcel issue report submitted for admin review. No order cancellation or wallet adjustment was made.',
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
