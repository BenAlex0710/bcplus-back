<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_reviews', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('event_id');
            $table->bigInteger('performer_id');
            $table->bigInteger('reviewer_id');
            $table->integer('stars');
            $table->text('review');
            $table->enum('status', ['0', '1', '2'])->comment('0 => pending, 1 => approved, 2 => rejected')->default('0');
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
        Schema::dropIfExists('event_reviews');
    }
}
