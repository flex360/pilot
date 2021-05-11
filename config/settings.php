<?php

/////////////////////////////////////////////////////////////////
//  Rules:                                                     //
//      Setting Rules:                                         //
//      --------------                                         //
//      --Key of setting should be lowercased                  //
//      --label, Saves as Name field in Settings table         //
//               in database and this text will be             //
//               placed in the bootstrap Pill Button           //
//                                                             //
//      Field Rules:                                           //
//      ------------                                           //
//      --id, unique id within the setting, must be            //
//            slugged                                          //
//      --type,                                                //
//      If its a header, give it a type of 'header'            //
//      If its a text field, give it a type of 'text'          //
//      If its a textarea field, give it a type of 'textarea'  //
//      if its a wysiwyg field, give it type of 'wysiwyg'      //
//      --label, is the header text if type is header, is the  //
//               input label otherwise                         //
//                                                             //
//                                                             //
//                                                             //
/////////////////////////////////////////////////////////////////

return [
    'tracking' => [
        'label' => 'Tracking Code',
        'fields' => [
            [
                'id' => 'head_top',
                'type' => 'textarea',
                'label' => 'Top of <code>&lt;HEAD></code> tag',
            ],
            [
                'id' => 'head_bottom',
                'type' => 'textarea',
                'label' => 'Bottom of <code>&lt;HEAD></code> tag',
            ],
            [
                'id' => 'body_top',
                'type' => 'textarea',
                'label' => 'Top of <code>&lt;BODY></code> tag',
            ],
            [
                'id' => 'body_bottom',
                'type' => 'textarea',
                'label' => 'Bottom of <code>&lt;BODY></code> tag',
            ],
        ],
    ],

    // 'contact' => [
    //     'label' => 'Contact',
    //     'fields' => [
    //         [
    //             'id' => 'contact_info_header',
    //             'type' => 'header',
    //             'label' => 'Primary Contact Information',
    //         ],

    //         [
    //             'id' => 'physical_address',
    //             'type' => 'text',
    //             'label' => 'Physical Address',
    //         ],

    //         [
    //             'id' => 'mailing_address',
    //             'type' => 'text',
    //             'label' => 'Mailing Address',
    //         ],

    //         [
    //             'id' => 'primary_phone',
    //             'type' => 'text',
    //             'label' => 'Primary Phone',
    //         ],

    //         [
    //             'id' => 'primary_email',
    //             'type' => 'text',
    //             'label' => 'Primary Email',
    //         ],

    //         [
    //             'id' => 'business_hours',
    //             'type' => 'textarea',
    //             'label' => 'Business Hours',
    //         ],

    //         [
    //             'id' => 'customer_service_info_header',
    //             'type' => 'header',
    //             'label' => 'Department: Customer Service',
    //         ],

    //         [
    //             'id' => 'customer_service_phone',
    //             'type' => 'text',
    //             'label' => 'Phone Number',
    //         ],

    //         [
    //             'id' => 'customer_service_email',
    //             'type' => 'text',
    //             'label' => 'Email Address',
    //         ],

    //         [
    //             'id' => 'contact_form_info_header',
    //             'type' => 'header',
    //             'label' => 'Contact Form',
    //         ],

    //         [
    //             'id' => 'contact_form_code',
    //             'type' => 'textarea',
    //             'label' => 'Code',
    //         ],
    //     ],
    // ],

    // 'events' => [
    //     'label' => 'Events',
    //     'fields' => [
    //         [
    //             'id' => 'test',
    //             'type' => 'text',
    //             'label' => 'To Be Determined Data',
    //         ],
    //     ],
    // ],

    // 'news' => [
    //     'label' => 'News',
    //     'fields' => [
    //         [
    //             'id' => 'test',
    //             'type' => 'text',
    //             'label' => 'To Be Determined Data',
    //         ],
    //     ],
    // ],
];
