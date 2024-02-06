<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusInPackageOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_orders', function (Blueprint $table) {
            $table->enum('status', ['0', '1', '2'])->comment('0 => Not Used, 1 => Active, 2 => Expired')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_orders', function (Blueprint $table) {
            $table->dropColumn(['status']);
        });
    }
}
