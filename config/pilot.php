<?php

return [
    'multisite' => false,

    'default_layout' => 'layouts.internal',

    'backend_side_bar_layout' => false,

    'pilot_color' => '#43379D',
    'pilot_color_dark' => '#322975',
    'pilot_color_light' => '#5B4EC1',
    'pilot_color_highlight' => '#786bd5',

    'social_image' => env('SOCIAL_IMAGE'),
    'social_image_width' => env('SOCIAL_IMAGE_WIDTH'),
    'social_image_height' => env('SOCIAL_IMAGE_HEIGHT'),

    'plugins' => [
        'pages' => [
            'name' => 'Pages',
            'enabled' => true,
            'url' => route('admin.page.index'),
        ],
        'events' => [
            'name' => 'Events',
            'enabled' => true,
            'url' => route('admin.event.index'),
        ],
        'news' => [
            'name' => 'News',
            'enabled' => true,
            'url' => route('admin.post.index'),
        ],
        'annoucements' => [
            'name' => 'Annoucements',
            'enabled' => false,
            'url' => route('admin.annoucement.index'),
        ],
        'forms' => [
            'name' => 'Forms',
            'enabled' => false,
            'url' => route('admin.form.index'),
        ],
        'styles' => [
            'name' => 'Styles',
            'enabled' => false,
            'url' => route('admin.style.index'),
        ],
    ],

    'disable_page_parts' => true,

    'disable_menu_builder' => false,
];
