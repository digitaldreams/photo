<?php
Route::group([
    'prefix' => 'photo', 'as' => 'photo::',
    'middleware' => ['web'],
    'namespace' => '\Photo\Http\Controllers'], function () {
    Route::get('download-url','photoController@downloadUrl')->name('photos.downloadUrl');
    Route::resource('photos', 'PhotoController');
    Route::resource('albums', 'AlbumController');
});
