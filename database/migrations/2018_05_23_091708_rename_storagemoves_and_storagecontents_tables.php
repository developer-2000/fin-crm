<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameStoragemovesAndStoragecontentsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('storagecontents', function(Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['product_id']);
        });
        Schema::table('storagemoves', function(Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['receiver_id']);
            $table->dropForeign(['sender_id']);
        });

        Schema::rename('storagecontents', 'storage_contents');
        Schema::rename('storagemoves', 'storage_moves');

        Schema::table('storage_contents', function(Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
        Schema::table('storage_moves', function(Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('projects')->onDelete('cascade');
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
