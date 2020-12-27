<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBulletinwidgetfieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bulletinwidgetfields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bulletin_id')->nullable();
            $table->integer('bulletinwidget_id')->nullable();
            $table->text('widgetdata')->nullable();
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
        Schema::dropIfExists('bulletinwidgetfields');
    }
}
