<?php

use Illuminate\Database\Seeder;

class IntegrationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('integrations')->insert([
            [
                'name'  => 'Новая Почта',
                'alias' => 'novaposhta',
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'name'  => 'Почта России',
                'alias' => 'russianpost',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
