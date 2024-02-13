<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreterPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('en_name');
            $table->string('zh_name');
            $table->float('price');
            $table->integer('validity');
            $table->integer('events');
            $table->enum('status', ['0', '1'])->default('0');
            $table->enum('type', ['1', '2'])->default('1')->comment('1=>normal user, 2=>performer');
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
        Schema::dropIfExists('packages');
    }
}
