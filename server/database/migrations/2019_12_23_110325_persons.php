<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Persons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('persons', function (Blueprint $table) {
            $table->decimal('nik', 16,0);
            $table->string('name');
            $table->text('address');
            $table->string('place_of_birth');
            $table->date('date_of_birth');
            $table->string('gender');
            $table->integer('religion');
            $table->string('marital_status');
            $table->integer('profession');
            $table->string('citizenship');
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
        Schema::dropIfExists('persons');
    }
}
