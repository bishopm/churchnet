<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocietiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('societies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('society');
            $table->string('slug')->nullable();
            $table->integer('circuit_id');
            $table->string('address')->nullable();
            $table->string('contact')->nullable();
            $table->string('website')->nullable();
            $table->string('journey')->nullable();
            $table->double('latitude', 20, 15);
            $table->double('longitude', 20, 15);
            $table->softDeletes();
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
        Schema::drop('societies');
    }
}
