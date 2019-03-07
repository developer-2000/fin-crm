<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passes', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('active')->index();
            $table->string('type')->index();
            $table->integer('sub_project_id')->index();
            $table->integer('user_id')->index();
            $table->text('comment');
            $table->timestamps();
        });

        $permissions = [
            [
                'name' => 'page_pass',
                'section'=> 'pass',
                'section_alias'=> 'Проводки',
                'group' => 'menu',
                'alias' => 'Страница проводок',
            ],
            [
                'name' => 'page_pass_one',
                'section'=> 'pass',
                'section_alias'=> 'Проводки',
                'group' => null,
                'alias' => 'Страница одной проводки',
            ],
            [
                'name' => 'page_pass_redemption',
                'section'=> 'pass',
                'section_alias'=> 'Проводки',
                'group' => null,
                'alias' => 'Страница "Выкуп"',
            ],
            [
                'name' => 'page_pass_no_redemption',
                'section'=> 'pass',
                'section_alias'=> 'Проводки',
                'group' => null,
                'alias' => 'Страница "Не выкуп"',
            ],
            [
                'name' => 'page_pass_sending',
                'section'=> 'pass',
                'section_alias'=> 'Проводки',
                'group' => null,
                'alias' => 'Страница "Отправлен"',
            ],
        ];
        foreach ($permissions as $permission) {
            $exists = DB::table('permissions')->where('name', $permission['name'])->exists();

            if (!$exists) {
                DB::table('permissions')->insert($permission);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('passes');
    }
}
