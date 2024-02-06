<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEventGuestsTableForEmailAndName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_guests', function (Blueprint $table) {
            $table->string('email');
            $table->string('full_name');
            // $table->dropColumn(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_guests', function (Blueprint $table) {
            // $table->bigInteger('user_id');
            $table->dropColumn(['email', 'full_name']);
        });
    }
}
