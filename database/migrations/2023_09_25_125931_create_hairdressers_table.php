<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hairdressers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone_nr');
            $table->boolean('is_approved');
            $table->integer('hair_salon_id')->unsigned();
            $table->foreign('hair_salon_id')->references('id')->on('hair_salons')->onDelete('cascade');
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
        Schema::dropIfExists('hairdressers');
    }
};
