<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBulletintemplateitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bulletintemplateitems', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('template_id')->nullable();
            $table->integer('columnnumber')->nullable();
            $table->integer('widget_id')->nullable();
            $table->integer('sortorder')->nullable();
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
        Schema::dropIfExists('bulletintemplateitems');
    }
}
