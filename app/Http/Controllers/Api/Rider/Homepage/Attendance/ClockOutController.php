<?php

namespace App\Http\Controllers\Api\Rider\Homepage\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ClockOutController extends Controller
{
    public function __invoke(Request $request, RiderOperationsService $operations): JsonResponse
    {
        $rider = $operations->rider($request->user());
        $validated = $request->validate([
            'photo' => ['required', 'image', 'max:10240'],
        ]);
        $now = now('Asia/Manila');
        $record = $rider->attendanceRecords()
            ->whereDate('attendance_date', $now->toDateString())
            ->whereNotNull('clock_in_at')
            ->whereNull('clock_out_at')
            ->first();

        if (! $record) {
            throw ValidationException::withMessages([
                'attendance' => ['Clock in before clocking out.'],
            ]);
        }

        $photoPath = $operations->storePrivatePhoto('rider-attendance', $validated['photo']);
        try {
            $record->update(['clock_out_at' => $now, 'clock_out_photo' => $photoPath]);
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($photoPath);
            throw $exception;
        }

        return response()->json(['data' => $record->fresh()]);
    }
}
