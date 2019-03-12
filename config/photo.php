<?php
return [
    /**
     * Layout will be used
     */
    'layout' => 'permit::layouts.app',

    'default' => '/storage/images/default.png',
    /**
     * Local Storage Path.
     */
    'rootPath' => 'images',

    /**
     * Url prefix that will be added to all photos.
     * For example if you are using public driver then prefix will be storage
     */
    'prefix' => 'storage',

    /**
     *
     */
    'driver' => 'public',
    /**
     * Do you like to reduce image size?
     */
    'compressSize' => true,

    /**
     * Exif Data
     */
    'exif' => true,

    /**
     * Maximum weight of image. Leave blank or false if you do not like shrink your images
     */
    'maxWidth' => 1024,

    /**
     * Maximum height of image. Leave blank or false if you do not like shrink your images
     */
    'maxHeight' => 750,

    /**
     * How many size of your image you want.
     */
    'sizes' => [
        /**
         * Thumbnail size in pixel
         */
        'thumbnail' => [
            /**
             * Path are relative to rootPath.
             * Suppose rootPath is photos and thumbnail path is thumbnails.
             * Then your thumbnail full path will be photos/thumbnails
             */
            'path' => 'thumbnails',
            'height' => 250,
            'width' => 250
        ],
    ],
    'googleMapApiKey' => env('GOOGLE_MAP_API_KEY'),

    /**
     * HERE PROJECT INFO.
     * https://developer.here.com
     */
    'here' => [
        'app_id' => env('HERE_APP_ID'),
        'app_code' => env('HERE_APP_CODE')
    ]
];