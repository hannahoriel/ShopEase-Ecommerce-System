<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller\Seller;
use App\Services\SellerWalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index(Request $request, SellerWalletService $wallet)
    {
        $seller = Seller::query()->where('user_id', $request->user()->id)->firstOrFail();
        $wallet->sync($seller);

        $filters = [
            'search' => (string) $request->query('search', ''),
            'type' => (string) $request->query('type', 'all'),
            'from' => (string) $request->query('from', ''),
            'to' => (string) $request->query('to', ''),
        ];

        return view('pages.seller.wallet', [
            'seller' => $seller,
            'wallet' => $wallet->summary($seller, $filters),
            'filters' => $filters,
        ]);
    }
}
