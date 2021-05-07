<?php

return [
    'storage_disk' => 'uploads',

    'upload_path' => '/assets/uploads/modules/',

    'route_prefix' => 'admin.',

    'layout' => 'pilot::layouts.admin.master',

    'controller_namespace' => 'Admin',

    'controller_path' => app_path('/Http/Controllers/Admin'),

    'view_prefix' => 'admin.dynamo',

    'view_theme' => 'bootstrap4',

    'default_has_many_class' => 'chosen-select',

    'modules_links_path' => base_path('resources/views/vendor/pilot/admin/partials/modules.blade.php'),

    'modulesSidebar_links_path' => base_path('resources/views/vendor/pilot/admin/partials/modulesSidebar.blade.php'),

    'editor_command' => 'code',

    /*
    |--------------------------------------------------------------------------
    | Model use statements
    |--------------------------------------------------------------------------
    |
    | This value contains an array of the classes that should be imported into
    | the generated model class.
    |
    */
    'model_uses' => [
        Spatie\MediaLibrary\Models\Media::class,
        Spatie\MediaLibrary\HasMedia\HasMediaTrait::class,
        Spatie\MediaLibrary\HasMedia\HasMedia::class,
        Spatie\Image\Manipulations::class,
        Illuminate\Database\Eloquent\SoftDeletes::class,
        Flex360\Pilot\Pilot\Traits\HasMediaAttributes::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Interfaces that the model implements
    |--------------------------------------------------------------------------
    |
    | This value contains an array of the interfaces that should be
    | implemented by the generated model class.
    |
    */
    'model_implements' => [
        'HasMedia',
    ],

    /*
    |--------------------------------------------------------------------------
    | Model traits
    |--------------------------------------------------------------------------
    |
    | This value contains an array of the traits that should be used by the
    | generated model class.
    |
    */
    'model_traits' => [
        'HasMediaTrait',
        'SoftDeletes',
        "HasMediaAttributes {\n        HasMediaAttributes::registerMediaConversions insteadof HasMediaTrait;\n    }",
    ],
];
