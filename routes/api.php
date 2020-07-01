<?php

Route::group([
    'prefix' => 'api/photo', 'as' => 'photo::api.',
    'middleware' => ['web'],
    'namespace' => '\Photo\Http\Controllers\Api', ], function () {
        Route::resource('photos', 'PhotoController')->only(['index', 'store']);
    });
