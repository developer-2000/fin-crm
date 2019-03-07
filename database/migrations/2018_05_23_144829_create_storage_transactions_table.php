<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStorageTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('project_id')->default(0);
            $table->unsignedInteger('user_id')->default(0);
            // количество на складе может быть ниже нуля, поэтому и в складах amount придётся исправить
            $table->integer('amount1')->default(0);
            $table->integer('amount2')->default(0);
            $table->unsignedInteger('hold1')->default(0);
            $table->unsignedInteger('hold2')->default(0);
            $table->unsignedTinyInteger('type')->default(0);
            $table->timestamp('created_at');

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('storage_contents', function(Blueprint $table) {
            $table->integer('amount')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('storage_transactions');
    }
}
