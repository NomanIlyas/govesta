<?php

$location = [
    ['name' => 'x', 'width' => 236, 'height' => 300, 'quality' => 90],
    ['name' => '2x', 'width' => 472, 'height' => 600, 'quality' => 90]
];

return [

    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | Intervention Image supports "GD Library" and "Imagick" to process images
    | internally. You may choose one of them according to your PHP
    | configuration. By default PHP's "GD Library" implementation is used.
    |
    | Supported: "gd", "imagick"
    |
    */

    'driver' => 'gd',

    'sizes' => [
        'property' => [
            ['name' => 'x', 'width' => 300, 'height' => 180, 'quality' => 90],
            ['name' => '2x', 'width' => 600, 'height' => 360, 'quality' => 90]
        ],
        'property-floor-plan' => [
            ['name' => 'x', 'width' => 300, 'height' => 180, 'quality' => 90],
            ['name' => '2x', 'width' => 600, 'height' => 360, 'quality' => 90]
        ],
        'user-avatar' => [
            ['name' => 'x', 'width' => 256, 'height' => 256, 'quality' => 90],
            ['name' => '2x', 'width' => 512, 'height' => 512, 'quality' => 90]
        ],
        'user-cover' => [
            ['name' => 'x', 'width' => 1440, 'height' => 450, 'quality' => 80],
            ['name' => '2x', 'width' => 2560, 'height' => 840, 'quality' => 80]
        ],
        'city' => $location,
        'state' => $location,
        'district' => $location
    ]

];
