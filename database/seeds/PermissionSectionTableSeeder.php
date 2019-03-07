<?php

use Illuminate\Database\Seeder;

class PermissionSectionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            'orders'         => ['page_orders', 'page_one_order', 'get_calls_page_order', 'buttons_confirm_cancel_bad_connection',
                                 'page_bad_connection', 'page_suspicious_orders', 'moderator_changes', 'page_order_create',
                                 'page_one_order_sending', 'page_requests', 'page_order_create_clone', 'page_print_orders'],
            'monitoring'     => ['page_monitoring'],
            'reports'        => ['page_account', 'grouping_operators_page_account', 'grouping_offers_page_account',
                                 'grouping_projects_page_account', 'page_time_login_logout', 'page_account_talk_time',
                                 'page_account_orders_opened','sales_report_page'],
            'moderation'     => ['page_moderation'],
            'filters'        => ['filter_projects_page_orders', 'filter_companies_page_orders', 'filter_campanies_page_orders',
                                 'filter_proc_status_page_orders', 'filter_target_user_page_orders', 'filter_target_status_page_orders',
                                 'filter_offers_page_orders', 'filter_products_page_orders', 'filter_operators_page_account',
                                 'filter_offer_page_account', 'filter_projects_page_account', 'filter_companies_page_account', 'filter_companies_page_transaction_companies',
                                 'filter_companies_page_transaction_users', 'filter_companies_page_balance_users', 'filter_companies_page_users',
                                 'filter_partners_page_orders', 'filter_sub_projects_page_orders', 'filter_proc_status_2_page_orders',
                                 'filter_deliveries_page_orders', 'filter_add_products_info','filter_track_page_orders'],
            'campaigns'      => ['chenge_campaigns_order', 'page_capmaigns', 'page_campaigns_operators', 'add_new_queue'],
            'logs'           => ['get_logs_page_order', 'read_plan_logs'],
            'finance'        => ['do_payout_companies', 'page_transactions_companies', 'page_payouts_companies',
                                 'do_payout_operators', 'page_transactions_operstors', 'page_payouts_operators', 'page_payouts_operators',
                                 'page_finance_operators', 'page_balance_companies', 'page_setting_offers',
                                 'chenge_up_cross_offers', 'page_offers'],
            'companies'      => ['add_chenge_companies', 'add_chenge_companies_billing'],
            'users'          => ['add_chenge_users', 'add_chenge_sip_users', 'settings_firewall', 'page_roles_and_permissions',
                                 'page_users', 'page_roles', 'add_new_role_ajax'],
            'projects'       => ['page_projects', 'add_project_into_proc_status'],
            'cold_calls'     => ['create_edit_cold_call_offer', 'create_edit_cold_call_list'],
            'plans'          => ['create_edit_plan', 'page_plans_companies', 'page_plan_rates'],
            'support_posts'  => ['page_operator_mistakes', 'close_feedback', 'feedback_add', 'ticket_create'],
            'scripts'        => ['chenge_scripts', 'read_add_script', 'only_read_script'],
            'posts'          => ['post_create_modify', 'page_posts'],
            'ranks'          => ['page_ranks', 'add_new_rank_ajax', 'edit_rank', 'delete_rank'],
            'variables'      => ['page_variables', 'change_variables'],
            'targets'        => ['page_targets', 'page_create_target', 'page_one_target'],
            'permissions'    => ['delegate_permissions'],
            'products'       => ['page_products', 'product_destroy', 'product_edit', 'add_new_product'],
            'sms'            => ['sms_send',],
            'statuses'       => ['delete_status', 'edit_status', 'page_proc_statuses'],
            'integrations'   => ['page_integrations', 'integrations_edit', 'integrations_keys_create', 'integrations_senders',
                                 'sender_address_create', 'novaposhta_print_delivery_note', 'get_document_status', 'set_system_code_status'],
            'storages'       => ['all_storages', 'my_storages', 'all_storage_remainders', 'my_storage_remainders', 'my_movings',
                                 'moving_create'],
            'transactions'   => ['all_transactions', 'my_transactions'],
            'categories'     => ['page_categories'],
            'partners'       => ['add_new_partner_ajax', 'page_partners'],
            'exchange_rates' => ['exchange_rates_page'],
        ];
        foreach ($array as $key => $permissions) {
            foreach ($permissions as $permission) {
                DB::table('permissions')->where('name', $permission)->update(['section' => $key]);
            }
        }
        $aliases = [
            'orders'         => 'Заказы',
            'monitoring'     => 'Мониторинг',
            'reports'        => 'Отчеты',
            'moderation'     => 'Модерация',
            'filters'        => 'Фильтры',
            'campaigns'      => 'Очереди',
            'logs'           => 'Логи',
            'finance'        => 'Финансы',
            'companies'      => 'Компании',
            'users'          => 'Пользователи',
            'projects'       => 'Проекты',
            'cold_calls'     => 'Холодные продажи',
            'plans'          => 'Планы',
            'support_posts'  => 'Служба поддержки',
            'scripts'        => 'Скрипты продаж',
            'posts'          => 'Новости',
            'ranks'          => 'Ранги',
            'variables'      => 'Переменные',
            'targets'        => 'Цели',
            'permissions'    => 'Разрешения',
            'products'       => 'Продукты',
            'statuses'       => 'Статусы',
            'sms'            => 'СМС',
            'integrations'   => 'Интеграции',
            'storages'       => 'Склады',
            'transactions'   => 'Транзакции',
            'categories'     => 'Категории',
            'partners'       => 'Партнеры',
            'exchange_rates' => 'Курсы валют',
        ];
        foreach ($aliases as $key => $alias) {
            DB::table('permissions')->where('section', $key)->update(['section_alias' => $alias]);
        }

    }
}
