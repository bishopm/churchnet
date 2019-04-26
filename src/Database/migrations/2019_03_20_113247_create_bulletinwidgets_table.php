<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBulletinwidgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bulletinwidgets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('widgettype')->nullable();
            $table->integer('widgetheight')->nullable();
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
        Schema::dropIfExists('bulletinwidgets');
    }
}
