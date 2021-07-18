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
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active');
            $table->string('mime_type', 100)->nullable();
            $table->string('src')->unique();
            $table->string('src_webp')->nullable();
            $table->string('caption')->nullable();
            $table->json('thumbnails')->nullable();
            $table->string('disk')->default(env('FILESYSTEM_DRIVER'));
            $table->json('info')->nullable();
            $table->string('hash', 100)->nullable();
            $table->text('exif')->nullable();
            $table->dateTime('captured_at')->nullable();

            $table->string('photoable_type')->nullable();
            $table->unsignedBigInteger('photoable_id')->nullable();

            $table->index(['photoable_type', 'photoable_id']);
            $table->timestamps();
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
