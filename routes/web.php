<?php
Route::group([
    'prefix' => 'photo', 'as' => 'photo::',
    'middleware' => ['web'],
    'namespace' => '\Photo\Http\Controllers'], function () {
    Route::resource('photos', 'PhotoController');
    Route::resource('albums', 'AlbumController');
});
