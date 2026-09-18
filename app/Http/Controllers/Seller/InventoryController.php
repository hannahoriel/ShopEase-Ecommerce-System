<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Admin\Registration;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        abort_unless(
            $user &&
            $user->role === User::ROLE_SELLER,
            403
        );

        /*
        |--------------------------------------------------------------------------
        | GET SELLER PROFILE
        |--------------------------------------------------------------------------
        */

        $seller = Seller::where(
            'user_id',
            $user->id
        )->first();


        /*
        |--------------------------------------------------------------------------
        | GET REGISTERED CATEGORIES
        |--------------------------------------------------------------------------
        |
        | During seller registration, the selected categories are saved
        | inside the sellers table under "line_of_business".
        |
        */

        $categorySource =
            $seller?->line_of_business;


        /*
        |--------------------------------------------------------------------------
        | FALLBACK
        |--------------------------------------------------------------------------
        |
        | If line_of_business is empty for some reason,
        | get the categories from the registration record.
        |
        */

        if (empty($categorySource)) {

            $categorySource =
                Registration::where(
                    'user_id',
                    $user->id
                )
                ->latest('id')
                ->value(
                    'business_category'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CONVERT CATEGORIES TO ARRAY
        |--------------------------------------------------------------------------
        |
        | Example database value:
        |
        | Electronics and Gadgets, Home and Garden, Pet Supplies
        |
        | Becomes:
        |
        | [
        |     'Electronics and Gadgets',
        |     'Home and Garden',
        |     'Pet Supplies'
        | ]
        |
        */

        $sellerCategories =
            collect(
                explode(
                    ',',
                    (string) $categorySource
                )
            )
            ->map(
                fn ($category) =>
                    trim($category)
            )
            ->filter()
            ->unique()
            ->values()
            ->all();


        /*
        |--------------------------------------------------------------------------
        | INVENTORY PAGE
        |--------------------------------------------------------------------------
        */

        return view(
            'pages.seller.inventory',
            [
                'seller' =>
                    $seller,

                'sellerCategories' =>
                    $sellerCategories,
            ]
        );
    }
}