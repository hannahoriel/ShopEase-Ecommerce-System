<?php
namespace App\Http\Controllers\Api\Buyer;
use App\Http\Controllers\Controller;
use App\Services\BuyerWalletService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
class WalletController extends Controller
{
 public function show(Request $request, BuyerWalletService $service): JsonResponse
 {
  $wallet = $service->account((int)$request->user()->id);
  $history = DB::table('buyer_wallet_transactions')->where('wallet_id',$wallet->id)
     ->orderByDesc('id')->limit(100)->get(['id','type','amount','balance_after','description','reference_type','reference_id','created_at']);
  return response()->json(['balance'=>(float)$wallet->balance,'currency'=>'PHP','transactions'=>$history]);
 }
}
