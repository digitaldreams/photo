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
            $table->string('mime_type', 100)->nullable();
            $table->string('src');

            $table->enum('status', ['active', 'inactive', 'pending'])->default('active');
            $table->text('exif')->nullable();
            $table->dateTime('taken_at')->nullable();
            $table->dateTime('captured_at')->nullable();

            $table->foreignId('location_id')->nullable()->constrained('photo_locations', 'id')->onDelete('set null');
            $table->unsignedInteger('photoable_id')->nullable();
            $table->string('photoable_type')->nullable();
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
