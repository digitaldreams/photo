<?php

return [
    /*
     * Layout will be used
     */
    'layout' => 'photo::layouts.app',

    'default' => env('PHOTO_DEFAULT_IMAGE_URL', '/storage/images/default.png'),

    /*
     * Local Storage Path.
     */
    'rootPath' => 'images',

    /*
     * either disk or cloud
     */
    'filesystem' => 'disk',

    /*
     * Do you like to reduce image size?
     */
    'compressSize' => true,

    /*
     * Exif Data
     */
    'exif' => true,

    /*
     * Maximum weight of image. Leave blank or false if you do not like shrink your images
     */
    'maxWidth' => env('PHOTO_IMAGE_MAX_WIDTH', 800),

    /*
     * Maximum height of image. Leave blank or false if you do not like shrink your images
     */
    'maxHeight' => env('PHOTO_IMAGE_MAX_HEIGHT', 450),

    /*
     * How many size of your image you want.
     */
    'dimensions' => [
        [
            'height' => 200,
            'width' => 250,
        ],
    ],

    /**
     * Photo Policy Class.
     */
    'policy' => \Photo\Policies\PhotoPolicy::class,
];
