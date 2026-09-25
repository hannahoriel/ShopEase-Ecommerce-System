<?php

/*
 * config/admin/seller_compliance.php
 * Single source of truth for Seller Compliance labels/options.
 * Used by the Blade views AND passed to JS (no more duplicated lists).
 */

return [

    'default_per_page' => 7,

    'per_page_options' => [7, 10, 20],

    'categories' => [
        'pet-supplies'                   => 'Pet Supplies',
        'electronics-and-gadgets'        => 'Electronics & Gadgets',
        'womens-apparel'                 => 'Women’s Apparel',
        'mens-apparel'                   => 'Men’s Apparel',
        'kids-and-baby'                  => 'Kids & Baby',
        'home-and-garden'                => 'Home & Garden',
        'sports-and-outdoors'            => 'Sports & Outdoors',
        'health-and-beauty'              => 'Health & Beauty',
        'books-and-media'                => 'Books & Media',
        'food-and-gourmet'               => 'Food & Gourmet',
        'automotive-motorcycle'          => 'Automotive & Motorcycle',
        'furniture-and-office-equipment' => 'Furniture & Office Equipment',
        'jewelry-and-watches'            => 'Jewelry & Watches',
        'office-and-school-supplies'     => 'Office & School Supplies',
    ],

    'statuses' => [
        'compliant'    => ['label' => 'Compliant',    'policy' => 'This seller is following the platform policies.'],
        'warning'      => ['label' => 'Warning',      'policy' => 'This seller has compliance items that require attention.'],
        'under-review' => ['label' => 'Under Review', 'policy' => 'This seller has compliance items that require attention.'],
        'suspended'    => ['label' => 'Suspended',    'policy' => 'This seller currently has serious compliance concerns.'],
    ],

    'warning_reasons' => [
        'Product does not match the registered category',
        'Prohibited product',
        'Inappropriate or misleading product content',
        'Misleading product information',
        'Unauthorized or restricted item',
        'Other',
    ],

    'remove_reasons' => [
        'Prohibited product',
        'Product does not match the registered category',
        'Inappropriate product content',
        'Misleading product information',
        'Violation of platform policies',
        'Other',
    ],
];
