<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsNewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('projects_new', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type')->default(1)->index();
            $table->integer('type_id')->default(0)->index();
            $table->integer('parent_id')->default(0)->index();
            $table->string('name')->nullable();
            $table->string('alias')->nullable();
            $table->integer('partner_id')->index();
            $table->tinyInteger('negative')->default(0)->index();
            $table->integer('country_id')->default(0)->index();
            $table->timestamps();
        });

// Эта миграция создает новую таблицу заменяя в старой (projects) 3 строки (project_id , subproject_id , division_id)
// на 2 новые (type - "проект, подпроект или склад",  type_id - кодировка названия записи владельца)
// создает склад основываясь на имеющемся подпроекте ( если еще небыл назначен подпроекту )
// в случае создания склада , заменяет project_id в 2 таблицах (storage_contents, storage_transactions)
// со-значений id старого подпроекта на новый id склада
// заменяет на id нового склада sender_id и receiver_id таблицы moving

        $rows = [];
        $new_id = DB::table('projects')->orderBy('id', 'desc')->first()->id;
        $projects = DB::table('projects')->get();

        if ($projects) {

            foreach ($projects as $P) {

                    // забираю кодировку проекта владельца
                    if ($P->project_id !== '' && $P->project_id !== '0') { $type_id = $P->project_id; }
                    elseif($P->subproject_id !== '' && $P->subproject_id !== '0'){ $type_id =$P->subproject_id; }
                    elseif(!is_null($P->division_id) || $P->division_id){ $type_id =$P->division_id; }
                    else{ $type_id = 0; }

                // 1 если есть родитель
                if ($P->parent_id){


                    // 2 если нет divison_id - это подпроект
                    if(is_null($P->division_id) || !$P->division_id) {

                        // 3 если в таблице еще не создавался отдельный склад с подпроектом
                        if( is_null(DB::table('projects') ->where('parent_id', $P->id) ->value('id'))){
                        $new_id++;

                            // старый subproject остается subproject
                            $rows[] = $this->Insert( $P->id, $P->partner_id, 2, $type_id, $P->name,
                                $P->alias, $P->negative, $P->parent_id, $P->country_id, $P->created_at, $P->updated_at);

                            // новый склад
                            $rows[] = $this->Insert( $new_id, $P->partner_id, 3, $type_id, $P->name . " Sklad",
                                $P->alias . "-sklad", $P->negative, $P->id, $P->country_id, $P->created_at, $P->updated_at);

                            // в этих таблицах project_id = складу
                            DB::table('storage_contents') ->where('project_id', $P->id) ->update(['project_id' => $new_id]);
                            DB::table('storage_transactions') ->where('project_id', $P->id) ->update(['project_id' => $new_id]);
                            DB::table('movings') ->where('sender_id', $P->id) ->update(['sender_id' => $new_id]);
                            DB::table('movings') ->where('receiver_id', $P->id) ->update(['receiver_id' => $new_id]);

                        }
                        // 3.3 subproject остается subproject
                        else{
                            $rows[] = $this->Insert( $P->id, $P->partner_id, 2, $type_id, $P->name,
                                $P->alias, $P->negative, $P->parent_id, $P->country_id, $P->created_at, $P->updated_at);
                        }
                    }
                    // 2.2 divison остается divison
                    else{
                        $rows[] = $this->Insert( $P->id, $P->partner_id, 3, $type_id, $P->name . " Sklad",
                            $P->alias . "-sklad", $P->negative, $P->parent_id, $P->country_id, $P->created_at, $P->updated_at);
                    }

                }
                // 1.1 project остается project
                else{
                    $rows[] = $this->Insert($P->id, $P->partner_id, 1, $type_id, $P->name,
                        $P->alias, $P->negative, 0, $P->country_id, $P->created_at, $P->updated_at);
                }
            } // / foreach

            DB::table('projects_new')->insert($rows);
        }
    }

    // формирую строку проектов в масиве для mysql
    public function Insert($id, $partner_id, $type, $type_id, $name, $alias, $negative, $parent_id, $country_id, $created_at, $updated_at){
        return [
            'id' => $id,
            'type' => $type,
            'type_id' => $type_id,
            'parent_id' => $parent_id,
            'name' => $name,
            'alias' => $alias,
            'partner_id' => $partner_id,
            'negative' => $negative,
            'country_id' => $country_id,
            'created_at' => $created_at,
            'updated_at' => $updated_at
        ];
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects_new');
    }
}
