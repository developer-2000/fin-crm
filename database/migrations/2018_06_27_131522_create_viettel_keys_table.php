<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViettelKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viettel_keys', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('target_id')->index();
            $table->string('active')->index()->nullable();
            $table->string('email')->index();
            $table->string('name', 50)->index();
            $table->string('user_name')->index();
            $table->integer('subproject_id');
            $table->integer('user_id')->index();
            $table->integer('role')->index();
            $table->string('from_source')->index();
            $table->string('token_key')->index();
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
        Schema::dropIfExists('viettel_keys');
    }
}
