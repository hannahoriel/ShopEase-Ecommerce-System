<?php

namespace App\Http\Controllers\Api\Rider\Homepage\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListAttendanceController extends Controller
{
    public function __invoke(Request $request, RiderOperationsService $operations): JsonResponse
    {
        $rider = $operations->rider($request->user());
        $today = now('Asia/Manila')->toDateString();

        return response()->json([
            'data' => $rider->attendanceRecords()->latest('attendance_date')->limit(90)->get(),
            'today' => $rider->attendanceRecords()->whereDate('attendance_date', $today)->first(),
        ]);
    }
}
