<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_category_id')->index();
            $table->string('title');
            $table->string('priority', 20)->index();
            $table->text('body');
            $table->integer('author_id')->index();
            $table->datetime('publish_at')->index();
            $table->boolean('publish_complete')->index()->nullable();
            $table->boolean('active')->index();
            $table->string('familiarization_required')->index()->nullable()->default(NULL);
            $table->integer('views')->index()->nullable()->default(0);
            $table->text('filters_settings')->nullable()->default(NULL);
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
        Schema::dropIfExists('posts');
    }
}
