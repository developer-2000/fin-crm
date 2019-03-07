<?php

use Illuminate\Database\Seeder;

class TranslateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'translation_group'         => 'Page one group',
            'translation_index'         => 'Page translations',
            'translation_words_add'     => 'Adding new word',
            'translation_edit'          => 'Editing translation',
            'translation_group_add'     => 'Adding new group',
            'translation_delete_word'   => 'Deleting translation',
            'translation_import'        => 'Import translation',
            'translation_find'          => 'Find translation in project',
            'translation_locale_add'    => 'Adding new locale',
            'translation_locale_remove' => 'Deleting locale',
            'translation_publish'       => 'Publishing translation',
            'translation_publish_all'   => 'Publishing all translations',
        ];

        foreach ($permissions as $name => $alias) {
            $permission = [
                'name'          => $name,
                'section'       => 'translation',
                'section_alias' => 'Translation',
                'group'         => null,
                'alias'         => $alias
            ];

            if ($name == 'translation_index') {
                $permission['group'] = 'menu';
            }

            $exists = \App\Models\Permission::where('name', $name)->exists();

            if (!$exists) {
                DB::table('permissions')->insert($permission);
            }
        }


        $statuses = \App\Models\ProcStatus::all();

        $statusTranslate = [
            'В обработке'          => 'in-processing',
            'В наборе'             => 'dialing',
            'Контакт'              => 'contact',
            'Повтор'               => 'repeat',
            'Недозвон'             => 'no-answer',
            'Некорректный номер'   => 'invalid-phone',
            'Другой язык'          => 'other-language',
            'Ошибка'               => 'failure',
            'Завершен'             => 'completed',
            'Подозрительный заказ' => 'suspicious',
            'Некорректный проект'  => 'invalid-project',
            'Хороший клиент'       => 'paid-up',
            'Плохой клиент'        => 'refused',
            'Отклонен'             => 'rejected',
            'В очереди на печать'  => 'in-queued-to-print',
            'Отправлено'           => 'sent',
            'На отделении'         => 'at-department',
            'Забран'               => 'received',
            'Возврат'              => 'returned',
            'Отправка отменена'    => 'sending-canceled',
            'Ошибка API'           => 'error-api',
            'Поиск'                => 'search',
            'Претензия'            => 'claim',
            'Спор'                 => 'dispute',
            'Сторно'               => 'reversal',
        ];

        if ($statuses) {
            foreach ($statuses as $status) {
                if (isset($statusTranslate[$status->name]) && $status->project_id == 0) {
                    $status->key = str_replace('statuses.', '', $statusTranslate[$status->name]);
                    $status->save();
                }
            }
        }
    }
}
