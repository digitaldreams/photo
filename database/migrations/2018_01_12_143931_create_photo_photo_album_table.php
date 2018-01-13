<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhotoPhotoAlbumTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('album_photo', function (Blueprint $table) {
            $table->integer('album_id')->unsigned();
            $table->integer('photo_id')->unsigned();
            $table->foreign('album_id')->references('id')->on('photo_albums')->onDelete('cascade');
            $table->foreign('photo_id')->references('id')->on('photo_photos')->onDelete('cascade');
            $table->primary(['album_id', 'photo_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('album_photo');
    }
}
