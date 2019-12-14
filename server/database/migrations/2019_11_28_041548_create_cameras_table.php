<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCamerasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cameras', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('location');
            $table->string('ip_stream');
            $table->string('ip_local');
            $table->integer('port');
            $table->integer('roi')->nullable();
            $table->integer('scale')->default(100);
            $table->string('direction', 20)->default('horizontal');
            $table->float('longitude')->nullable();
            $table->float('latitude')->nullable();
            $table->integer('status')->default(100);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cameras');
    }
}
