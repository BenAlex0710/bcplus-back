<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomeSlidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('home_slides', function (Blueprint $table) {
            $table->id();
            $table->string('language');
            $table->string('image');
            $table->string('title');
            $table->string('subtitle');
            $table->string('button_label')->nullable();
            $table->string('button_url')->nullable();
            $table->string('slide_order');
            $table->enum('status', ['0', '1'])->comment('0 => disable, 1 => enable')->default('1');
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
        Schema::dropIfExists('home_slides');
    }
}
