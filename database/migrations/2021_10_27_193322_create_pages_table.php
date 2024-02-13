<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('en_title');
            $table->string('zh_title');
            $table->text('en_description');
            $table->text('zh_description');
            $table->enum('status', ['0', '1'])->comment('0=>Disabled, 1=>Enabled')->default('1');
            $table->enum('system_page', ['0', '1'])->comment('0=>No, 1=>Yes')->default('0');
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
        Schema::dropIfExists('pages');
    }
}
