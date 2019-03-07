<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressFiledsNinja extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ninjaxpress_keys', function ( Blueprint $table ) {
            $table->string('kelurahan')->index()->nullable();
            $table->string('kecamatan')->index()->nullable();
            $table->string('city')->index()->nullable();
            $table->string('province')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
