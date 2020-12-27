<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeeditemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feeditems', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category');
            $table->integer('distributable_id')->nullable();
            $table->string('distributable_type')->nullable();
            $table->integer('feedpost_id')->nullable();
            $table->string('library')->nullable();
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
        Schema::drop('feeditems');
    }
}
