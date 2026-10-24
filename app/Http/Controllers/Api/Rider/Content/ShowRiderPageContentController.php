<?php

namespace App\Http\Controllers\Api\Rider\Content;

use App\Http\Controllers\Controller;
use App\Http\Services\RiderOperationsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowRiderPageContentController extends Controller
{
    private const CONTENT = [
        'about' => [
            'title' => 'About ShopEase',
            'app_name' => 'ShopEase Rider',
            'version' => '1.0.4',
            'sections' => [
                [
                    'title' => 'Our Mission',
                    'body' => 'ShopEase connects local logistics, riders, and customers seamlessly to deliver parcels efficiently and reliably.',
                ],
                [
                    'title' => 'Terms & Services',
                    'body' => 'By using this application, you agree to adhere to rider compliance policies and localized logistics guidelines.',
                ],
            ],
            'copyright' => '© 2026 ShopEase Logistics Inc. All rights reserved.',
        ],
        'knowledge-base' => [
            'title' => 'Knowledge Base',
            'categories' => [
                [
                    'category' => 'Delivery Protocols',
                    'items' => [
                        [
                            'q' => 'What to do if customer is unreachable?',
                            'a' => 'Attempt to call at least 3 times. Wait for 5-10 minutes at location before marking attempt as Unsuccessful.',
                        ],
                        [
                            'q' => 'Proof of Delivery (POD) Guidelines',
                            'a' => 'Ensure the photo clearly shows the package and parcel label along with the recipient or location threshold.',
                        ],
                    ],
                ],
                [
                    'category' => 'Package Issues',
                    'items' => [
                        [
                            'q' => 'Handling Damaged Goods',
                            'a' => 'Report immediately under Suspected Damage in the app before attempting delivery to customer.',
                        ],
                        [
                            'q' => 'Lost Package Procedures',
                            'a' => 'Notify Hub supervisor and update parcel status to Suspected Lost in your rider portal.',
                        ],
                    ],
                ],
                [
                    'category' => 'App & Payouts',
                    'items' => [
                        [
                            'q' => 'When do earnings appear in my account?',
                            'a' => 'Earnings appear after Logistics posts them. You can review posted entries and remittance status in Money Report; payout timing is managed by your assigned logistics branch.',
                        ],
                        [
                            'q' => 'How does Star Courier scoring work?',
                            'a' => 'The current backend reports completed deliveries and posted earnings. Star points, levels, and scoring bonuses are not configured.',
                        ],
                        [
                            'q' => 'How to update vehicle information?',
                            'a' => 'Go to Edit Profile or contact support tickets to submit vehicle modification requests.',
                        ],
                    ],
                ],
            ],
        ],
        'star-courier-guidelines' => [
            'title' => 'Star Courier Guidelines',
            'overview' => 'Star Courier currently reports verified completed deliveries and earnings posted by Logistics. The backend does not yet calculate points, levels, ratings, bonuses, or penalties.',
            'scoring_configured' => false,
        ],
        'navigation' => [
            'providers' => ['google_maps', 'apple_maps'],
            'default_provider' => 'google_maps',
            'address_query_parameter' => 'destination',
        ],
    ];

    public function __invoke(
        Request $request,
        RiderOperationsService $operations,
        string $page
    ): JsonResponse
    {
        $operations->rider($request->user());
        abort_unless(array_key_exists($page, self::CONTENT), 404, 'Rider content was not found.');

        return response()->json(['data' => self::CONTENT[$page]]);
    }
}
