<?php

Route::group([
    'prefix' => 'photo', 'as' => 'photo::',
    'middleware' => ['web', 'auth'],
    'namespace' => '\Photo\Http\Controllers',], function () {
    Route::get('photos/{photo}/download', 'DownloadController@download')->name('photos.download');
    Route::get('photos/download-url', 'DownloadController@downloadUrl')->name('photos.downloadUrl');
    Route::post('photos/dropzone', 'DownloadController@dropzone')->name('photos.dropzone');
    Route::get('tags/search', 'TagController@search')->name('tags.search');
    Route::resource('photos', 'PhotoController');
});
