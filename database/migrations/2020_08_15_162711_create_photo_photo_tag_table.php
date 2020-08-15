<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhotoPhotoTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photo_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('tag_id')->unsigned();
            $table->unsignedInteger('photo_id')->unsigned();
            $table->foreign('tag_id')->references('id')->on('photo_tags')->onDelete('cascade');
            $table->foreign('photo_id')->references('id')->on('photo_photos')->onDelete('cascade');
            $table->primary(['tag_id', 'photo_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('photo_tag');
    }
}
