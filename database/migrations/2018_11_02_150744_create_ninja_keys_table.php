<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNinjaKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ninjaxpress_keys', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('target_id')->index();
            $table->string('active')->index()->nullable();
            $table->string('name', 50);
            $table->string('phone', 20);
            $table->string('country');
            $table->integer('postcode');
            $table->string('address');
            $table->string('email');
            $table->string('password');
            $table->integer('subproject_id')->index();
            $table->string('client_id')->index()->nullable();
            $table->string('client_secret')->index()->nullable();
            $table->string('access_token')->index()->nullable();
            $table->integer('expires')->index()->nullable();
            $table->string('token_type')->index()->nullable();
            $table->integer('expires_in')->index()->nullable();
            $table->string('size')->nullable();
            $table->decimal('weight',10, 2)->nullable();
            $table->decimal('volume', 10, 2)->nullable();
            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height',10, 2)->nullable();
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
        Schema::dropIfExists('ninjaxpress_keys');
    }
}
