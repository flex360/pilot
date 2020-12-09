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

    'table_prefix' => env('PILOT_TABLE_PREFIX', ''),

    'plugins' => [
        'pages' => [
            'name' => 'Pages',
            'enabled' => true,
            'url' => ['admin.page.index'],
            'routePattern' => 'admin.page.*',
            'view' => null,
            'target' => '_self',
            'children' => null
        ],
        'news' => [
            'name' => 'News',
            'enabled' => true,
            'url' => ['admin.post.index'],
            'routePattern' => 'admin.post.*',
            'view' => null,
            'target' => '_self',
            'children' => [
                'manage_post' => [
                    'name' => 'Manage News',
                    'enabled' => true,
                    'url' => ['admin.post.index'],
                    'routePattern' => null,
                    'view' => 'published',
                    'target' => '_self',
                    'children' => null,
                ],
                'add_post' => [
                    'name' => 'Add New Post',
                    'enabled' => true,
                    'url' => ['admin.post.create'],
                    'routePattern' => null,
                    'view' => null,
                    'target' => '_self',
                    'children' => null,
                ],
            ]
        ],
        'events' => [
            'name' => 'Events',
            'enabled' => false,
            'url' => ['admin.event.index'],
            'routePattern' => 'admin.event.*',
            'view' => null,
            'target' => '_self',
            'children' => [
                'manage_event' => [
                    'name' => 'Manage Events',
                    'enabled' => true,
                    'url' => ['admin.event.index'],
                    'routePattern' => null,
                    'view' => 'published',
                    'target' => '_self',
                    'children' => null,
                ],
                'add_event' => [
                    'name' => 'Add New Event',
                    'enabled' => true,
                    'url' => ['admin.event.create'],
                    'routePattern' => null,
                    'view' => null,
                    'target' => '_self',
                    'children' => null,
                ],
            ]
        ],
        'annoucements' => [
            'name' => 'Alerts',
            'enabled' => true,
            'url' => ['admin.annoucement.index'],
            'routePattern' => 'admin.annoucement.*',
            'view' => null,
            'target' => '_self',
            'children' => null
        ],
        'resources' => [
            'name' => 'Resources',
            'enabled' => true,
            'url' => ['admin.resource.index'],
            'routePattern' => 'admin.resource.*',
            'view' => 'published',
            'target' => '_self',
            'children' => [
                'manage_resource' => [
                    'name' => 'Manage Resources',
                    'enabled' => true,
                    'url' => ['admin.resource.index'],
                    'routePattern' => null,
                    'view' => 'published',
                    'target' => '_self',
                    'children' => null,
                ],
                'add_resource' => [
                    'name' => 'Add New Resource',
                    'enabled' => true,
                    'url' => ['admin.resource.create'],
                    'routePattern' => null,
                    'view' => null,
                    'target' => '_self',
                    'children' => null,
                ],
                'resource_category' => [
                    'name' => 'Manage Resource Categories',
                    'enabled' => true,
                    'url' => ['admin.resourcecategory.index'],
                    'routePattern' => null,
                    'view' => null,
                    'target' => '_self',
                    'children' => null
                ],
                'add_resource_category' => [
                    'name' => 'Add New Resource Category',
                    'enabled' => true,
                    'url' => ['admin.resourcecategory.create'],
                    'routePattern' => null,
                    'view' => null,
                    'target' => '_self',
                    'children' => null
                ]
            ],
        ],
        'employees' => [
            'name' => 'Employees',
            'enabled' => true,
            'url' => ['admin.employee.index'],
            'routePattern' => 'admin.employee.*',
            'view' => 'published',
            'target' => '_self',
            'children' => [
                'manage_employee' => [
                    'name' => 'Manage Employees',
                    'enabled' => true,
                    'url' => ['admin.employee.index'],
                    'routePattern' => null,
                    'view' => 'published',
                    'target' => '_self',
                    'children' => null,
                ],
                'add_employee' => [
                    'name' => 'Add New Employee',
                    'enabled' => true,
                    'url' => ['admin.employee.create'],
                    'routePattern' => null,
                    'view' => null,
                    'target' => '_self',
                    'children' => null,
                ],
                'departments' => [
                    'name' => 'Manage Departments',
                    'enabled' => true,
                    'url' => ['admin.department.index'],
                    'routePattern' => null,
                    'view' => null,
                    'target' => '_self',
                    'children' => null,
                    'tags_relationship' => true,
                    'resources_relationship' => true,
                    'sort_employees_within_department' => false
                ],
                'add_departments' => [
                    'name' => 'Add New Department',
                    'enabled' => true,
                    'url' => ['admin.department.create'],
                    'routePattern' => null,
                    'view' => null,
                    'target' => '_self',
                    'children' => null,
                    'tags_relationship' => true,
                    'resources_relationship' => true,
                ]
            ],
        ],
        'forms' => [
            'name' => 'Forms',
            'enabled' => false,
            'url' => ['admin.form.index'],
            'routePattern' => 'admin.form.*',
            'view' => null,
            'target' => '_self',
            'children' => null
        ],
        'styles' => [
            'name' => 'Styles',
            'enabled' => false,
            'url' => ['admin.style.index'],
            'routePattern' => 'admin.style.*',
            'view' => null,
            'target' => '_self',
            'children' => null
        ],
    ],

    'disable_page_parts' => true,

    'disable_menu_builder' => false,
];
