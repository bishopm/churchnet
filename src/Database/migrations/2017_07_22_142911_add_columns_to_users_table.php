<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function($table) {
            $table->dropColumn('password');
            $table->integer('circuit_id')->nullable();
            $table->string('app_secret')->nullable();
            $table->string('app_name')->nullable();
            $table->string('app_url')->nullable();
            $table->string('username')->nullable();
            $table->softDeletes();
        });
        Schema::table('users', function($table) {
            $table->string('password')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function($table) {
            $table->dropColumn('circuit_id');
            $table->dropColumn('app_secret');
            $table->dropColumn('app_name');
            $table->dropColumn('app_url');
            $table->dropColumn('username');
        });
    }
}