<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIntegrationKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('integration_keys', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('integration_id');
            $table->string('key')->index();
            $table->string('name', 50);
            $table->timestamp('exp_key_date')->index();
            $table->string('active')->index();
            $table->string('sender_id')->index();
            $table->text('contacts')->nullable();
            $table->string('size')->nullable();
            $table->string('weight')->nullable();
            $table->string('description')->nullable();
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
        Schema::dropIfExists('integration_keys');
    }
}
