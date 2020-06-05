<?php
Route::group([
    'prefix' => 'photo', 'as' => 'photo::',
    'middleware' => ['web','auth'],
    'namespace' => '\Photo\Http\Controllers'], function () {
    Route::get('photos/download-url','photoController@downloadUrl')->name('photos.downloadUrl');
    Route::post('photos/dropzone', 'PhotoController@dropzone')->name('photos.dropzone');

    Route::resource('photos', 'PhotoController');
    Route::resource('albums', 'AlbumController');
});
