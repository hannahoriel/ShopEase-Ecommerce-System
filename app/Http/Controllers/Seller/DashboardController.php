<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Api\Seller\DashboardController as ApiDashboardController;
use Illuminate\Http\Request;

class DashboardController extends ApiDashboardController
{
    public function index(Request $request)
    {
        $dashboard = $this->dashboardData($request->user());

        return view('pages.seller.dashboard', compact('dashboard'));
    }
}
