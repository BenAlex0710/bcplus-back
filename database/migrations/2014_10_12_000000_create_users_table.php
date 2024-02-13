<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('photo')->nullable();
            $table->string('headline')->nullable();
            $table->text('bio')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('google_url')->nullable();
            $table->string('organization_name')->nullable();
            $table->string('organization_email')->nullable();
            $table->enum('role', ['1', '2'])->comment('1 => normal, 2 => performer')->default('1');
            $table->enum('status', ['0', '1', '2'])->default('0')->comment('0 => unapproved, 1 => approved, 2 => suspended');
            $table->rememberToken();
            $table->timestamps();
        });
        DB::statement("ALTER TABLE " . DB::getTablePrefix() . "users AUTO_INCREMENT = 10001;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
