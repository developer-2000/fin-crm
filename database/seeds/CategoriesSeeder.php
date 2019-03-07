<?php

use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'entity'     => 'script',
                'name'       => 'Содержание',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'entity'     => 'script',
                'name'       => 'Возражения',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'entity'     => 'script',
                'name'       => 'Другие вопросы',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'entity'     => 'post',
                'name'       => 'Отдел разработки срм',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'entity'     => 'post',
                'name'       => 'Бухгалтерия',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'entity'     => 'post',
                'name'       => 'Отдел кадров',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'entity'     => 'post',
                'name'       => 'Администрация',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($categories as $category) {
            \App\Models\Category::updateOrCreate(
                [
                    'entity' => $category['entity'],
                    'name'   => $category['name']
                ],
                [
                    'created_at' => $category['created_at'],
                    'updated_at' => $category['updated_at']
                ]
            );
        }
    }
}
