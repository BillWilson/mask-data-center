<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name');
            $table->string('tel');
            $table->string('address');
            $table->string('gmap_address')->nullable();
            $table->unsignedSmallInteger('adult')->default(0);
            $table->unsignedSmallInteger('child')->default(0);
            $table->unsignedDecimal('latitude', 10, 7)->nullable();
            $table->unsignedDecimal('longitude', 10, 7)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->unique(['code']);
            $table->index(['adult']);
            $table->index(['child']);
            $table->index(['latitude']);
            $table->index(['longitude']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facilities');
    }
}
