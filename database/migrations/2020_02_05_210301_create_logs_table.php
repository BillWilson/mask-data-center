<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('facility_id');
            $table->unsignedSmallInteger('adult')->default(0);
            $table->unsignedSmallInteger('child')->default(0);
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('created_at')->nullable();

            $table->unique(['facility_id', 'updated_at']);
            $table->foreign('facility_id')->on('facilities')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropForeign(['facility_id']);
        });

        Schema::dropIfExists('logs');
    }
}
