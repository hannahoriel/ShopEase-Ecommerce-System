<?php

return [
    'weight_brackets' => [
        ['max_weight_kg' => 3, 'fee' => 50],
        ['max_weight_kg' => 5, 'fee' => 60],
        ['max_weight_kg' => 10, 'fee' => 70],
        ['max_weight_kg' => 15, 'fee' => 80],
        ['max_weight_kg' => 20, 'fee' => 90],
    ],
    'additional_bracket_weight_kg' => 5,
    'additional_bracket_fee' => 10,
    'volumetric_divisor' => (float) env('SHIPPING_VOLUMETRIC_DIVISOR', 5000),
];
