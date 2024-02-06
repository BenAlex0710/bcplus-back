<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->float('amount');
            $table->integer('events');
            $table->text('package_data');
            $table->date('start_date')->nullable();
            $table->date('expiry_date')->nullable();
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
        Schema::dropIfExists('package_orders');
    }
}
