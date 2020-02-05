<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMorphColumnsToPhotoPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('photo_photos', function (Blueprint $table) {
            $table->unsignedInteger('photoable_id')->nullable();
            $table->string('photoable_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('photo_photos', function (Blueprint $table) {
            $table->dropColumn(['photoable_id', 'photoable_type']);
        });
    }
}
