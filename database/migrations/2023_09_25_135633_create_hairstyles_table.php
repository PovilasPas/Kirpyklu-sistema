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
        Schema::create('hairstyles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->double('price', 12, 2);
            $table->string('image')->nullable();
            $table->integer('estimated_time_min')->unsigned();
            $table->integer('hairdresser_id')->unsigned();
            $table->foreign('hairdresser_id')->references('id')->on('hairdressers')->onDelete('cascade');
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
        Schema::dropIfExists('hairstyles');
    }
};
