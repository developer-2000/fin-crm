<?php

use Illuminate\Database\Seeder;

class AddedProcStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            [
                'project_id' => 0,
                'id'         => 1,
                'name'       => 'В обработке',
                'locked'     => 1,
                'type'       => 'call_center'
            ],
            [
                'project_id' => 0,
                'id'         => 2,
                'name'       => 'В наборе',
                'locked'     => 1,
                'type'       => 'call_center'
            ],
            [
                'project_id' => 0,
                'id'         => 3,
                'name'       => 'Контакт',
                'locked'     => 1,
                'type'       => 'call_center'
            ],
            [
                'project_id' => 0,
                'id'         => 4,
                'name'       => 'Повтор',
                'locked'     => 1,
                'type'       => 'call_center'
            ],
            [
                'project_id' => 0,
                'id'         => 5,
                'name'       => 'Недозвон',
                'locked'     => 1,
                'type'       => 'call_center'
            ],
            [
                'project_id' => 0,
                'id'         => 6,
                'name'       => 'Некоректный номер',
                'locked'     => 1,
                'type'       => 'call_center'
            ],
            [
                'project_id' => 0,
                'id'         => 7,
                'name'       => 'Другой язык',
                'locked'     => 1,
                'type'       => 'call_center'
            ],
            [
                'project_id' => 0,
                'id'         => 8,
                'name'       => 'Ошибка',
                'locked'     => 1,
                'type'       => 'call_center'
            ],
            [
                'project_id' => 0,
                'id'         => 9,
                'name'       => 'Завершен',
                'locked'     => 1,
                'type'       => 'call_center'
            ],
            [
                'project_id' => 0,
                'id'         => 10,
                'name'       => 'Подозрительный заказ',
                'locked'     => 1,
                'type'       => 'call_center'
            ],
            [
                'project_id' => 0,
                'id'         => 13,
                'name'       => 'Ошибка',
                'locked'     => 1,
                'type'       => 'call_center'
            ],
            [
                'project_id'   => 0,
                'id'           => 20,
                'name'         => 'Выкуп',
                'locked'       => 1,
                'type'         => 'senders',
                'action'       => 'paid-up',
                'target_final' => 1
            ],
            [
                'project_id'   => 0,
                'id'           => 21,
                'name'         => 'Не выкуп',
                'locked'       => 1,
                'type'         => 'senders',
                'action'       => 'refused',
                'target_final' => 2
            ],
            [
                'project_id'   => 0,
                'id'           => 22,
                'name'         => 'Отклонен',
                'locked'       => 1,
                'type'         => 'senders',
                'action'       => 'rejected',
                'target_final' => 3
            ],
            [
                'project_id' => 0,
                'id'         => 23,
                'name'       => 'В очереди на печать',
                'locked'     => 1,
                'type'       => 'senders',
                'action'     => 'to_print'
            ],
            [
                'project_id'   => 0,
                'id'           => 24,
                'name'         => 'Отправлено',
                'locked'       => 1,
                'type'         => 'senders',
                'action'       => 'sent'
            ],
        ];

        foreach ($statuses as $status) {
            \App\Models\ProcStatus::firstOrCreate([
                'id' => $status['id'],
            ], $status);
        }

        $permissions = [
            [
                'name'  => 'page_proc_statuses',
                'group' => 'menu',
                'alias' => 'Все статусы'
            ],
            [
                'name'  => 'page_requests',
                'group' => 'menu',
                'alias' => 'Заявки'
            ],
            [
                'name'  => 'edit_status',
                'group' => null,
                'alias' => 'Редактировать статус'
            ],
            [
                'name'  => 'delete_status',
                'group' => null,
                'alias' => 'Удаление статуса'
            ],
            [
                'name'  => 'filter_proc_status_2_page_orders',
                'group' => null,
                'alias' => 'Фильтр под статусов на странице заказов'
            ],
            [
                'name'  => 'filter_sub_projects_page_orders',
                'group' => null,
                'alias' => 'Фильтр под проектов на странице заказов'
            ],
            [
                'name'  => 'filter_partners_page_orders',
                'group' => null,
                'alias' => 'Фильтр партнеров на странице заказов'
            ],
            [
                'name'  => 'add_project_into_proc_status',
                'group' => null,
                'alias' => 'Выбор проекта при создании статуса'
            ],
            [
                'name'  => 'page_one_order_sending',
                'group' => null,
                'alias' => 'Страница заказа для отправтеля'
            ],
            [
                'name'  => 'page_order_create',
                'group' => null,
                'alias' => 'Страница создания заказа'
            ],
            [
                'name'  => 'sms_send',
                'group' => null,
                'alias' => 'Отправка СМС'
            ],
            [
                'name'  => 'product_destroy',
                'group' => null,
                'alias' => 'Удаление товара'
            ],
            [
                'name'  => 'product_edit',
                'group' => null,
                'alias' => 'Редактирование товара'
            ],
            [
                'name'  => 'page_categories',
                'group' => null,
                'alias' => 'Страница категорий'
            ],
            [

                'name'  => 'page_integrations',
                'group' => null,
                'alias' => 'Страница Всех Интеграций'
            ],
            [
                'name'  => 'integrations_edit',
                'group' => null,
                'alias' => 'Страница интеграции'
            ],
            [
                'name'  => 'integrations_keys_create',
                'group' => null,
                'alias' => 'Страница ключей одной интеграции'
            ],
            [
                'name'  => 'integrations_senders',
                'group' => null,
                'alias' => 'Страница отправителей'
            ],
            [
                'name'  => 'sender_address_create',
                'group' => null,
                'alias' => 'Страница создания адреса отправителя'
            ],
            [
                'name'  => 'novaposhta_print_delivery_note',
                'group' => null,
                'alias' => 'Печать ТТН Новой Почты'
            ],
            [
                'name'  => 'get_document_status',
                'group' => null,
                'alias' => 'Получение статуса отправки'
            ],
            [
                'name'  => 'add_new_partner_ajax',
                'group' => null,
                'alias' => 'Страница добавление '
            ],
            [
                'name'  => 'add_new_product',
                'group' => null,
                'alias' => 'Добавление товара'
            ],
            [
                'name'  => 'page_partners',
                'group' => 'menu',
                'alias' => 'Страница партнеров'
            ],
            [
                'name'  => 'exchange_rates_page',
                'group' => 'menu',
                'alias' => 'Курсы валют'
            ],
            [
                'name'  => 'filter_deliveries_page_orders',
                'group' => null,
                'alias' => 'Фильтр курьерских доставок на странице заказов'
            ],
            [
                'name'  => 'page_print_orders',
                'group' => null,
                'alias' => 'Страница заказов отправленных на печать'
            ],
            [
                'name'  => 'add_new_role_ajax',
                'group' => null,
                'alias' => 'Добавление новой роли'
            ],
            [
                'name'  => 'sales_report_page',
                'group' => 'menu',
                'alias' => 'Отчет Продажи за период'
            ],
            [
                'name'  => 'filter_add_products_info',
                'group' => null,
                'alias' => 'Отображать информацию по товарам'
            ],
            [
                'name'  => 'filter_track_page_orders',
                'group' => null,
                'alias' => 'Фильтр по трекам на странице заказов'
            ],
            [
                'name'  => 'set_system_code_status',
                'group' => null,
                'alias' => 'Установка системных статусов для интеграции'
            ],
            [
                'name'  => 'collectors_buttons',
                'group' => null,
                'alias' => 'Кнопки для коллекторов'
            ],
            [
                'name'  => 'cancel_sending_button',
                'group' => null,
                'alias' => 'Кнопка отмены отправки'
            ],
            [
                'name'  => 'page_collectings',
                'group' => null,
                'alias' => 'Страница дожимов'
            ],
            [
                'name'  => 'page_collectings_processing',
                'group' => null,
                'alias' => 'Страница обработкаи заказов для коллекторов'
            ],
            [
                'name'  => 'page_collectings_hand_processing',
                'group' => null,
                'alias' => 'Страница ручной обработки для коллекторов'
            ],
            [
                'name'  => 'collectors_show_all_hand_orders',
                'group' => null,
                'alias' => 'Показывать все заказы в ручной обработке'
            ],
            [
                'name'  => 'page_collectings_auto_processing',
                'group' => null,
                'alias' => 'Страница автообзвона для коллекторов'
            ],
            [
                'name'  => 'assign_orders_by_collectors',
                'group' => null,
                'alias' => 'Возможность забрасывать заказы для коллекторов'
            ],
            [
                'name'  => 'get_statuses_page_order',
                'group' => null,
                'alias' => 'Просмотр логов по статусам на странице заказа'
            ],

            [
              'name' => 'annul_moderation',
              'group' => null,
              'alias' => 'Аннулировать модерацию'
            ],

            [
              'name' => 'documentations',
              'group' => 'menu',
              'alias' => 'Документация'
            ],

            [
              'name' => 'documentations_show',
              'group' => null,
              'alias' => 'Просмотр документации'
            ],

            [
              'name' => 'documentations_create',
              'group' => null,
              'alias' => 'Добавление документации'
            ],

            [
              'name' => 'documentations_edit',
              'group' => null,
              'alias' => 'Редактирование документации'
            ],

            [
              'name' => 'documentations_destroy',
              'group' => null,
              'alias' => 'Удалениеи документации'
            ],

            [
              'name' => 'posts_feed',
              'group' => 'menu',
              'alias' => 'Лента новостей'
            ],

            [
              'name' => 'posts_settings',
              'group' => null,
              'alias' => 'Настройка новостей'
            ],

            [
              'name' => 'posts_show',
              'group' => null,
              'alias' => 'Просмотр новости'
            ],

            [
              'name' => 'posts_create',
              'group' => null,
              'alias' => 'Создание новости'
            ],

            [
              'name' => 'posts_edit',
              'group' => null,
              'alias' => 'Редактирование новости'
            ],

            [
              'name' => 'posts_destroy',
              'group' => null,
              'alias' => 'Удаление новости'
            ],
        ];

        foreach ($permissions as $permission) {
            $exists = DB::table('permissions')->where('name', $permission['name'])->exists();

            if (!$exists) {
                DB::table('permissions')->insert($permission);
            }
        }
    }
}
