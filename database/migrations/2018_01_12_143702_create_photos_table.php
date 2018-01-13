<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photo_photos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('caption')->nullable();
            $table->string('title')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->string('src');
            $table->integer('location_id')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('location_id')->references('id')->on('photo_locations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('photo_photos');
    }
}
