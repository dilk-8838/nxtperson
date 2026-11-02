<?php

return [
    /*
    |--------------------------------------------------------------------------
    | FPO Weightment Discrepancy Tolerance Percentage
    |--------------------------------------------------------------------------
    |
    | Defines the allowable variance between declared delivery quantity and
    | measured net weight before a formal discrepancy exception is raised.
    | Default is 5.0% (standard APMC/Mandi trade tolerance).
    | Set to 0.0 for zero tolerance.
    |
    */
    'weight_discrepancy_tolerance_percentage' => (float) env('AGRITECH_WEIGHT_TOLERANCE_PERCENT', 5.0),

    /*
    |--------------------------------------------------------------------------
    | Separation of Duties Policy
    |--------------------------------------------------------------------------
    |
    | When true, prevents the same user who performed quality tests/inspection
    | from approving the final commercial quality grade on that delivery.
    |
    */
    'enforce_separation_of_duties' => (bool) env('AGRITECH_SEPARATION_OF_DUTIES', false),

    /*
    |--------------------------------------------------------------------------
    | Default Sampling Method
    |--------------------------------------------------------------------------
    */
    'default_sampling_method' => 'Manual Composite Sampling (BIS/IS 4333)',

    /*
    |--------------------------------------------------------------------------
    | Weighbridge Operational Limits (in kg)
    |--------------------------------------------------------------------------
    */
    'weighbridge' => [
        'min_gross_weight_kg' => 10.0,
        'max_gross_weight_kg' => 60000.0, // 60 MT weighbridge limit
    ],
];
