<?php

return [
    'multisite' => false,

    'default_layout' => 'layouts.internal',

    'backend_side_bar_layout' => false,

    'social_image' => env('SOCIAL_IMAGE'),
    'social_image_width' => env('SOCIAL_IMAGE_WIDTH'),
    'social_image_height' => env('SOCIAL_IMAGE_HEIGHT'),

    'plugins' => [
        'pages' => [
            'name' => 'Pages',
            'enabled' => true,
        ],
        'events' => [
            'name' => 'Events',
            'enabled' => true,
        ],
        'news' => [
            'name' => 'News',
            'enabled' => true,
        ],
        'annoucements' => [
            'name' => 'Annoucements',
            'enabled' => false,
        ],
        'forms' => [
            'name' => 'Forms',
            'enabled' => false,
        ],
        'styles' => [
            'name' => 'Styles',
            'enabled' => false,
        ],
    ],

    'disable_page_parts' => true,

    'disable_menu_builder' => false,
];
