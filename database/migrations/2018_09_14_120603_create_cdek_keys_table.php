<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCdekKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cdek_keys', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('target_id')->index();
            $table->string('active')->index()->nullable();
            $table->string('name', 50)->index();
            $table->integer('subproject_id');
            $table->integer('postal_code');
            $table->string('account');
            $table->string('secure');
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
        Schema::dropIfExists('cdek_keys');
    }
}
