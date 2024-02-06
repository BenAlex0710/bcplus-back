<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventAttendeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_attendees', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('event_id');
            $table->bigInteger('user_id');
            $table->float('amount');
            $table->enum('payment_status', ['0', '1', '2'])->comment('0 => payment not completed, 1 => success, 2 => failed')->default('0');
            $table->text('payment_response')->nullable();
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
        Schema::dropIfExists('event_attendees');
    }
}
