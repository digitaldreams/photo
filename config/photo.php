<?php
return [
    /**
     * Local Storage Path.
     */
    'rootPath' => 'images',

    /**
     *
     */
    'driver' => 'public',
    /**
     * Do you like to reduce image size?
     */
    'compressSize' => true,
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

];