<?php

namespace App\Http\Controllers\Api\Rider\Homepage\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use App\Models\Rider\AttendanceRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ClockInController extends Controller
{
    public function __invoke(Request $request, RiderOperationsService $operations): JsonResponse
    {
        $rider = $operations->rider($request->user());
        $validated = $request->validate([
            'photo' => ['required', 'image', 'max:10240'],
        ]);
        $now = now('Asia/Manila');
        $photoPath = $operations->storePrivatePhoto('rider-attendance', $validated['photo']);

        try {
            $record = DB::transaction(function () use ($rider, $now, $photoPath): AttendanceRecord {
                $record = AttendanceRecord::query()
                    ->where('rider_id', $rider->id)
                    ->whereDate('attendance_date', $now->toDateString())
                    ->lockForUpdate()
                    ->first();

                if ($record?->clock_in_at) {
                    throw ValidationException::withMessages([
                        'attendance' => ['You have already clocked in for today.'],
                    ]);
                }

                return AttendanceRecord::updateOrCreate(
                    ['rider_id' => $rider->id, 'attendance_date' => $now->toDateString()],
                    [
                        'clock_in_at' => $now,
                        'clock_in_photo' => $photoPath,
                        'status' => $now->format('H:i:s') > '08:15:00' ? 'late' : 'present',
                    ],
                );
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($photoPath);
            throw $exception;
        }

        return response()->json(['data' => $record], 201);
    }
}
