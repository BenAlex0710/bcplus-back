<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventLiveDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_live_data', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('event_id');
            $table->text('resource_id');
            $table->string('sid');
            $table->string('uid');
            $table->text('video_url')->nullable();
            $table->timestamps();
        });
    }

    /**string
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_live_data');
    }
}
