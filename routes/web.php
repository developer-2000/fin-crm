<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*Auth routes*/
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::match(['get', 'post'], 'logout', 'Auth\LoginController@logout')->name('logout');

///* Главная страница авторизации */ /* true */
Route::get('/', [
    'as'   => 'index',
    'uses' => 'IndexController@index'
]);

///* Группа для авторизированных пользователей  */ /* true */
Route::group([
    'middleware' => ['auth', 'checkPermissions']
], function () {
    /*USERS ROUTES GROUP*/
    Route::get('/cdek/print/{id}', 'Api\CdekController@print')->where('id', '[0-9]+')->name('cdek-print');
    Route::prefix('users')->group(function () {

        /* Страница управления пользователями */ /* true */
        Route::match(['get', 'post'], '/', [
            'as'         => 'users',
            'uses'       => 'UserController@index',
        ])->middleware('can:page_users');

        /* Страница управления одним пользователем */
        Route::get('/{id}/edit', [
            'as'         => 'users-edit',
            'uses'       => 'UserController@edit',
        ])->where('id', '[0-9]+')->middleware('can:add_chenge_users');

        /* Страница управления правами одного пользователя */
        Route::get('/{id}/edit/permissions', [
            'as'         => 'user-edit-permissions',
            'uses'       => 'UserController@editPermissions',
        ])->where('id', '[0-9]+')->middleware('can:user_permission');

        /**
         * Кабинет пользователя
         */
        Route::match(['get', 'post'], '/{id}/{tab?}', [
            'as'   => 'user',
            'uses' => 'UserController@show',
        ])->where('id', '[0-9]+');

        /**
         * страница рангов
         */
        Route::get('/ranks', [
            'as'         => 'users-ranks',
            'uses'       => 'RankController@usersRanks',
        ])->middleware('can:page_ranks');

        /* Страница управления пользователями */
        Route::get('/registration', [
            'as'   => 'users-registration',
            'uses' => 'UserController@registration'
        ]);

        /**
         * страница роли/пермишин
         */
        Route::get('/role-and-permission', [
            'as'         => 'role-and-permission',
            'uses'       => 'RoleController@rolesPermissions',
        ])->middleware('can:page_roles_and_permissions');

        /**
         * страница всех ролей
         */
        Route::match(['get', 'post'], '/roles', [
            'as'         => 'roles',
            'uses'       => 'RoleController@index',
        ])->middleware('can:page_roles');
    });

    /*ORDERS ROUTES GROUP*/
    Route::prefix('orders')->group(function () {

        /**
         * Страница всех заказов
         */
        Route::match(['get', 'post'], '/', [ 'as' => 'orders', 'uses' => 'OrderController@index', ])
            ->middleware('can:page_orders');

// TEST
        Route::get('/test', [ 'as' => 'test', 'uses' => 'TestController@test', ])
        ->middleware('can:page_orders');

        /**
         * страница создания заказа
         */
        Route::get('/create', [
            'as'         => 'order-create',
            'uses'       => 'OrderController@create',
        ])->middleware('can:page_order_create');

        /**
         * страница создания заказа
         */
        Route::get('/create/clone/{id}', [
            'as'         => 'order-create-clone',
            'uses'       => 'OrderController@create',
        ])->where('id', '[0-9]+')->middleware('can:page_order_create_clone');

        /**
         * Страница одного заказы для отправителя
         */
        Route::get('/{id}', [
            'as'         => 'order-sending',
            'uses'       => 'OrderController@orderSending',
        ])->where('id', '[0-9]+')->middleware('can:page_one_order_sending');

        /**
         * Страница заказов отправленных на печать
         */
        Route::match(['get', 'post'], '/print', [
            'as'         => 'orders-print',
            'uses'       => 'OrderController@ordersPrint',
        ])->middleware('can:page_print_orders');

        /**
         * Реестр заказов готовых к отправке
         */
        Route::match(['get', 'post'], '/print-register', [
            'as'   => 'print-register',
            'uses' => 'OrderController@printRegister'
        ]);

        /**
         * печать реестра заказов готовых к отправке
         */
        Route::match(['get', 'post'], '/print-register', [
            'as'   => 'print-register',
            'uses' => 'OrderController@printRegister'
        ]);

        /**
         * страница изменения проекта в заказе
         */
        Route::get('{id}/change-project', [
            'as'         => 'orders-change-project',
            'uses'       => 'OrderController@changeProject',
        ])->where('id', '[0-9]+')->middleware('can:order-change-project');

        /**
         * подверждение обработки заказа коллектором
         */
        Route::get('collector-processed/{id}', [
            'as'         => 'collector-processed',
            'uses'       => 'CollectingController@collectorProcessed',
        ])->where('id', '[0-9]+')->middleware('can:collectors_buttons');
    });

    /*ORDERS ROUTES GROUP*/
    Route::prefix('collectings')->group(function () {

        /**
         * Страница всех дожимов
         */
        Route::match(['get', 'post'], '/', [
            'as'         => 'collectings',
            'uses'       => 'CollectingController@index',
        ])->middleware('can:page_collectings');

        /**
         * таб заказов-дожимой в обаботке (распределенных)
         */
        Route::match(['get', 'post'], '/processing', [
            'as'         => 'collectings-processing',
            'uses'       => 'CollectingController@processing',
        ])->middleware('can:page_collectings_processing');

        /**
         * таб ручная обработка
         */
        Route::match(['get', 'post'], '/hand-processing', [
            'as'         => 'collectings-hand-processing',
            'uses'       => 'CollectingController@handProcessing',
        ])->middleware('can:page_collectings_hand_processing');

        /**
         * таб авто обработка
         */
        Route::match(['get', 'post'], '/auto-processing', [
            'as'         => 'collectings-auto-processing',
            'uses'       => 'CollectingController@autoProcessing',
        ])->middleware('can:page_collectings_auto_processing');
    });

    Route::prefix('pass')->group(function () {
        /**
         * Страница Проводок
         */
        Route::match(['get', 'post'], '/', [
            'as'         => 'pass',
            'uses'       => 'OrderController@pass',
        ])->middleware('can:page_pass');

        /**
         * Страница одного проводка
         */
        Route::get('/{id}', [
            'as'         => 'pass-one',
            'uses'       => 'OrderController@passOne',
        ])->where('id', '[0-9]+')->middleware('can:page_pass_one');

        /**
         * Страница Выкуп
         */
        Route::get('/redemption', [
            'as'         => 'pass-redemption',
            'uses'       => 'OrderController@passRedemption',
        ])->middleware('can:page_pass_redemption');

        /**
         * Страница не выкуп
         */
        Route::get('/no-redemption', [
            'as'         => 'pass-no-redemption',
            'uses'       => 'OrderController@passNoRedemption',
        ])->middleware('can:page_pass_no_redemption');

        /**
         * Страница отправлен
         */
        Route::get('/sending', [
            'as'         => 'pass-sending',
            'uses'       => 'OrderController@passSending',
        ])->middleware('can:page_pass_sending');

        /**
         * сохранение проводка
         */
        Route::post('/{id}/save', [
            'as'   => 'pass-save',
            'uses' => 'OrderController@passSave'
        ])->where('id', '[0-9]+');

        /**
         * Страница сторно
         */
        Route::post('/reversal', [
            'as'   => 'pass-reversal',
            'uses' => 'PassController@passReversal'
        ]);
    });


//    ================================================================================================
//    ================================================================================================
//    домен/all_orders =======================================================================================
//    ================================================================================================

    Route::prefix('all_orders')->group(function () {

        // все поставки
        Route::match(['get', 'post'], '/', [ 'as' => 'all_orders', 'uses' => 'DeliveryController@all_orders', ])
            ->middleware('can:placing_orders');

        // стр создания поставки
        Route::get('create', [ 'as' => 'all_orders-create', 'uses' => 'DeliveryController@create', ])
            ->middleware('can:placing_orders');

        // выборка ajax из creat_order.js совершает поиск и возвращает искомый товар
        Route::post('get-products-list', [ 'as' => 'all_orders-products-list', 'uses' => 'DeliveryController@ProductsList',])
            ->middleware('can:placing_orders');

        // выборка ajax из creat_order.js возвращает выбраный товар
        Route::post('plus-product', [ 'as' => 'all_orders-plus-product', 'uses' => 'DeliveryController@plusProduct', ])
            ->middleware('can:placing_orders');

        // запрос ajax из creat_order.js создает новый заказ на поставку
        Route::post('add-order', [ 'as' => 'all_orders-add-order', 'uses' => 'DeliveryController@addOrder', ])
            ->middleware('can:placing_orders');

        // стр редактиования заказа
        Route::get('{id}', [ 'as' => 'all_orders-one', 'uses' => 'DeliveryController@oneOrder', ])
            ->where(['id' => '[0-9]+'])
            ->middleware('can:placing_orders');

    });

//    ================================================================================================
//    ================================================================================================
//    ================================================================================================
//    ================================================================================================

    /*ORDERS ROUTES GROUP*/
    Route::prefix('requests')->group(function () {

        /**
         * Страница всех заявок
         */
        Route::match(['get', 'post'], '/', [
            'as'         => 'requests',
            'uses'       => 'OrderController@requests',
        ])->middleware('can:page_requests');

        /**
         * Страница одного заказа
         */
        Route::get('/{id}', [
            'as'         => 'order',
            'uses'       => 'OrderController@edit',
        ])->where('id', '[0-9]+')->middleware('can:page_one_order');

        /**
         * страница модерации
         */
        Route::match(['get', 'post'], '/moderation', [
            'as'         => 'moderation',
            'uses'       => 'OrderController@moderation',
        ])->middleware('can:page_moderation');

        /**
         * страница пре модерации
         */
        Route::match(['get', 'post'], '/pre-moderation', [
            'as'         => 'pre-moderation',
            'uses'       => 'OrderController@preModeration',
        ])->middleware('can:page_moderation');//todo permission

        /**
         * страница плохой связи
         */
        Route::match(['get', 'post'], '/bad-connection', [
            'as'         => 'bad-connection',
            'uses'       => 'OrderController@badConnection',
        ])->middleware('can:page_bad_connection');

        /**
         * страница плохой связи
         */
        Route::match(['get', 'post'], '/suspicious-orders', [
            'as'         => 'suspicious-orders',
            'uses'       => 'OrderController@suspiciousOrders',
        ])->middleware('can:page_suspicious_orders');

        /**
         * Страница одного заказа для модератора
         */
        Route::get('/{id}/manage', [
            'as'   => 'order-one-manage',
            'uses' => 'OrderController@orderOneManage'
        ])->where('id', '[0-9]+');
    });

    /*REPORTS ROUTES GROUP*/
    Route::prefix('reports')->group(function () {
        Route::match(['get', 'post'], '/main', [
            'as'         => 'reports-main',
            'uses'       => 'ReportController@index',
        ])->middleware('can:page_account');

//        Route::match(['get', 'post'], '/calls-detailing', [
//            'as'         => 'reports-calls-detailing',
//            'uses'       => 'CallProgressController@callsDetailing',
//        ])->middleware('can:read_calls_detailing');

        /**
         * страница отчетов по кол-ву минут разговора
         */
        Route::match(['get', 'post'], '/talk-time', [
            'as'         => 'reports-talk-time',
            'uses'       => 'ReportController@talkTime',
        ])->middleware('can:page_account_talk_time');

        Route::match(['get', 'post'], '/time-login-logout', [
            'as'         => 'reports-time-login-logout',
            'uses'       => 'ReportController@timeLoginLogout',
        ])->middleware('can:page_time_login_logout');

        /**
         * страница отчетов по открытым операторам входящим заказам
         */
        Route::match(['get', 'post'], '/orders-opened', [
            'as'         => 'reports-orders-opened',
            'uses'       => 'ReportController@ordersOpened',
        ])->middleware('can:page_account_orders_opened');

        Route::match(['get', 'post'], '/sales', [
            'as'         => 'sales',
            'uses'       => 'ReportController@sales',
        ])->middleware('can:sales_report_page');

        Route::match(['get', 'post'], '/statuses', [
            'as'         => 'report-statuses',
            'uses'       => 'ReportController@statuses',
        ])->middleware('can:page_report_by_statuses');

        Route::match(['get', 'post'], '/collectings', [
            'as'         => 'report-collectings',
            'uses'       => 'ReportController@collectings',
        ])->middleware('can:page_report_by_collectings');

        Route::match(['get', 'post'], '/moderators', [
            'as'         => 'report-moderators',
            'uses'       => 'ReportController@moderators',
        ])->middleware('can:page_report_by_moderators');

        Route::match(['get', 'post'], '/operators', [
            'as'         => 'report-operators',
            'uses'       => 'ReportController@operatorsReport',
        ])->middleware('can:page_report_by_operators');

        Route::match(['get', 'post'], '/verification-orders-operators', [
            'as'         => 'verification-orders--operators',
            'uses'       => 'ReportController@verificationOrdersOperators',
        ])->middleware('can:page_report_verification_orders_operators');

        Route::match(['get', 'post'], '/by-city', [
            'as'         => 'report-by-city',
            'uses'       => 'ReportController@reportByCity',
        ])->middleware('can:page_report_by_city');


        Route::match(['get', 'post'], '/by-counterparties', [
            'as'         => 'report-by-counterparties',
            'uses'       => 'ReportController@reportByCounterparties',
        ])->middleware('can:page_report_by_counterparties');

        Route::get('/get-sales-report-export', [
            'as'   => 'get-sales-report-export',
            'uses' => 'ReportController@getSalesReportExport'
        ]);
    });

    /*FEEDBACKS ROUTES GROUP*/
    Route::prefix('feedbacks')->group(function () {

        /**
         * страница отчетов по открытым операторам входящим заказам
         */
        Route::match(['get', 'post'], '/operator-mistakes', [
            'as'         => 'operator-mistakes',
            'uses'       => 'FeedbackController@index',
        ])->middleware('can:page_operator_mistakes');

        /**
         * страница отчетов по открытым операторам входящим заказам
         */
        Route::match(['get', 'post'], '/success-calls', [
            'as'         => 'success-calls',
            'uses'       => 'FeedbackController@getSuccessCalls',
        ])->middleware('can:page_operator_mistakes');

        /* Страница ошибки оператора согласно ордера*/
        Route::match(['get', 'post'], '/{id}', [
            'as'         => 'feedback',
            'uses'       => 'FeedbackController@show',
        ])->middleware('can:page_operator_mistakes');
    });

    /*MONITORING ROUTES GROUP*/
    Route::prefix('monitoring')->group(function () {
        /**
         * мониторинг->компании
         */
        Route::get('/companies', [
            'as'         => 'monitoring-companies',
            'uses'       => 'MonitoringController@monitoringCompany',
        ])->middleware('can:page_monitoring');

        /**
         * мониторинг->процессинг
         */
        Route::match(['get', 'post'], '/processing', [
            'as'         => 'monitoring-processing',
            'uses'       => 'MonitoringController@processing',
        ])->middleware('can:page_monitoring');
        /**
         * мониторинг->звонков по весу
         */
        Route::match(['get', 'post'], '/orders-by-weight', [
            'as'         => 'monitoring-orders-by-weight',
            'uses'       => 'MonitoringController@monitoringOrdersByWeight',
        ])->middleware('can:page_monitoring');
    });

    /*FINANCE ROUTES GROUP*/
    Route::prefix('finance')->group(function () {

        /**
         * Страница баланса комапний
         */
        Route::match(['get', 'post'], '/balance-companies', [
            'as'         => 'finance-balance-companies',
            'uses'       => 'FinanceController@balanceCompanies',
        ])->middleware('can:page_balance_companies');

        /**
         * Страница транзакций всех комапний
         */
        Route::match(['get', 'post'], '/transactions-companies', [
            'as'         => 'finance-transactions-companies',
            'uses'       => 'FinanceController@transactionsCompanies',
        ])->middleware('can:page_transactions_companies');

        /**
         * Страница выплат всех комапний
         */
        Route::match(['get', 'post'], '/payouts-companies', [
            'as'         => 'finance-payouts-companies',
            'uses'       => 'FinanceController@payoutsCompanies',
        ])->middleware('can:page_payouts_companies');
    });

    /*FINANCE ROUTES GROUP*/
    Route::prefix('finance-users')->group(function () {
        /**
         * страница баланса всех пользователей
         */
        Route::match(['get', 'post'], '/balance-users', [
            'as'         => 'balance-users',
            'uses'       => 'FinanceController@balanceUsers',
        ])->middleware('can:page_finance_operators');

        /**
         * страница транзакций ператоров
         */
        Route::match(['get', 'post'], '/transaction-users', [
            'as'         => 'transaction-users',
            'uses'       => 'FinanceController@transactionUsers',
        ])->middleware('can:page_transactions_operstors');

        /**
         * страница всех выплат операторам
         */
        Route::get('/payouts-users', [
            'as'         => 'payouts-users',
            'uses'       => 'FinanceController@payoutsUsers',
        ])->middleware('can:page_payouts_operators');
    });

    /*PLANS ROUTES GROUP*/
    Route::prefix('plans')->group(function () {
        /**
         * Страница планов
         */
        Route::match(['get', 'post'], '/', [
            'as'         => 'plans',
            'uses'       => 'PlanController@index',
        ])->middleware('can:page_plans_companies');

        /**
         * просмотр/редактирование плана
         */
        Route::match(['get', 'post'], '/{id}', [
            'as'         => 'plan',
            'uses'       => 'PlanController@edit',
        ])->where('id', '[0-9]+')->middleware('can:create_edit_plan');

        /**
         * Страница добавления нормы апрува
         */
        Route::match(['get', 'post'], '/rate-add', [
            'as'         => 'plans-rate-add',
            'uses'       => 'PlanController@rateAdd',
        ])->middleware('can:page_plan_rates');

        /**
         * Страница редактирования нормы
         */
        Route::match(['get', 'post'], '/rates/edit/{link}', [
            'as'         => 'plans-rates-edit',
            'uses'       => 'PlanController@editRate',
        ])->where('link', '[0-9]+')->middleware('can:page_plan_rates');

        /**
         * Удалить норму
         */
        Route::match(['get', 'post'], '/rates/{id}/delete', [
            'as'         => 'plans-rates-delete',
            'uses'       => 'PlanController@deleteRate',
        ])->where('link', '[0-9]+')->middleware('can:page_plan_rates');

        /**
         * страница создания плана
         */
        Route::match(['get', 'post'], '/create', [
            'as'         => 'plans-create',
            'uses'       => 'PlanController@create',
        ])->middleware('can:create_edit_plan');

        /**
         * Страница норм
         */
        Route::match(['get', 'post'], '/rates', [
            'as'         => 'plans-rates',
            'uses'       => 'PlanController@rates',
        ])->middleware('can:page_plan_rates');

        /**
         * тестирование плана
         */
        Route::match(['get', 'post'], '/test/{id}', [
            'as'         => 'plans-test-one',
            'uses'       => 'PlanController@test',
        ])->where('id', '[0-9]+')->middleware('can:create_edit_plan');

        /**
         * страница логов по планам
         */
        Route::match(['get', 'post'], '/logs', [
            'as'         => 'plans-logs',
            'uses'       => 'PlanLogController@show',
        ])->middleware('can:read_plan_logs');
    });

    /*COLD-CALLS ROUTES GROUP*/
    Route::prefix('cold-calls')->group(function () {
        /**
         * Страница холодных продаж
         */

        Route::match(['get', 'post'], '/lists', [
            'as'         => 'cold-calls-lists',
            'uses'       => 'ColdCallListController@index',
        ])->middleware('can:create_edit_cold_call_list');

        /**
         * Страница одного листа холодных продаж
         */

        Route::match(['get', 'post'], '/lists/{id}', [
            'as'         => 'cold-calls-lists-edit',
            'uses'       => 'ColdCallListController@show',
        ])->where('id', '[0-9]+')->middleware('can:create_edit_cold_call_list');

        /**
         * Страница статистической информации по листу ХП
         */

        Route::match(['get', 'post'], '/lists/info/{id}', [
            'as'         => 'cold-calls-lists-info',
            'uses'       => 'ColdCallListController@info',
        ])->middleware('can:create_edit_cold_call_list');

        /**
         * Страница создания/редактирования очередей к холодных продажам
         */

        Route::match(['get', 'post'], '/campaigns', [
            'as'         => 'cold-calls-campaigns',
            'uses'       => 'ColdCallListController@getCampaigns',
        ])->middleware('can:create_edit_cold_call_list');

        /**
         * отображение формы
         */
        Route::match(['get', 'post'], '/campaigns/create', [
            'as'         => 'cold-calls-campaigns-create',
            'uses'       => 'ColdCallListController@createCampaign',
        ])->middleware('can:create_edit_cold_call_list');

        /**
         * Страница настройки офферов для ХП
         */
        Route::match(['get', 'post'], '/offers', [
            'as'         => 'cold-call-offers',
            'uses'       => 'ColdCallListController@offers',
        ])->middleware('can:create_edit_cold_call_offer');

        /**
         * настройка операторов в очередях холодных продаж
         */
        Route::match(['get', 'post'], '/operators-settings', [
            'as'         => 'cold-calls-operators-settings',
            'uses'       => 'ColdCallListController@setOperators',
        ])->middleware('can:create_edit_cold_call_list');

        /**
         * создание офеера ХП
         */
        Route::match(['get', 'post'], '/offer/create', [
            'as'         => 'cold-calls-offers-create',
            'uses'       => 'ColdCallListController@createOffer',
        ])->middleware('can:create_edit_cold_call_offer');

        /**
         * редактирование офеера ХП
         */
        Route::match(['get', 'post'], '/offers/{id}', [
            'as'         => 'cold-call-offer',
            'uses'       => 'ColdCallListController@oneOffer',
        ])->middleware('can:create_edit_cold_call_offer');

        /**
         * Страница холодных продаж
         */
        Route::match(['get', 'post'], '/import', [
            'as'         => 'cold-calls-import',
            'uses'       => 'ColdCallListController@getImport',
        ])->middleware('can:create_edit_cold_call_list');

        Route::match(['get', 'post'], '/import_parse', [
            'as'         => 'cold-calls-import-parse',
            'uses'       => 'ColdCallListController@parseImport',
        ])->middleware('can:create_edit_cold_call_list');

        /**
         * Страница холодных продаж
         */
        Route::match(['get', 'post'], '/moderation', [
            'as'         => 'cold-calls-moderation',
            'uses'       => 'ColdCallListController@moderation',
        ])->middleware('can:cold_calls_moderation');

        /**
         * модерация заказов
         */
        Route::post('/cold-calls-moderation-order-ajax/{id}', [
            'as'   => 'cold-calls-moderation-order-ajax',
            'uses' => 'ColdCallListController@moderationOrderAjax'
        ])->where('id', '[0-9]+');
    });

    /*COMPANIES ROUTES GROUP*/
    Route::prefix('companies')->group(function () {
        /**
         * страница всех компаний
         */
        Route::get('/', [
            'as'         => 'companies',
            'uses'       => 'CompanyController@index',
        ])->middleware('can:add_chenge_companies');

        /**
         * регистрация новой компании
         */
        Route::match(['get', 'post'], '/registration', [
            'as'         => 'companies-registration',
            'uses'       => 'CompanyController@registration',
        ])->middleware('can:add_chenge_companies');

        /**
         * редактирование компаний
         */
        Route::match(['get', 'post'], '/{id}', [
            'as'         => 'company',
            'uses'       => 'CompanyController@edit',
        ])->where('id', '[0-9]+')->middleware('can:add_chenge_companies');
    });

    /*OFFERS ROUTES GROUP*/
    Route::prefix('offers')->group(function () {

        /* Страница редактирования предложения */
        Route::match(['get', 'post'], '/', [
            'as'         => 'offers',
            'uses'       => 'OfferController@index',
        ])->middleware('can:page_offers');

        /* Страница редактирования оффера */
        Route::get('/{id}', [
            'as'         => 'offer',
            'uses'       => 'OfferController@edit',
        ])->where('id', '[0-9]+')->middleware('can:page_setting_offers');
    });

    /*CAMPAIGNS ROUTES GROUP*/
    Route::prefix('campaigns')->group(function () {

        /**
         * Отображение всех company elastix
         */
        Route::get('/', [
            'as'         => 'campaigns',
            'uses'       => 'CampaignController@index',
        ])->middleware('can:page_capmaigns');

        /**
         * отображение формы
         */
        Route::match(['get', 'post'], '/create', [
            'as'         => 'campaigns-create',
            'uses'       => 'CampaignController@companyElatixAddView',
        ])->middleware('can:add_chenge_companies');

        /**
         * Страница назначеная операторов по группам
         */
        Route::get('/campaigns-operators', [
            'as'         => 'campaigns-operators',
            'uses'       => 'CampaignController@campaignsOperators',
        ])->middleware('can:page_campaigns_operators');

    });

    /*POSTS ROUTES GROUP*/

    Route::prefix('posts')->group(function () {

        Route::get('/', [
            'uses'       => 'PostController@index',
        ])->middleware('can:posts_feed');


        Route::get('/settings', [
            'uses'       => 'PostController@settings',
        ])->name('posts.settings')->middleware('can:posts_settings');


        Route::post('/search', 'PostController@search')->name('posts.search');
        Route::get('/create', [
            'uses'       => 'PostController@create',
        ])->name('posts.create')->middleware('can:posts_create');
        Route::get('/show/{id}', [
            'uses' => 'PostController@show',
        ])->name('posts.show')->middleware('can:posts_show');
        Route::get('/edit/{id}', [
            'uses' => 'PostController@edit',
        ])->name('posts.edit')->middleware('can:posts_edit');
        Route::post('/update', 'PostController@update');
        Route::post('/store', 'PostController@store');
        Route::post('/public/search', 'PostController@searchPublic');
        Route::post('/familiar/set', 'PostController@setFamiliar');
        Route::post('/active/change', 'PostController@changeActivity');
        Route::delete('/destroy/{id}', [
            'uses'       => 'PostController@destroy',
        ])->name('posts.destroy')->middleware('can:posts_destroy');
    });

    /*Scripts ROUTES GROUP*/

    Route::prefix('scripts')->group(function () {

        /* Страница всех скриптов  */
        Route::match(['get', 'post'], '/', [
            'as'         => 'scripts',
            'uses'       => 'ScriptController@index',
        ])->middleware('can:page_offers');

        /* Страница скриптов к офферу */
        Route::match(['get', 'post'], '/offers/{id}/', [
            'as'         => 'scripts-offers',
            'uses'       => 'ScriptController@scriptsByOffer',
        ])->where('id', '[0-9]+')->middleware('can:read_add_script');

        /* Страница добавления скрипта к офферу */
        Route::match(['get', 'post'], '/{scriptId}/blocks/create', [
            'as'         => 'scripts-blocks-create',
            'uses'       => 'ScriptController@blockCreate',
        ])->where('id', '[0-9]+')->middleware('can:read_add_script');

        /* Страница добавления скрипта к офферу */
        Route::match(['get', 'post'], '/create', [
            'as'         => 'scripts-create',
            'uses'       => 'ScriptController@create',
        ])->where('id', '[0-9]+')->middleware('can:read_add_script');

        /* Страница редактирования скрипта к офферу */
        Route::match(['get', 'post'], '/{scriptId}/edit', [
            'as'         => 'scripts-edit',
            'uses'       => 'ScriptController@edit',
        ])->where('id', '[0-9]+')->middleware('can:read_add_script');

        /* Страница предварительного просмотра скрипта к офферу */
        Route::match(['get', 'post'], '/{id}', [
            'as'         => 'script-show',
            'uses'       => 'ScriptController@show',
        ])->where('offerId', '[0-9]+')->middleware('can:only_read_script');
    });

    /*PROJECTS ROUTES GROUP*/
    Route::group(['prefix' => 'projects'], function () {
        /**
         * страница со всеми проектами
         */
        Route::get('/', [
            'as'         => 'projects',
            'uses'       => 'ProjectController@projects',
        ])->middleware('can:page_projects');
        Route::get('{id}', [
            'uses'       => 'ProjectController@show',
            'as'         => 'project-show',
        ])->where(['id' => '[0-9]+'])->middleware('can:page_projects');

        Route::get('{project_id}/subproject/create', [
            'uses'       => 'ProjectController@subCreate',
            'as'         => 'subproject-create',
        ])->where(['project_id' => '[0-9]+'])->middleware('can:page_projects');

        Route::get('{id}/edit', [
            'uses'       => 'ProjectController@edit',
            'as'         => 'project-edit',
        ])->where(['id' => '[0-9]+'])->middleware('can:page_projects');

        Route::post('/{id}/update', [
            'uses'       => 'ProjectController@update',
            'as'         => 'project-update',
        ])->where(['id' => '[0-9]+'])->middleware('can:page_projects');
    });

    Route::post('/subproject/store', [
        'uses'       => 'ProjectController@subStore',
        'as'         => 'subproject-store',
    ])->where(['project_id' => '[0-9]+'])->middleware('can:page_projects');


//    ================================================================================================
//    ================================================================================================
//    домен/storages =======================================================================================
//    ================================================================================================


    Route::group(['prefix' => 'storages'], function () {

        Route::get('/', [ 'as' => 'storages', 'uses' => 'StorageController@all', ])
            ->middleware('can:storages');

        Route::post('get-products-list', [ 'as' => 'storage-get-products-list', 'uses' => 'StorageController@getProductsList', ])
            ->middleware('can:storages');

        Route::get('{id}', [ 'as' => 'storage', 'uses' => 'StorageController@storage', ])
            ->where(['id' => '[0-9]+'])
            ->middleware('can:my_storages');

        //    ================================================================================================
        //    ================================================================================================
        //    ================================================================================================
        //    ================================================================================================

        Route::group(['prefix' => 'remainders'], function () {

            Route::get('/', [ 'as' => 'all-storage-remainders', 'uses' => 'StorageController@allRemainders', // смотреть "остатки" всех складов
            ])->middleware('can:all_storage_remainders');

            Route::get('{id}', [ 'as' => 'storage-remainders', 'uses' => 'StorageController@remainder', ])
                ->where(['id' => '[0-9]+'])
                ->middleware('can:my_storage_remainders');

            Route::post('/', [ 'as' => 'post-storage-remainders', 'uses' => 'StorageController@postRemainder', ])
                ->middleware('can:my_storage_remainders');

        });

        //    ================================================================================================
        //    ================================================================================================
        //    домен/storages/movings =================================================================================
        //    ================================================================================================

        Route::group(['prefix' => 'movings'], function () {

            Route::get('/', [ 'as' => 'movings', 'uses' => 'MovingController@movings', ])
                ->middleware('can:movings');

            // показывает свойство созданного пермещения товара
            Route::get('{id}', [ 'as' => 'moving', 'uses' => 'MovingController@one', ])
                ->where(['id' => '[0-9]+'])
                ->middleware('can:my_movings');


            // стр создания перемещения в складе
            Route::get('create', [ 'as' => 'moving-create', 'uses' => 'MovingController@create', ])
                ->middleware('can:moving_create');


            // выборка ajax из moving_creat.js отправка на регистрацию перемещения товара с данными
            Route::post('store', [ 'as' => 'moving-store', 'uses' => 'MovingController@store', ])
                ->middleware('can:moving_create');

            Route::post('move', [ 'as' => 'moving-move', 'uses' => 'MovingController@move', ])
                ->middleware('can:moving_create');

            Route::post('comment', [ 'as' => 'moving-comment', 'uses' => 'MovingController@comment', ])
                ->middleware('can:moving_create');

            Route::post('arrived', [ 'as' => 'moving-arrived', 'uses' => 'MovingController@arrived', ])
                ->middleware('can:moving_create');

            Route::post('close/{id}', [ 'as' => 'moving-close', 'uses' => 'MovingController@close', ])
                ->where(['id' => '[0-9]+'])
                ->middleware('can:moving_create');


            // выборка ajax из moving_creat.js отображая подпроекты
            Route::post('get-storages', [ 'as' => 'moving-get-storages', 'uses' => 'MovingController@getStorages', ])
                ->middleware('can:moving_create');

            // выборка ajax из moving_creat.js отображая мои склады
            Route::post('get-my-storages', [ 'as' => 'moving-get-my-storages', 'uses' => 'MovingController@getMyStorages', ])
                ->middleware('can:moving_create');

            // выборка ajax из moving_creat.js отображая на какие склады первод
            Route::post('get-to-storages', [ 'as' => 'moving-get-to-storages', 'uses' => 'MovingController@getToStorages', ])
                ->middleware('can:moving_create');


            // выборка ajax из moving_creat.js отображая select поиска товара
            Route::post('get-products', [ 'as' => 'moving-get-products', 'uses' => 'MovingController@getProducts', ])
                ->middleware('can:moving_create');


            // выборка ajax из moving_creat.js совершает поиск и возвращает искомый товар
            Route::post('get-products-list', [ 'as' => 'moving-get-products-list', 'uses' => 'MovingController@getProductsList',])
                ->middleware('can:moving_create');


            // выборка ajax из moving_creat.js возвращает выбраный товар
            Route::post('plus-product', [ 'as' => 'moving-plus-product', 'uses' => 'MovingController@plusProduct', ])
                ->middleware('can:moving_create');



            Route::post('minus-product', [ 'as' => 'moving-minus-product', 'uses' => 'MovingController@minusProduct', ])
                ->middleware('can:moving_create');

            Route::post('change-date/{id}', [ 'as' => 'moving-change-date', 'uses' => 'MovingController@changeDate', ])
                ->where(['id' => '[0-9]+'])
                ->middleware('can:moving_create');

        });

    //    ================================================================================================
    //    ================================================================================================
    //    ================================================================================================
    //    ================================================================================================

        Route::group(['prefix' => 'transactions'], function () {
            Route::get('/', [
                'as'         => 'transactions',
                'uses'       => 'StorageTransactionController@all',
            ])->middleware('can:transactions');

            Route::post('get-products-list2', [
                'as'         => 'transaction-get-products-list2',
                'uses'       => 'StorageTransactionController@getProductsList2',
            ])->middleware('can:transactions');
            Route::get('create', [
                'as'         => 'transaction-create',
                'uses'       => 'StorageTransactionController@create',
            ])->middleware('can:manual_transaction');
            Route::post('store', [
                'as'         => 'transaction-store',
                'uses'       => 'StorageTransactionController@store',
            ])->middleware('can:manual_transaction');
            Route::post('get-storages', [
                'as'         => 'transaction-get-storages',
                'uses'       => 'StorageTransactionController@getStorages',
            ])->middleware('can:manual_transaction');
            Route::post('get-products', [
                'as'         => 'transaction-get-products',
                'uses'       => 'StorageTransactionController@getProducts',
            ])->middleware('can:manual_transaction');
            Route::post('get-products-list', [
                'as'         => 'transaction-get-products-list',
                'uses'       => 'StorageTransactionController@getProductsList',
            ])->middleware('can:manual_transaction');
            Route::post('get-product', [
                'as'         => 'transaction-get-product',
                'uses'       => 'StorageTransactionController@getProduct',
            ])->middleware('can:manual_transaction');
        });
    });

    Route::group(['prefix' => 'operations'], function () {
        Route::match(['get', 'post'], '/', [
            'as'         => 'operations',
            'uses'       => 'OperationController@index',
        ])->middleware('can:operations_all');
    });

    /*PRODUCTS ROUTES GROUP*/
    Route::prefix('products')->group(function () {
        /**
         * страница со всеми продуктами
         */
        Route::match(['get', 'post'], '/', [
            'as'         => 'products',
            'uses'       => 'ProductController@index',
        ])->middleware('can:page_products');

        /**
         * страница редактирования товара
         */
        Route::match(['get', 'post'], '/{product}', [
            'as'         => 'products-edit',
            'uses'       => 'ProductController@edit',
        ])->where(['id', '[0-9]+'])->middleware('can:product_edit');
    });

    /*TARGETS ROUTES GROUP*/
    Route::prefix('targets')->group(function () {
        Route::get('/', [
            'as'         => 'targets',
            'uses'       => 'TargetController@index',
        ])->middleware('can:page_targets');

        /**
         * страница создания заказа
         */
        Route::get('/create', [
            'as'         => 'targets-create',
            'uses'       => 'TargetController@create',
        ])->middleware('can:page_create_target');

        Route::get('/{id}', [
            'as'         => 'target',
            'uses'       => 'TargetController@edit',
        ])->where(['id', '[0-9]+'])->middleware('can:page_one_target');

    });

    Route::prefix('partners')->group(function () {
        Route::get('/', [
            'as'         => 'partners',
            'uses'       => 'PartnerController@index',
        ])->middleware('can:page_partners');
    });

    Route::prefix('statuses')->group(function () {
        Route::get('/', [
            'as'         => 'statuses-index',
            'uses'       => 'ProcStatusController@index',
        ])->middleware('can:page_proc_statuses');
    });

    Route::prefix('categories')->group(function () {
        Route::match(['get', 'post'], '/', [
            'as'         => 'categories',
            'uses'       => 'CategoryController@index',
        ])->middleware('can:page_categories');
    });

    /*VARIABLES ROUTES GROUP*/
    Route::prefix('variables')->group(function () {
        Route::get('/', [
            'as'         => 'variables',
            'uses'       => 'VariableController@index',
        ])->middleware('can:page_variables');
    });

    /*EXCHANGE RATES ROUTES GROUP*/
    Route::prefix('exchange_rates')->group(function () {
        Route::get('/', [
            'as'         => 'exchange_rates',
            'uses'       => 'ExchangeRateController@index',
        ])->middleware('can:exchange_rates_page');
    });

    /*INTEGRATIONS ROUTES GROUP*/
    Route::prefix('integrations')->group(function () {
        Route::get('/', [
            'as'         => 'integrations',
            'uses'       => 'Api\IntegrationController@index',
        ])->middleware('can:page_integrations');

        Route::match(['get', 'post'], '/{alias}/codes-statuses', [
            'as'         => 'integration-codes-statuses',
            'uses'       => 'Api\IntegrationController@codesStatuses',
        ])->middleware('can:integration_codes_statuses');

        /**
         * kazpost
         */
        Route::prefix('kazpost')->group(function () {
            Route::get('/senders/{id}', [
                'as'   => 'kazpost-edit-sender',
                'uses' => 'Api\KazpostController@editSender'
            ])->where('id', '[0-9]+');

            Route::get('/sticker2/{orderId}', [
                'as'   => 'kazpost-get-sticker2',
                'uses' => 'Api\KazpostController@sticker2'
            ])->where('orderId', '[0-9]+');

            Route::get('/stickers2/{ordersIds}', [
                'as'   => 'kazpost-get-stickers2',
                'uses' => 'Api\KazpostController@stickers2'
            ]);

            Route::get('/blank/{orderId}', [
                'as'   => 'kazpost-get-blank',
                'uses' => 'Api\KazpostController@blank'
            ]);

            Route::get('/get-registry', [
                'as'   => 'kazpost-get-registry',
                'uses' => 'Api\KazpostController@getRegistry'
            ]);
        });

        /**
         * russianpost
         */
        Route::prefix('russianpost')->group(function () {
            Route::get('/senders/{id}', [
                'as'   => 'russianpost-edit-sender',
                'uses' => 'Api\RussianpostController@editSender'
            ])->where('id', '[0-9]+');

            Route::get('/sticker2/{orderId}/{senderId}', [
                'as'   => 'russianpost-get-sticker2',
                'uses' => 'Api\RussianpostController@sticker2'
            ])->where('orderId', '[0-9]+');

            Route::get('/stickers2/{ordersIds}', [
                'as'   => 'russianpost-get-stickers2',
                'uses' => 'Api\RussianpostController@stickers2'
            ]);

            Route::get('/blank_113/{ordersId}/{senderId}', [
                'as'   => 'russianpost-get-blank_113',
                'uses' => 'Api\RussianpostController@blank_113'
            ]);

            Route::get('/blank_107/{ordersId}/{senderId}', [
                'as'   => 'russianpost-get-blank_107',
                'uses' => 'Api\RussianpostController@blank_107'
            ]);
            Route::get('/blank_7/{ordersId}/{senderId}', [
                'as'   => 'russianpost-get-blank_7',
                'uses' => 'Api\RussianpostController@blank_7'
            ]);
        });
        /**
         * CDEK
         */


        Route::prefix('cdek')->group(function () {

            Route::get('/edit/{id}', [
                'as'   => 'cdek-edit-key',
                'uses' => 'Api\CdekController@editKey'
            ])->where('id', '[0-9]+');

        });

        /**
         * Ninjaxpress Post
         */
        Route::prefix('ninjaxpress')->group(function () {
            Route::get('/edit/{ninjaxpress_key}', [
                'as'   => 'ninjaxpress-edit-key',
                'uses' => 'Api\NinjaxpressController@editKey'
            ]);

            /*print ninjaxpress deliveryNote*/
            Route::get('/ninjaxpress-delivery-note-print/{key}/{number}', [
                'as'   => 'ninjaxpress-delivery-note-print',
                'uses' => 'Api\NinjaxpressController@printDocument',
                //  'permission' => 'ninjaxpress_print_delivery_note'
            ]);
        });


        /**
         * Viettel Post
         */
        Route::prefix('viettel')->group(function () {
            Route::get('/edit/{id}', [
                'as'   => 'viettel-edit-key',
                'uses' => 'Api\ViettelController@editKey'
            ])->where('id', '[0-9]+');
        });

        /**
         * Novaposhta Post
         */
        Route::prefix('novaposhta')->group(function () {
            Route::match(['get', 'post'], '/senders', [
                'as'         => 'novaposhta-senders',
                'uses'       => 'Api\NovaposhtaController@senders',
            ])->middleware('can:integrations_senders');
        });

        Route::get('/{alias}/edit', [
            'as'         => 'integrations-edit',
            'uses'       => 'Api\IntegrationController@edit',
        ])->middleware('can:integrations_edit');

        Route::get('/{id}/keys/create', [
            'as'         => 'integrations-keys-create',
            'uses'       => 'Api\IntegrationController@keyCreate',
        ])->middleware('can:integrations_keys_create');

        Route::match(['get', 'post'], '{alias}/senders/{id}/', [
            'as'         => 'sender',
            'uses'       => 'Api\NovaposhtaController@senderEdit',
        ])->where('id', '[0-9]+')->middleware('can:integrations_senders');

        Route::match(['get', 'post'], '/senders/{id}/addresses/create', [
            'as'         => 'sender-address-create',
            'uses'       => 'Api\IntegrationController@senderAddressCreate',
        ])->where('id', '[0-9]+')->middleware('can:integrations_senders');

        /*print novaPoshta deliveryNote*/
        Route::get('/novaposhta-delivery-note-print/{number}', [
            'as'         => 'novaposhta-delivery-note-print',
            'uses'       => 'Api\NovaposhtaController@printDocument',
        ])->middleware('can:novaposhta_print_delivery_note');

        /*print novaPoshta markings*/
        Route::get('/novaposhta-markings-print/{number}', [
            'as'         => 'novaposhta-markings-print',
            'uses'       => 'Api\NovaposhtaController@printMarkings',
        ])->middleware('can:novaposhta_print_delivery_note');

        /*print novaPoshta markings Zebra*/
        Route::get('/novaposhta-markings-zebra-print/{number}', [
            'as'         => 'novaposhta-markings-zebra-print',
            'uses'       => 'Api\NovaposhtaController@printMarkingsZebra',
        ])->middleware('can:novaposhta_print_delivery_note');

        /*print novaPoshta deliveryNote*/
        Route::get('/delivery-note-print-all/{tracks}/{alias}/{ordersIds}', [
            'as'   => 'delivery-note-print-all',
            'uses' => 'Api\NovaposhtaController@printAllDocuments',
        ]);

        /*print novaPoshta markings*/
        Route::get('/markings-print-all/{tracks}/{alias}', [
            'as'   => 'markings-print-all',
            'uses' => 'Api\NovaposhtaController@printAllMarkings',
        ]);

        /*print novaPoshta markings Zebra*/
        Route::get('/markings-zebra-print-all/{tracks}/{alias}', [
            'as'   => 'markings-zebra-print-all',
            'uses' => 'Api\NovaposhtaController@printAllMarkingsZebra',
        ]);

        /**
         * роуты wefast
         */
        Route::prefix('wefast')->group(function () {
            /**
             * страница контрагентов для wefast
             */
            Route::get('/counterparties', [
                'as'         => 'wefast-counterparties',
                'uses'       => 'Api\WeFastController@wefastCounterparties',
            ])->middleware('can:integrations_edit');

            /**
             * страница редактирования контагентов
             */
            Route::get('counterparties/{id}', [
                'as'         => 'wefast-counterparties-edit',
                'uses'       => 'Api\WeFastController@counterpartiesEdit',
            ])->where('id', '[0-9]+')->middleware('can:integrations_edit');

            /**
             * страница отделений
             */
            Route::get('/offices', [
                'as'         => 'wefast-offices',
                'uses'       => 'Api\WeFastController@offices',
            ])->middleware('can:integrations_edit');
        });
    });

    /*SMS ROUTES GROUP*/
    Route::prefix('sms')->group(function () {
        /**
         * страница настроек смс-рассылки
         */
        Route::match(['get', 'post'], '/', [
            'as'         => 'sms',
            'uses'       => 'SmsController@index',
        ])->middleware('can:sms_template_page');
    });

    Route::prefix('menu')->group(function () {
        Route::get('/', [
            'as'         => 'menu.index',
            'uses'       => 'MenuController@index',
        ])->middleware('can:page_menu');

        Route::delete('/{menu}', [
            'as'         => 'menu.destroy',
            'uses'       => 'MenuController@destroy',
        ])->middleware('can:page_menu');

        Route::post('/store', [
            'as'         => 'menu.store',
            'uses'       => 'MenuController@store',
        ])->middleware('can:page_menu');

        Route::post('/change-position', [
            'as'         => 'menu.change_position',
            'uses'       => 'MenuController@changePosition',
        ])->middleware('can:page_menu');

        Route::get('/edit/{menu}', [
            'as'         => 'menu.edit',
            'uses'       => 'MenuController@edit',
        ])->middleware('can:page_menu');

        Route::put('/{menu}', [
            'as'         => 'menu.update',
            'uses'       => 'MenuController@update',
        ])->middleware('can:page_menu');
    });

    /*AJAX ROUTES GROUP*/
    Route::prefix('ajax')->group(function () {

        Route::prefix('categories')->group(function () {
            /**
             * создание категории
             */
            Route::post('/create', [
                'as'   => 'category-create-ajax',
                'uses' => 'CategoryController@createAjax'
            ]);

            /**
             * редактирование категории
             */
            Route::post('/edit', [
                'as'   => 'category-edit-ajax',
                'uses' => 'CategoryController@editAjax'
            ]);

            /**
             * изменение позиций
             */
            Route::post('/change-position', [
                'as'   => 'categories-change-position-ajax',
                'uses' => 'CategoryController@changePositionAjax'
            ]);

            /**
             * удаление категорий
             */
            Route::post('/delete', [
                'as'   => 'categories-delete-ajax',
                'uses' => 'CategoryController@deleteAjax'
            ]);

        });

        Route::match(['get', 'post'], '/change-operator-queues-elastix/', [
            'as'   => 'change-operator-queues-elastix',
            'uses' => 'CampaignController@changeOperatorQueuesElastixAjax'
        ]);

        Route::prefix('integrations')->group(function () {
            Route::prefix('wefast')->group(function () {
                Route::post('create-token', [
                    'as'   => 'wefast-create-token',
                    'uses' => 'Api\WeFastController@wefastCreateTokenAjax'
                ]);

                Route::post('change-status-key', [
                    'as'   => 'wefast-change-status-key',
                    'uses' => 'Api\WeFastController@changeStatusKeyAjax'
                ]);

                Route::post('edit-key', [
                    'as'   => 'wefast-edit-key',
                    'uses' => 'Api\WeFastController@editKeyAjax'
                ]);

                Route::post('delete-key', [
                    'as'   => 'wefast-delete-key',
                    'uses' => 'Api\WeFastController@deleteKeyAjax'
                ]);

                Route::post('import-offices', [
                    'as'   => 'wefast_import_offices',
                    'uses' => 'Api\WeFastController@importOffices'
                ]);

                Route::post('create-counterparty', [
                    'as'   => 'wefast-create-counterparty',
                    'uses' => 'Api\WeFastController@createCounterpartyAjax'
                ]);

                Route::post('change-status-counterparty', [
                    'as'   => 'wefast-change-status-counterparty',
                    'uses' => 'Api\WeFastController@changeStatusCounterpartyAjax'
                ]);

                Route::post('counterparties/edit', [
                    'as '  => 'wefast-counterparties-edit-ajax',
                    'uses' => 'Api\WeFastController@counterpartiesEditAjax'
                ]);

                Route::post('counterparties/delete', [
                    'as '  => 'wefast-counterparties-delete-ajax',
                    'uses' => 'Api\WeFastController@counterpartiesDeleteAjax'
                ]);

                Route::get('find/province', [
                    'as'   => 'wefast-find-province',
                    'uses' => 'Api\WeFastController@findProvince'
                ]);

                Route::get('find/district', [
                    'as'   => 'wefast-find-district',
                    'uses' => 'Api\WeFastController@findDistrict'
                ]);

                Route::get('find/ward', [
                    'as'   => 'wefast-find-ward',
                    'uses' => 'Api\WeFastController@findWard'
                ]);
                Route::get('find/key', [
                    'as'   => 'wefast-find-key',
                    'uses' => 'Api\WeFastController@findKey'
                ]);
            });

            Route::prefix('kazpost')->group(function () {
                Route::post('/add-sender', [
                    'as'   => 'kazpost-add-sender-ajax',
                    'uses' => 'Api\KazpostController@addSenderAjax'
                ]);

                Route::post('/edit-sender/{id}', [
                    'as'   => 'kazpost-edit-sender-ajax',
                    'uses' => 'Api\KazpostController@editSenderAjax'
                ])->where('id', '[0-9]+');
            });

            Route::prefix('russianpost')->group(function () {
                Route::post('/add-sender', [
                    'as'   => 'russianpost-add-sender-ajax',
                    'uses' => 'Api\RussianpostController@addSenderAjax'
                ]);

                Route::post('/edit-sender/{id}', [
                    'as'   => 'russianpost-edit-sender-ajax',
                    'uses' => 'Api\RussianpostController@editSenderAjax'
                ])->where('id', '[0-9]+');
            });

            Route::prefix('measoft')->group(function () {

                Route::post('/add-sender', [
                    'as'   => 'measoft-add-sender-ajax',
                    'uses' => 'Api\MeasoftController@addSenderAjax'
                ]);

                Route::post('/edit-sender', [
                    'as'   => 'measoft-edit-sender-ajax',
                    'uses' => 'Api\MeasoftController@editSenderAjax',
                ]);

                Route::post('/delete-sender', [
                    'as'   => 'measoft-delete-sender-ajax',
                    'uses' => 'Api\MeasoftController@deleteSenderAjax',
                ]);

                Route::get('/find/town', [
                    'as'   => 'measoft-find-town-ajax',
                    'uses' => 'Api\MeasoftController@findTowns'
                ]);

                Route::get('/find/street', [
                    'as'   => 'measoft-find-street-ajax',
                    'uses' => 'Api\MeasoftController@findStreets'
                ]);
            });
        });

        /**
         * создание подстатуса
         */
        Route::post('/statuses/add-sub-status', [
            'as'   => 'status-add-sub-status',
            'uses' => 'ProcStatusController@addSubStatus'
        ]);

        /**
         * удаление статуса
         */
        Route::post('/statuses/delete', [
            'as'   => 'status-delete',
            'uses' => 'ProcStatusController@delete'
        ]);

        /**
         * редактирование proc_status
         */
        Route::post('/statuses/edit', [
            'as'   => 'status-edit',
            'uses' => 'ProcStatusController@edit'
        ]);

        /**
         * создание статуса
         */
        Route::post('/statuses/create', [
            'as'   => 'status-create',
            'uses' => 'ProcStatusController@create'
        ]);

        /**
         * Добавление партнера
         */
        Route::post('/partners/create', [
            'as'         => 'partners-create-ajax',
            'uses'       => 'PartnerController@partnerCreateAjax',
        ])->middleware('can:add_new_partner_ajax');

        /**
         * Редактирование партнера
         */
        Route::post('/partners/', [
            'as'         => 'partners-edit-ajax',
            'uses'       => 'PartnerController@partnerEditAjax',
        ])->middleware('can:edit_partner');

        /**
         * Генерация ключа
         */
        Route::post('/generate-key', [
            'as'   => 'generate-key',
            'uses' => 'PartnerController@generateKey'
        ]);

        Route::post('/reset-pros-stage', [
            'as'   => 'ajax-reset-pros-stage',
            'uses' => 'OrderController@resetProcStage'
        ]);

        /**
         * Страница добавления нормы апрува ajax
         */
        Route::match(['get', 'post'], '/plan-rate-add', [
            'as'         => 'plan-rate-add-ajax',
            'uses'       => 'PlanController@addNewPlanRateAjax',
        ])->middleware('can:page_plan_rates');

        /**
         * Страница добавления нормы апрува ajax
         */
        Route::match(['get', 'post'], '/plan-rate-add-with-link/{link}', [
            'as'         => 'plan-rate-add-with-link',
            'uses'       => 'PlanController@addNewPlanRateWithLinkAjax',
        ])->where('link', '[0-9]+')->middleware('can:page_plan_rates');

        /**
         * распеделение заказов ммжду коллекторами
         */
        Route::post('/share-collector-orders', [
            'as'   => 'share-collector-orders-ajax',
            'uses' => 'CollectingController@shareCollectorOrder'
        ]);

        /**
         * меняем цель для заказа
         */
        Route::post('/order/change-target-in-order', [
            'as'   => 'order-change-target-in-order',
            'uses' => 'OrderController@orderChangeTargetInOrderAjax'
        ]);

        /*order change status*/
        Route::post('/order-change-proc-status', [
            'as'   => 'order-change-proc-status',
            'uses' => 'OrderController@changeProcStatus',
        ]);


        /*run action*/
        Route::post('/run-action', [
            'as'   => 'run-action-ajax',
            'uses' => 'ActionController@runActionAjax',
        ]);

        /* Добавляем комментарии к заказу */
        Route::match(['get', 'post'], '/add-comment/', [
            'as'   => 'add-comment-ajax',
            'uses' => 'OrderController@addCommentAjax'
        ]);

        /**
         * добавление комента к товару
         */
        Route::post('/order/add-comment-for-product', [
            'as'   => 'order-add-comment-for-product-ajax',
            'uses' => 'OrderController@addCommentForProductAjax'
        ]);

        /**
         * отменяем отправку для заказа
         */
        Route::post('/orders/cancel-send', [
            'as'   => 'orders-cancel-send-ajax',
            'uses' => 'OrderController@cancelSendAjax'
        ]);

        /**
         * количество заказов на модерации
         */
        Route::post('count-orders-on-moderation', [
            'as'   => 'count-orders-on-moderation',
            'uses' => 'OrderController@countOrdersOnModerationAjax'
        ]);


        /**
         * количество заказов на модерации
         */
        Route::post('count-coldcalls-orders-on-moderation', [
            'as'   => 'count-coldcalls-orders-on-moderation',
            'uses' => 'ColdCallListController@countColdCallsOrdersOnModerationAjax'
        ]);

        /**
         * сохраняем все данные о заявке на заказ
         */
        Route::post('/orders/{id}/save-order-data', [
            'as'   => 'orders-id-save-order-data',
            'uses' => 'OrderController@saveOrderDataAjax'
        ])->where('id', '[0-9]+');

        /**
         * сохраняем статус для заблокированного заказа
         */
        Route::post('/save-status-for-locked-order', [
            'as'   => 'save-status-for-locked-order',
            'uses' => 'OrderController@saveStatusForLockedOrder'
        ]);

        /**
         * сохраняем все данные о заказе
         */
        Route::post('/orders/{id}/save-order-sending-data', [
            'as'   => 'orders-id-save-order-sending-data',
            'uses' => 'OrderController@saveOrderSendingDataAjax'
        ])->where('id', '[0-9]+');

        /**
         * изменяем проект в заказа
         */
        Route::post('/orders/change-project', [
            'as'   => 'orders-change-project-ajax',
            'uses' => 'OrderController@changeProjectAjax'
        ]);

        /**
         * Страница добавления нормы апрува ajax
         */

        Route::match(['get', 'post'], '/plan-rate-add-offer/{id}', [
            'as'         => 'plan-rate-add-with-link',
            'uses'       => 'PlanController@addPlanOffersAjax',
        ])->where('id', '[0-9]+')->middleware('can:page_plan_rates');

        /**
         * Страница добавления нормы апрува ajax
         */
        Route::match(['get', 'post'], '/delete-plan-offer', [
            'as'         => 'delete-plan-offer-ajax',
            'uses'       => 'PlanController@deletePlanOffer',
        ])->where('link', '[0-9]+')->middleware('can:page_plan_rates');

        /**
         * Страница добавления нормы апрува ajax
         */
        Route::match(['get', 'post'], '/delete-plan-rate', [
            'as'         => 'delete-plan-rate-ajax',
            'uses'       => 'PlanController@deletePlanRate',
        ])->where('link', '[0-9]+')->middleware('can:page_plan_rates');

        /**
         * удаление файла ХП (и листов)
         */
        Route::post('/delete-cold-call-file', [
            'as'         => 'cold-call-list-delete',
            'uses'       => 'ColdCallListController@delete',
        ])->middleware('can:create_edit_cold_call_list');

        /**
         * изменение статуса листа ХП
         */
        Route::match(['get', 'post'], '/cold-calls-list-change-status/{id}/{status}', [
            'as'         => 'cold-calls-list-change-status',
            'uses'       => 'ColdCallListController@changeStatus',
        ])->where('id', '[0-9]+')->middleware('can:create_edit_cold_call_list');

        /**
         * изменяем тип товара на странице модерации
         */
        Route::post('/moderation/change-product-type', [
            'as'   => 'moderation-change-product-type',
            'uses' => 'ProductController@moderationChangeProductType'
        ]);

        /**
         * Подгрузка списка операторов согласно очереди
         */
        Route::post('/change-operators-options', [
            'as'   => 'change-operators-options',
            'uses' => 'OrderController@changeOperatorsOptions'
        ]);

        /**
         * Сохряняем измнения модератора
         */
        Route::post('/save-moderator-changes', [
            'as'         => 'save-moderator-changes',
            'uses'       => 'OrderController@saveModeratorChanges',
        ])->middleware('can:moderator_changes');

        /**
         * give feedback
         */
        Route::post('/feedback-ajax/{orderId}/{userId}/{ordersOpenedId}', [
            'as'   => 'feedback-ajax',
            'uses' => 'FeedbackController@add',
        ]);

        /**
         * send failed ticket
         */
        Route::post('/send-ticket', [
            'as'         => 'send-ticket',
            'uses'       => 'FeedbackController@sendTicket',
        ])->middleware('can:feedback_add');

        /**
         * send info/fault ticket
         */
        Route::post('/send-info-fault-ticket', [
            'as'         => 'send-info-fault-ticket',
            'uses'       => 'FeedbackController@sendInfoFaultTicket',
        ])->middleware('can:ticket_create');

        /**
         * загрузить листы в рвх повторно
         */
        Route::post('/upload-cold-call-lists-pbx', [
            'as'         => 'upload-cold-call-lists-pbx-ajax',
            'uses'       => 'ColdCallListController@uploadToPbx',
        ])->middleware('can:create_edit_cold_call_list');

        /**
         * установить статус офферу
         */
        Route::post('/set-offer-status/{id}', [
            'as'   => 'set-offer-status-ajax',
            'uses' => 'ColdCallListController@setOfferStatus'
        ])->where('id', '[0-9]+');

        Route::match(['get', 'post'], '/cold-calls/import-process', [
            'as'         => 'cold_call_list_import_process',
            'uses'       => 'ColdCallListController@processImport',
        ])->middleware('can:create_edit_cold_call_list');

        /**
         * подгрузка рекомендованных товаров согласно оффера
         */
        Route::post('/change-recommended-products', [
            'as'   => 'change-recommended-products-ajax',
            'uses' => 'ColdCallListController@changeRecommendedProducts'
        ]);

        /**
         * создание продукта
         */
        Route::post('/products/create', [
            'as'   => 'product-create-ajax',
            'uses' => 'ProductController@create'
        ]);

        /**
         * удаление продукта
         */
        Route::post('/products/destroy', [
            'as'   => 'product-destroy-ajax',
            'uses' => 'ProductController@destroy'
        ]);

        /**
         * обновление продукта
         */
        Route::post('/products/store', [
            'as'   => 'product-store-ajax',
            'uses' => 'ProductController@store'
        ]);

        /**
         * обьелинение товаров
         */
        Route::post('/products/merge', [
            'as'   => 'products-merge-ajax',
            'uses' => 'ProductController@mergeProducts'
        ]);

        /**
         * обновление наименования продукта
         */
        Route::post('/products/edit-product-title', [
            'as'   => 'edit-product-title-ajax',
            'uses' => 'ProductController@editProductTitle'
        ]);

        /**
         * изменение статуса товара
         */
        Route::match(['get', 'post'], '/products/{id}/set-status/{status}', [
            'as'   => 'products-change-status',
            'uses' => 'ProductController@changeStatus',
        ])->where('id', '[0-9]+');


        /* Обновление блоков к скрипту*/
        Route::match(['get', 'post'], '/scripts/details/update', [
            'as'         => 'scripts-change-offer-ajax',
            'uses'       => 'ScriptController@updateBlockPosition',
        ])->middleware('can:read_add_script');

        /* Страница добавления скрипта*/
        Route::match(['get', 'post'], '/scripts/change-offer', [
            'as'         => 'scripts-change-offer-ajax',
            'uses'       => 'OfferController@scriptChangeOffer',
        ])->middleware('can:read_add_script');


        /* Страница добавления сообщения к фидбеку*/
        Route::match(['get', 'post'], '/feedback/new-message', [
            'as'         => 'feedback-new-message-ajax',
            'uses'       => 'FeedbackController@addNewComment',
        ])->middleware('can:page_operator_mistakes');

        /* Изменения статуса фидбека*/
        Route::post('/feedback-change-status', [
            'as'         => 'feedback-new-message-ajax',
            'uses'       => 'FeedbackController@changeStatus',
        ])->middleware('can:page_operator_mistakes');

        /* Удалениеи оффера */
        Route::match(['get', 'post'], 'script/{id}/delete', [
            'as'         => 'script-delete-ajax',
            'uses'       => 'ScriptController@delete',
        ])->where('id', '[0-9]+')->middleware('can:read_add_script');

        /**
         * изменение статуса
         */
        Route::match(['get', 'post'], '/scriptDetail/{id}/{status}', [
            'as'         => 'script-detail-change-status',
            'uses'       => 'OfferController@changeStatus',
        ])->where('id', '[0-9]+')->middleware('can:read_add_script');

        /**
         * измения переменных(системы)
         */
        Route::post('/change-variable', [
            'as'         => 'change-variable-ajax',
            'uses'       => 'VariableController@changeVariableAjax',
        ])->middleware('can:change_variables');

        /**
         * добавление рангов
         */
        Route::post('/users/ranks/create', [
            'as'         => 'users-ranks-create-ajax',
            'uses'       => 'RankController@usersRanksCreateAjax',
        ])->middleware('can:add_new_rank_ajax');

        /**
         * добавление роли
         */
        Route::post('/users/roles/create', [
            'as'         => 'users-roles-create-ajax',
            'uses'       => 'RoleController@usersRolesCreateAjax',
        ])->middleware('can:add_new_rank_ajax');

        /**
         * редактирование рангов
         */
        Route::post('/users/ranks/update', [
            'as'         => 'users-ranks-update-ajax',
            'uses'       => 'RankController@usersRanksUpdateAjax',
        ])->middleware('can:edit_rank');

        /**
         * удаление рангов
         */
        Route::post('/users/ranks/delete', [
            'as'         => 'users-rank-delete-ajax',
            'uses'       => 'RankController@usersRanksDeleteAjax',
        ])->middleware('can:delete_rank');

        /**
         * вкл/выкл пермишн для роли
         */
        Route::post('/set-role-to-permission/{id}', [
            'as'   => 'set-role-to-permission',
            'uses' => 'RoleController@setRoleToPermission'
        ])->where('id', '[0-9]+');

        /**
         * вкл/выкл пермишн для пользователя
         */
        Route::post('/set-user-permission/{id}', [
            'as'   => 'set-user-permission',
            'uses' => 'UserController@setUsetPermission'
        ])->where('id', '[0-9]+');

        /* Создание скрипта к офферу(ajax) */
        Route::match(['get', 'post'], '/scripts/create', [
            'as'         => 'scripts-create-ajax',
            'uses'       => 'OfferController@addScriptAjax',
        ])->where('id', '[0-9]+')->middleware('can:read_add_script');

        /**
         * создание заказа от входящей линии
         */
        Route::post('/incoming-call/create-order', [
            'as'   => 'incoming-call-create-order-ajax',
            'uses' => 'OrderController@incomingCallCreateOrderAjax'
        ]);

        /**
         * изменение телефона и страны на странице модерации
         */
        Route::post('/moderation-change-phone-and-country', [
            'as'   => 'moderation-change-phone-and-country-ajax',
            'uses' => 'OrderController@moderationChangePhoneAndCountryAjax'
        ]);

        Route::post('/target/update', [
            'as'   => 'target-update-ajax',
            'uses' => 'TargetController@updateTargetOneAjax'
        ]);

        /**
         * поиск по всем товарам для нового заказа
         * на странице создания заказа
         */
        Route::post('/incoming-call/create-order/search-product', [
            'as'   => 'incoming-call-create-order-search-product-ajax',
            'uses' => 'OrderController@incomingCallCreateOrderSearchProductAjax'
        ]);

        /**
         * добавление очереди ajax
         */
        Route::match(['get', 'post'], '/campaigns-create', [
            'as'         => 'campaigns-create-ajax',
            'uses'       => 'CampaignController@createAjax',
        ])->middleware('can:add_chenge_companies');

        /**
         * удаление товара из оффера
         */
        Route::post('/delete-product-from-offer', [
            'as'   => 'delete-product-from-offer-ajax',
            'uses' => 'OfferController@deleteProductFromOfferAjax'
        ]);

        /**
         *создание ключа ajax
         */
        Route::post('/integrations/keys/create', [
            'as'   => 'ajax-integrations-keys-create-ajax',
            'uses' => 'Api\NovaposhtaController@keyCreateAjax'
        ]);

        /**
         * поиск по товарам на странице создание товара
         */
        Route::post('/search-product', [
            'as'   => 'search-product-ajax',
            'uses' => 'ProductController@searchProduct'
        ]);

        /**
         * Создание заказа
         */
        Route::post('/orders/create', [
            'as'   => 'create-order-ajax',
            'uses' => 'OrderController@createAjax'
        ]);

        /**
         *создание ключа ajax
         */
        Route::post('/integrations/save', [
            'as'   => 'integrations-save-ajax',
            'uses' => 'ProjectController@integrationSave'
        ]);

        /**
         * поиск населенных пунктов с доставкой новой Почты
         */
        Route::match(['get', 'post'], '/novaposhta/settlements/find', [
            'as'   => 'novaposhta-settlements-find-ajax',
            'uses' => 'Api\NovaposhtaController@novaposhtaSettlementsFind'
        ]);

        /**
         * поиск городов компании
         */
        Route::match(['get', 'post'], '/novaposhta/city/find', [
            'as'   => 'novaposhta-cities-find-ajax',
            'uses' => 'Api\NovaposhtaController@findCityByWord'
        ]);

        /**
         * поиск отделений Новой Почты
         */
        Route::match(['get', 'post'], '/novaposhta/warehouses/find', [
            'as'   => 'novaposhta-warehouses-find-ajax',
            'uses' => 'Api\NovaposhtaController@novaposhtaWarehousesFind'
        ]);

        /**
         * поиск улиц Новой Почты
         */
        Route::match(['get', 'post'], '/novaposhta/street/find', [
            'as'   => 'novaposhta-warehouses-find-ajax',
            'uses' => 'Api\NovaposhtaController@novaposhtaStreetFind'
        ]);

        //CDEK POST

        /**
         * создать учетку почты CDEK
         */
        Route::match(['get', 'post'], '/cdek/account/create', [
            'as'   => 'cdek-account-create-ajax',
            'uses' => 'Api\CdekController@accountCreate'
        ]);

        /**
         * активировать отправителя  почты CDEK
         */
        Route::match(['get', 'post'], '/cdek/sender/activate', [
            'as'   => 'cdek-sender-activate-ajax',
            'uses' => 'Api\CdekController@activateSender'
        ]);

        /**
         * поиск регионов  русской почты CDEK
         */
        Route::match(['get', 'post'], '/cdek/regions/find', [
            'as'   => 'cdek-regions-find-ajax',
            'uses' => 'Api\CdekController@regionsFind'
        ]);
        /**
         * поиск ПВЗ русской почты CDEK
         */
        Route::match(['get', 'post'], '/cdek/cities/find', [
            'as'   => 'cdek-cities-find-ajax',
            'uses' => 'Api\CdekController@citiesFind'
        ]);
        /**
         * поиск ПВЗ русской почты CDEK
         */
        Route::match(['get', 'post'], '/cdek/pvz/find', [
            'as'   => 'cdek-pvz-find-ajax',
            'uses' => 'Api\CdekController@pvzFind'
        ]);

        // NINJAXPRESS POST AJAX ROUTES
        /**
         * добавить отправителя почты Ninjaxpress
         */
        Route::match(['get', 'post'], '/ninjaxpress/key/add', [
            'as'   => 'ninjaxpress-sender-add-ajax',
            'uses' => 'Api\NinjaxpressController@keyAdd'
        ]);

        /**
         * сгенерировать access_token почты Ninjaxpress
         */
        Route::match(['get', 'post'], '/ninjaxpress/generate-access-token', [
            'as'   => 'ninjaxpress-generate-access-token-ajax',
            'uses' => 'Api\NinjaxpressController@generateAccessToken'
        ]);


        /**
         * сгенерировать hmac почты Ninjaxpress
         */
        Route::match(['get', 'post'], '/ninjaxpress/generate-hmac', [
            'as'   => 'ninjaxpress-hmac-ajax',
            'uses' => 'Api\NinjaxpressController@generateHmac'
        ]);

        /**
         * акивировать ключ почты Ninjaxpress
         */
        Route::match(['get', 'post'], '/ninjaxpress/key/activate', [
            'as'   => 'ninjaxpress-key-activate-ajax',
            'uses' => 'Api\NinjaxpressController@keyActivate'
        ]);


        //VIETTEL POST
        /**
         * поиск district вьетнамской почты Viettel
         */
        Route::match(['get', 'post'], '/viettel/district/find', [
            'as'   => 'viettel-district-find-ajax',
            'uses' => 'Api\ViettelController@districtFind'
        ]);

        /**
         * load sender viettel ajax
         */
        Route::match(['get', 'post'], '/viettel/sender-by-key', [
            'as'   => 'viettel-sender-by-key-ajax',
            'uses' => 'Api\ViettelController@loadSenderByKey'
        ]);

        /**
         * поиск province вьетнамской почты Viettel
         */
        Route::match(['get', 'post'], '/viettel/province/find', [
            'as'   => 'viettel-province-find-ajax',
            'uses' => 'Api\ViettelController@provinceFind'
        ]);

        /**
         * поиск ward (административного подразделения) вьетнамской почты Viettel
         */
        Route::match(['get', 'post'], '/viettel/ward/find', [
            'as'   => 'viettel-ward-find-ajax',
            'uses' => 'Api\ViettelController@wardFind'
        ]);

        /**
         * создать учетку вьетнамской почты Viettel
         */
        Route::match(['get', 'post'], '/viettel/account/create', [
            'as'   => 'viettel-account-create-ajax',
            'uses' => 'Api\ViettelController@accountCreate'
        ]);

        /**
         * добавить отправителя вьетнамской почты Viettel
         */
        Route::match(['get', 'post'], '/viettel/sender/add', [
            'as'   => 'viettel-sender-add-ajax',
            'uses' => 'Api\ViettelController@senderAdd'
        ]);

        /**
         * активировать отправителя вьетнамской почты Viettel
         */
        Route::match(['get', 'post'], '/viettel/sender/activate', [
            'as'   => 'viettel-sender-activate-ajax',
            'uses' => 'Api\ViettelController@activateSender'
        ]);


        /**
         * Sign-in and refresh login for Viettel Post
         */
        Route::match(['get', 'post'], '/viettel/sign-in', [
            'as'   => 'viettel-sign-in-ajax',
            'uses' => 'Api\ViettelController@signInAjax'
        ]);


//        /**
//         * создание экспресс-накладной (Новая почта)
//         */
//        Route::match(['get', 'post'], '/delivery-note-create', [
//            'as'   => 'delivery-note-create-ajax',
//            'uses' => 'Api\IntegrationController@novaPoshtaDeliveryNoteCreate'
//        ]);

        /**
         * Создание экспресс-накладной
         */
        Route::post('/delivery-note-create', [
            'as'   => 'delivery-note-create-ajax',
            'uses' => 'Api\IntegrationController@createDeliveryNote'
        ]);


        /**
         *  редактирование накладной
         */
        Route::match(['get', 'post'], '/delivery-note-edit', [
            'as'   => 'delivery-note-edit-ajax',
            'uses' => 'Api\IntegrationController@editDeliveryNote'
        ]);

        /**
         *  удаление накладной
         */
        Route::match(['get', 'post'], '/delivery-note-delete', [
            'as'   => 'delivery-note-delete-ajax',
            'uses' => 'Api\IntegrationController@deleteDeliveryNote'
        ]);

        /**
         * активация/деактивация ключа Новой Почты
         */
        Route::match(['get', 'post'], 'novaposhta/keys/{keyId}/{status}', [
            'as'   => 'novaposhta-keys-change-status-ajax',
            'uses' => 'Api\NovaposhtaController@changeKeyStatus'
        ]);

        /**
         * сохранение настроек для кодов каждой из интеграций
         */
        Route::match(['get', 'post'], '/save-code-status', [
            'as'   => 'save-code-status-ajax',
            'uses' => 'Api\IntegrationController@saveCodeStatusAjax'
        ]);

        /**
         *
         */
        Route::post('/sender-save', [
            'as'   => 'sender-save-ajax',
            'uses' => 'Api\NovaposhtaController@senderEditAjax'
        ]);

        // set timezone fore ninjaxpress
        Route::post('/integrations/ninjaxpress/set-timezone/html', [
            'as'    => 'ajax-integrations-ninjaxpress-set-timezone-html',
            'uses'  => 'Api\NinjaxpressApiController@setTimezoneHtml'
        ]);

        Route::match(['get', 'post'], '/integrations/ninjaxpress/delivery-note/html/{sender}/{track}', [
            'as'   => 'ninjaxpress-delivery-note-print-html-ajax',
            'uses' => 'Api\NinjaxpressController@printNoteHtmlData'
        ]);

        /**
         * get sms template for sending customer notification
         */
        Route::post('/get-sms-template/{templateId}', [
            'as'   => 'get-sms-template-ajax',
            'uses' => 'SmsController@getTemplate'
        ])->where('id', '[0-9]+');

        /**
         *  sending sms
         */
        Route::post('/send-sms', [
            'as'   => 'send-sms-ajax',
            'uses' => 'SmsController@initiateSmsGuzzle'
        ])->where('id', '[0-9]+');

        /**
         *  sms template edit name
         */
        Route::post('/sms/templates/create', [
            'as'   => 'sms-templates-create',
            'uses' => 'SmsController@create'
        ]);

        /**
         *  sms template edit name
         */
        Route::post('/sms/templates/name-edit', [
            'as'   => 'sms-templates-name-edit',
            'uses' => 'SmsController@editTemplateName'
        ]);

        /**
         *  sms template edit body
         */
        Route::post('/sms/templates/body-edit', [
            'as'   => 'sms-templates-body-edit',
            'uses' => 'SmsController@editTemplateBody'
        ]);

        /**
         *  sms template delete
         */
        Route::post('/sms/templates/destroy', [
            'as'   => 'sms-templates-destroy',
            'uses' => 'SmsController@destroy'
        ]);


        /**
         * CDEK ajax
         */
        Route::match(['get', 'post'], '/cdek/calculate-cost-actual', [
            'as'   => 'cdek-calculate-cost-actual-ajax',
            'uses' => 'Api\CdekController@calculateCostActual'
        ]);

        /**
         *
         */
        Route::match(['get', 'post'], '/integrations/novaposhta/delivery-note/html/{track}', [
            'as'   => 'integrations-delivery-note-print-html-ajax',
            'uses' => 'Api\NovaposhtaController@printNoteHtmlData'
        ]);

        /**
         *
         */
        Route::post('/orders/proc-status2-load/{status}/{orderId}', [
            'as'   => 'orders-proc-status2-load-ajax',
            'uses' => 'ProcStatusController@procStatus2Load'
        ]);

        /**
         *
         */
        Route::post('/orders/proc-statuses/update', [
            'as'   => 'orders-proc-statuses-update-ajax',
            'uses' => 'OrderController@updateSendingStatuses'
        ]);


        Route::prefix('pass')->group(function () {
            /**
             * добавление заказа в проводок по трекингу
             */
            Route::post('/add-order-by-track', [
                'as'   => 'add-order-by-track-ajax',
                'uses' => 'OrderController@addOrderByTrackAjax',
            ]);

            /**
             * Удаление заказа из проводка
             */
            Route::post('/order/delete', [
                'as'   => 'pass-order-delete-ajax',
                'uses' => 'OrderController@passOrderDeleteAjax'
            ]);

            /**
             * Удаление заказа из проводка "отправлен"
             */
            Route::post('/order/delete-send', [
                'as'   => 'pass-order-delete-send-ajax',
                'uses' => 'OrderController@passSendOrderDeleteAjax'
            ]);

            /**
             * Добавление заказа в проводок
             */
            Route::post('/order/add', [
                'as'   => 'pass-order-add-ajax',
                'uses' => 'OrderController@passOrderAddAjax'
            ]);

            /**
             * Добавление заказа в проводок для отправки
             */
            Route::post('/order/add-send', [
                'as'   => 'pass-order-add-send-ajax',
                'uses' => 'OrderController@passSendingOrderAddAjax'
            ]);

            /**
             * поиск заказов
             */
            Route::post('/orders/search', [
                'as'   => 'pass-orders-search-ajax',
                'uses' => 'OrderController@passOrdersSearchAjax'
            ]);

            /**
             * изменение полей
             */
            Route::post('/orders/change', [
                'as'   => 'pass-orders-change-ajax',
                'uses' => 'OrderController@passOrdersChangeAjax'
            ]);
        });

        Route::post('/product_option/save', [
            'as'   => 'add-product-option-to-order-ajax',
            'uses' => 'ProductController@addProductOptionToOrderAjax'
        ]);

        Route::post('count-orders-by-status', [
            'as'   => 'count-orders-by-status-ajax',
            'uses' => 'OrderController@countOrdersByStatusAjax'
        ]);

        Route::post('/statuses/get-order-by-status', [
            'as'   => 'statuses-get-order-by-status-ajax',
            'uses' => 'ProcStatusController@getOrderByStatusAjax'
        ]);

        Route::post('/rewrite-statuses', [
            'as'   => 'rewrite-statuses-ajax',
            'uses' => 'ProcStatusController@rewriteStatus'
        ]);

        Route::post('/annul-moderation/{id}', [
            'as'   => 'annul-moderation-ajax',
            'uses' => 'OrderController@annulModeration'
        ])->where('id', '[0-9]+');

        Route::post('/user/settings', [
            'as'   => 'user-settings-ajax',
            'uses' => 'UserController@userSettings'
        ]);
    });

    /**
     * открытие листа холодных продаж при дозвоне
     */
    Route::match(['get', 'post'], '/list/opened/{id}/{flag?}',
        [
            'as'   => 'list-opened',
            'uses' => 'ColdCallListController@createOrder'
        ])->where('id', '[0-9]+')->defaults('flag', 1);


    /* Страница добавления скрипта*/
    Route::match(['get', 'post'], '/offer/{offerId}/script/add', [
        'as'         => 'offer-script-add',
        'uses'       => 'OfferController@addScriptWithOffer',
    ])->where('id', '[0-9]+')->middleware('can:read_add_script');


    /* Страница добавления скрипта к офферу */
    Route::match(['get', 'post'], '/script/{scriptId}/block-add-ajax', [
        'as'         => 'script-block-add-ajax',
        'uses'       => 'ScriptController@addScriptBlockAjax',
    ])->where('id', '[0-9]+')->middleware('can:read_add_script');

    /* Удалениеи блока скрипта */
    Route::match(['get', 'post'], 'script/block/{id}/delete', [
        'as'         => 'script-delete-ajax',
        'uses'       => 'ScriptController@deleteBlock',
    ])->where('id', '[0-9]+')->middleware('can:read_add_script');

    /* Страница редактирования скрипта к офферу */
    Route::match(['get', 'post'], '/script/{scriptId}/edit/{scriptDetailId}', [
        'as'         => 'offers-scripts-details-edit',
        'uses'       => 'ScriptController@editBlock',
    ])->where('id', '[0-9]+')->middleware('can:read_add_script');

    /**
     * изменение статуса скрипта
     */
    Route::match(['get', 'post'], '/script/{id}/set-status/{status}', [
        'as'         => 'script-change-status',
        'uses'       => 'ScriptController@changeStatus',
    ])->where('id', '[0-9]+')->middleware('can:read_add_script');

    /* Страница редактирования скрипта к офферу */
    Route::match(['get', 'post'], '/script/{scriptId}/edit-ajax', [
        'as'         => 'offer-script-edit-ajax',
        'uses'       => 'OfferController@editScriptAjax',
    ])->where('id', '[0-9]+')->middleware('can:read_add_script');

    Route::post('/target/create-ajax', [
        'as'         => 'target-create-ajax',
        'uses'       => 'TargetController@targetCreateAjax',
    ])->middleware('can:page_create_target');

    /**
     * страница выплат компаниям
     */
    Route::match(['get', 'post'], '/new-payout-company/{id}', [
        'as'         => 'new-payout-company',
        'uses'       => 'FinanceController@newPayoutCompany',
    ])->where('id', '[0-9]+')->middleware('can:do_payout_companies');


    /*Search by filter*/
    Route::post('/search', 'SearchController@filter');

    /**
     * изменение статуса плана
     */
    Route::match(['get', 'post'], '/plan-change-status-ajax/{id}/{status}', [
        'as'         => 'plan-change-status',
        'uses'       => 'PlanController@changeStatus',
    ])->where('id', '[0-9]+')->middleware('can:create_edit_plan');

    /**
     * страница выплат операторам
     */
    Route::match(['get', 'post'], '/new-payout-user/{id}', [
        'as'         => 'new-payout-user',
        'uses'       => 'FinanceController@newPayoutUser',
    ])->where('id', '[0-9]+')->middleware('can:do_payout_operators');

    /**
     * изменяем цель для нового заказа
     */
    Route::post('/incoming-call/create-order/get-target-ajax', [
        'as'   => 'incoming-call-create-order-get-target-ajax',
        'uses' => 'OrderController@incomingCallCreateOrderGetTargetAjax'
    ]);

    /**
     * поиск по номеру телефона
     */
    Route::post('/incoming-call/search-orders-by-phone-ajax', [
        'as'   => 'incoming-call-search-orders-by-phone-ajax',
        'uses' => 'OrderController@incomingCallSearchOrdersByPhoneAjax'
    ]);


    Route::post('/online', [
        'as'   => 'online',
        'uses' => 'OnlineController@getOnlineUser'
    ]);

    Route::post('/edit-group-another-language', [
        'as'   => 'edit-group-another-language',
        'uses' => 'OrderController@editGroupAnotherLanguage'
    ]);

    Route::match(['get', 'post'], '/change-elastix-company-orders-ajax', [
        'as'   => 'change-elastix-company-orders-ajax',
        'uses' => 'OrderController@changeElastixCompanyOrdersAjax'
    ]);

    Route::match(['get', 'post'], '/set-not-calls-callback-ajax/', [
        'as'   => 'set-not-calls-callback-ajax',
        'uses' => 'OrderController@setNotCallsCallbackAjax'
    ]);

    Route::match(['get', 'post'], '/date-filter-template-ajax/', [
        'as'   => 'date-filter-template-ajax',
        'uses' => 'OrderController@dateFilterTemplateAjax'
    ]);

    /* Поиск предложений */
    Route::match(['get', 'post'], '/order-search-offers-ajax/', [
        'as'   => 'order-search-offers-ajax',
        'uses' => 'OrderController@orderSearchOffersAjax'
    ]);

    /* Поиск предложений */
    Route::match(['get', 'post'], '/order-search-offers-locked-ajax/', [
        'as'   => 'order-search-offers-locked-ajax',
        'uses' => 'OrderController@orderSearchOffersLockedAjax'
    ]);

    /* Получение всех операторов согласно компании */
    Route::match([
        'get',
        'post'
    ], '/operator-mistakes/users/get-by-company-id/{company_id}', 'FeedbackController@getUsersByCompany')
        ->where('id', '[0-9]+');

    Route::match(['get', 'post'], '/order-final-target-ajax/', [
        'as'   => 'order-final-target-ajax',
        'uses' => 'OrderController@finalTargetAjax'
    ]);

    Route::match(['get', 'post'], '/get-monitoring-agents-ajax/', [
        'as'   => 'get-monitoring-agents-ajax',
        'uses' => 'MonitoringController@getMonitoringAgentsAjax'
    ]);

    Route::match(['get', 'post'], '/get-monitoring-company-ajax/', [
        'as'   => 'get-monitoring-company-ajax',
        'uses' => 'MonitoringController@getMonitoringCompanyAjax'
    ]);

    /**
     * Получаем все города НП
     */
    Route::match(['get', 'post'], '/city-np-ajax/', [
        'as'   => 'city-np-ajax',
        'uses' => 'OrderController@getCityNPAjax'
    ]);

    /**
     * Получаем все отделения НП
     */
    Route::match(['get', 'post'], '/get-warehouse-np-ajax/', [
        'as'   => 'get-warehouse-np-ajax',
        'uses' => 'OrderController@getWarehouseNPAjax'
    ]);

    /**
     * Обновление позиции
     */
    Route::match(['get', 'post'], '/company_elastix/position_update', [
        'as'   => 'company_elastix/position_update',
        'uses' => 'CampaignController@companyElastixPositionUpdate'
    ]);

    /**
     * Обновление Campaign
     */
    Route::match(['get', 'post'], '/company_elastix_ajax/{id}', [
        'as'   => 'company_elastix_update_ajax',
        'uses' => 'CampaignController@companyElastixUpdateAjax'
    ]);

    Route::post('/monitoring-targets-ajax', [
        'as'   => 'monitoring-targets-ajax',
        'uses' => 'MonitoringController@monitoringTargetsAjax'
    ]);

    Route::post('/get-monitoring-calls-ajax', [
        'as'   => 'get-monitoring-calls-ajax',
        'uses' => 'MonitoringController@getMonitoringCallsAjax'
    ]);

    /**
     * получаем записи разговоров
     */
    Route::get('/get-call', [
        'as'   => 'get-call-by-name',
        'uses' => 'CallProgressController@getCallByName'
    ]);

    /**
     * сохраняем изменеия в оффере
     */
    Route::post('/change-offer-information-ajax/{id}', [
        'as'   => 'change-offer-information-ajax',
        'uses' => 'OfferController@changeOfferInformationAjax'
    ])->where('id', '[0-9]+');

    /**
     * поиск рекомендованых товаров
     */
    Route::match(['get', 'post'], '/search-products-for-offer-ajax/', [
        'as'   => 'search-products-for-offer-ajax',
        'uses' => 'OfferController@searchProductsForOfferAjax'
    ]);

    /**
     * добавление товара в заказ
     */
    Route::post('/add-new-product-ajax', [
        'as'   => 'add-new-product-ajax',
        'uses' => 'OrderController@addNewProductAjax'
    ]);

    /**
     * добавление товара в заблокированом заказе
     */
    Route::post('/add-new-product-locked-ajax', [
        'as'   => 'add-new-product-locked-ajax',
        'uses' => 'OrderController@addNewProductLockedAjax'
    ]);

    /**
     * Удаление товара из заказа
     */
    Route::post('/delete-products-from-order', [
        'as'   => 'delete-products-from-order',
        'uses' => 'OrderController@deleteProductFromOrder'
    ]);

    /**
     * анулируем повторы групой
     */
    Route::post('/cancel-as-repeat-ajax', [
        'as'   => 'cancel-as-repeat-ajax',
        'uses' => 'OrderController@cancelAsRepeatAjax'
    ]);

    /**
     * анулировка заказа для модератора
     */
    Route::post('/cancel-order-ajax', [
        'as'   => 'cancel-order-ajax',
        'uses' => 'OrderController@cancelAndModeration'
    ]);

    /**
     * добавление пользователей в компании
     */
    Route::post('/add-user-in-client-company-ajax', [
        'as'   => 'add-user-in-client-company-ajax',
        'uses' => 'CampaignsController@addUserInClientCompanyAjax'
    ]);

    /**
     * получение цены для компаний по умалчанию
     */
    Route::post('/get-default-prices-ajax', [
        'as'   => 'get-default-prices-ajax',
        'uses' => 'CompanyController@getDefaultPrices'
    ]);

    /**
     * получение цены для компаний по умолчанию на страницу создания плана
     */
    Route::get('/plan-get-default-prices-ajax/{company_id}', [
        'as'   => 'plan-get-default-prices-ajax',
        'uses' => 'CompanyController@getDefaultPricesForPlan'
    ]);

    /**
     * получение новых цен по плану
     */
    Route::get('/plan-get-new-prices-ajax/{planId}', [
        'as'   => 'plan-get-new-prices-ajax',
        'uses' => 'PlanController@getNewPrices'
    ]);

    /**
     * получаем view всех похожих заказов на стрнице заказа
     */
    Route::post('/repeat-orders-in-order-ajax', [
        'as'   => 'repeat-orders-in-order-ajax',
        'uses' => 'OrderController@repeatOrdersInOrderAjax'
    ]);

    /**
     * модерация заказов
     */
    Route::post('/moderation-order-ajax/{id}', [
        'as'   => 'moderation-order-ajax',
        'uses' => 'OrderController@moderationOrderAjax'
    ])->where('id', '[0-9]+');

    /**
     * отправка повторного заказа в прозвон
     */
    Route::post('/go-to-pbx-ajax', [
        'as'   => 'go-to-pbx-ajax',
        'uses' => 'OrderController@goToPbxAjax'
    ]);

    /**
     * поиск операторов в настройках pbx компаний
     */
    Route::post('/search-operators-for-pgx-campaign-ajax', [
        'as'   => 'search-operators-for-pgx-campaign-ajax',
        'uses' => 'CampaignController@searchOperatorsForPbxCampaignAjax'
    ]);

    /**
     * поиск операторов в настройках pbx компаний
     */
    Route::post('/search-operators-for-pgx-campaign-ajax-cold-calls', [
        'as'   => 'search-operators-for-pgx-campaign-ajax-cold-calls',
        'uses' => 'ColdCallListController@searchOperatorsForPbxCampaignAjaxColdCalls'
    ]);

    /**
     * добавление товара к оферу
     */
    Route::post('/add-new-product-for-offers-ajax/{id}', [
        'as'   => 'add-new-product-for-offers-ajax',
        'uses' => 'OfferController@addNewProductForOffersAjax'
    ])->where('id', '[0-9]+');

    /**
     * добавление товара к оферу(ХП)
     */
    Route::post('/add-new-product-for-offers-ajax-cold-calls/{id}', [
        'as'   => 'add-new-product-for-offers-ajax',
        'uses' => 'OfferController@addNewProductsForOfferAjaxColdCalls'
    ])->where('id', '[0-9]+');

    /**
     * Сохоаняем даные о пользователе
     */
    Route::post('/change-data-users-ajax/{id}', [
        'as'   => 'change-data-users-ajax',
        'uses' => 'UserController@changeDataUsersAjax'
    ])->where('id', '[0-9]+');

    /**
     * создаем нового пользователя в pbx
     */
    Route::post('/create-account-elastix-ajax/{id}', [
        'as'   => 'create-account-elastix-ajax',
        'uses' => 'UserController@createAccountElastixAjax'
    ])->where('id', '[0-9]+');

    /**
     * редактирование аккаунта в pbx
     */
    Route::post('/change-account-elastix-ajax/{id}', [
        'as'   => 'change-account-elastix-ajax',
        'uses' => 'UserController@changeAccountElastixAjax'
    ])->where('id', '[0-9]+');

    /**
     * редактирование аккаунта в pbx
     */
    Route::post('/registration-data-users-ajax', [
        'as'   => 'registration-data-users-ajax',
        'uses' => 'UserController@registrationDataUsersAjax'
    ])->where('id', '[0-9]+');

    /**
     * получаем данные о не оплаченых транцакциях на странице выплат компаниям
     */
    Route::post('/get-info-transaction-ajax/{id}', [
        'as'   => 'get-info-transaction-ajax',
        'uses' => 'FinanceController@getInfoTransactionAjax'
    ])->where('id', '[0-9]+');

    /**
     * получаем данные о не оплаченых транцакциях на странице выплат компаниям
     */
    Route::post('/get-info-transaction-user-ajax/{id}', [
        'as'   => 'get-info-transaction-user-ajax',
        'uses' => 'FinanceController@getInfoTransactionUserAjax'
    ])->where('id', '[0-9]+');

    /**
     * подтверждаем звонок как плохая связь
     */
    Route::post('/confirm-bad-connection-ajax/{id}', [
        'as'   => 'confirm-bad-connection-ajax',
        'uses' => 'OrderController@confirmBadConnectionAjax'
    ])->where('id', '[0-9]+');

    /**
     * меняем статус с автоответчика на ложный авто
     */
    Route::post('/cancel-bad-connection-ajax/{id}', [
        'as'   => 'cancel-bad-connection-ajax',
        'uses' => 'OrderController@cancelBadConnectionAjax'
    ])->where('id', '[0-9]+');

    /**
     * изменяем очередь и забрасываем в прозвон
     */
    Route::post('/change-campaign/{id}', [
        'as'   => 'change-campaign',
        'uses' => 'OrderController@changeCampaign'
    ])->where('id', '[0-9]+');

    /**
     * изменяем очередь и забрасываем в прозвон
     */
    Route::post('/save-order-locked-changes', [
        'as'   => 'save-order-locked-changes',
        'uses' => 'OrderController@saveOrderLockedChanges'
    ]);

    /*SEARCHING ROUTES*/

    /* Поиск всех компаний */
    Route::match(['get', 'post'], '/company/find/', 'CompanyController@findByWord');
    /* Поиск всех предложений */
    Route::match(['get', 'post'], '/offer/find/', 'OfferController@findByWord');
    /* Поиск всех товаров */
    Route::match(['get', 'post'], '/product/find/', 'ProductController@findByWord');
    /* Поиск всех стран */
    Route::match(['get', 'post'], '/country/find/', 'CountryController@findByWord');
    /* Поиск всех операторов */
    Route::match(['get', 'post'], '/user/find/', 'UserController@findByWord');
    /* Поиск всех заказов */
    Route::match(['get', 'post'], '/order/find/', 'OrderController@findById');
    /* Поиск всех ролей */
    Route::match(['get', 'post'], '/roles/find/', 'RoleController@findByName');
    /* Поиск всех рангов */
    Route::match(['get', 'post'], '/ranks/find/', 'RankController@findByName');
    /* Поиск группы операторов  */
    Route::match(['get', 'post'], '/user-group/find/', 'UserController@findGroupByWord');
    /* все роли для x-editable */
    Route::match(['get', 'post'], '/roles/all', 'RoleController@getAllRoles');
    /* Поиск группы операторов  */
    Route::match(['get', 'post'], '/users-by-parameters/find', 'UserController@getUsersByParametersAjax');
    /* поиск оффера для хп */
    Route::match(['get', 'post'], '/cold-calls/product/find/', 'ColdCallListController@findProducts');
    /* поиск проектов*/
    Route::match(['get', 'post'], '/projects/find', 'ProjectController@findByWordProject');
    /* поиск подпроектов*/
    Route::match(['get', 'post'], '/sub_projects/find', 'ProjectController@findByWordSubProject');
    /* поиск подразделения*/
    Route::match(['get', 'post'], '/divisions/find', 'ProjectController@findByWordDivisions');
    /* поиск доставки */
    Route::match(['get', 'post'], '/target/find', 'TargetController@findByWord');
    /*поиск доп.опций к товару*/
    Route::match(['get', 'post'], '/product_options/find', 'ProductController@findOptionByWord');
    /*поиск треков*/
    Route::match(['get', 'post'], '/track/find', 'TargetValueController@findTrack');
    /*поиск всех тегов*/
    Route::match(['get', 'post'], '/tags/find', 'TagController@findTag');

    /*Времмено не работающие роуты*/

    /**
     * входящая линия
     */
    Route::get('/incoming-call/{phone?}', [
        'as'   => 'incoming-call',
        'uses' => 'OrderController@incomingCall'
    ])->where('phone', '[0-9]+');

    /**
     * страница создания заказа от входящей линии
     */
    Route::get('/incoming-call/create-order', [
        'as'   => 'incoming-call-create-order',
        'uses' => 'OrderController@incomingCallCreateOrder'
    ]);

    Route::group(['prefix' => 'countries'], function () {
        Route::get('/', [
            'as'   => 'countries',
            'uses' => 'CountryController@all',
        ])->middleware('can:countries');
        Route::post('use', [
            'as'   => 'country-use',
            'uses' => 'CountryController@useChange',
        ])->middleware('can:countries');
        Route::post('replace', [
            'as'   => 'country-replace',
            'uses' => 'CountryController@replace',
        ])->middleware('can:countries');
    });


    Route::get('/documentations', [
        'as'   => 'documentation',
        'uses' => 'DocumentationController@index',
    ])->name('documentations')->middleware('can:documentations');

    Route::get('/documentations/create', [
        'uses' => 'DocumentationController@create',
    ])->name('documentations.create')->middleware('can:documentations_create');
    Route::get('/documentations/edit/{id}', [
        'uses' => 'DocumentationController@edit',
    ])->name('documentations.edit')->middleware('can:documentations_edit');
    Route::get('/documentations/show/{id}', [
        'uses' => 'DocumentationController@show',
    ])->name('documentations.show')->middleware('can:documentations_show');
    Route::post('/documentations/search', 'DocumentationController@search')
        ->name('documentations.search');
    Route::post('/documentations/store', 'DocumentationController@store')
        ->name('documentations.store');
    Route::post('/documentations/update', 'DocumentationController@update')
        ->name('documentations.update');
    Route::post('/documentations/file/delete', 'DocumentationController@deleteFile')
        ->name('documentations.file.delete');
    Route::get('/documentations/destroy/{id}', [
        'uses' => 'DocumentationController@destroy',
    ])->name('documentations.destroy')->middleware('can:documentations_destroy');

    /*
    * Access control
    */
    Route::post('/access/rule/add', 'AccessController@addRule')->name('access.rule.add');
    Route::post('/access/store', 'AccessController@store')->name('access.store');

    Route::post('/files/upload', 'UploadFileController')->name('files.upload');
});

Route::match(['get', 'post'], '/get-record', [
    'as'   => 'get-record',
    'uses' => 'CallProgressController@getRecord'
]);
