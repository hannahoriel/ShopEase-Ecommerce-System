<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\Logistics;
use App\Models\Rider\Remittance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ShowRiderRemittanceReceiptController extends Controller
{
    public function __invoke(Request $request, Remittance $remittance)
    {
        $company = Logistics::query()
            ->where('user_id', $request->user()->id)
            ->where('registration_status', 'active')
            ->firstOrFail();
        abort_unless(
            $remittance->rider()->whereHas('logisticsBranch', fn ($branch) => $branch->where('logistics_id', $company->id))->exists(),
            404
        );
        abort_unless($remittance->receipt_path && Storage::disk('local')->exists($remittance->receipt_path), 404);

        return response()->file(Storage::disk('local')->path($remittance->receipt_path));
    }
}
