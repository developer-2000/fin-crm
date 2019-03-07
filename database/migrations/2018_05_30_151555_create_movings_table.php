<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sender_id')->default(0)->index(); // project_id склада отправителя, или 0 - космос
            $table->unsignedInteger('receiver_id')->default(0)->index(); // project_id склада получателя, или 0 - космос
            $table->timestamp('send_date')->nullable()->default(NULL);
            $table->timestamp('received_date')->nullable()->default(NULL);
        });

        DB::statement('truncate table storage_transactions');
        Schema::table('storage_transactions', function(Blueprint $table) {
            $table->unsignedInteger('moving_id')->after('project_id');
            $table->foreign('moving_id')->references('id')->on('movings')->onDelete('cascade');
        });

        Schema::create('moving_product', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('amount');
            $table->unsignedInteger('moving_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('moving_id')->references('id')->on('movings')->onDelete('cascade');
        });

        Schema::dropIfExists('storage_moves');
        Schema::dropIfExists('storage_pack_moves');

        DB::table('permissions')->insert([
            ['name' => 'my_movings', 'alias' => 'Движение в проекте', 'group' => 'menu'],
            ['name' => 'moving_create', 'alias' => 'Создание движения', 'group' => null],
        ]);

        Schema::table('comments', function(Blueprint $table){
            $table->morphs('commentable');
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
