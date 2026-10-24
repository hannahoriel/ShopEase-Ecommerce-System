<?php

namespace App\Http\Controllers\Api\Rider\User\Support;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListSupportTicketsController extends Controller
{
    public function __invoke(Request $request, RiderOperationsService $operations): JsonResponse
    {
        $rider = $operations->rider($request->user());
        $query = $rider->supportTickets()->latest();
        if ($request->boolean('archived')) {
            $query->whereNotNull('archived_at');
        } else {
            $query->whereNull('archived_at');
        }

        return response()->json(['data' => $query->paginate($request->integer('per_page', 30))]);
    }
}
