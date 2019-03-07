<?php

use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $menuText = '1,main,,menu.link-group.orders-kc,,fa-database,2
2,main,1,menu.link-title.all-orders,requests,,1
3,main,,menu.group.navigation,,,1
4,main,1,menu.link-title.pre-moderation,pre-moderation,fa-database,2
5,main,1,menu.link-title.moderation,moderation,,3
6,main,1,menu.link-title.bad-connection,bad-connection,,4
7,main,1,menu.link-title.suspicious,suspicious-orders,,5
8,main,9,menu.link-title.all-sendings,orders,,1
9,main,,menu.link-group.sendings,,fa-database,3
10,main,9,menu.link-title.queued-to-print,orders-print,,2
11,main,9,menu.link-title.passes,pass,,3
12,main,,menu.link-group.reports,,fa-bar-chart-o,4
13,main,12,menu.link-title.consolidated-report,reports-main,,1
14,main,12,menu.link-title.time-loginout,reports-time-login-logout,,2
15,main,12,menu.link-title.conversations,reports-talk-time,,3
16,main,12,menu.link-title.open-orders,reports-orders-opened,,4
17,main,12,menu.link-title.sales-period,sales,,5
18,main,12,menu.link-title.by-status,report-statuses,,6
19,main,12,menu.link-title.collection,report-collectings,,7
20,main,12,menu.link-title.by-moderators,report-moderators,,8
21,main,12,menu.link-title.by-operators,report-operators,,9
22,main,12,menu.link-title.verification,verification-orders--operators,,10
23,main,12,menu.link-title.report-by-city,report-by-city,,11
24,main,,menu.link-group.monitoring,,fa-tasks,5
25,main,24,menu.link-title.companies,monitoring-companies,,1
26,main,24,menu.link-title.processing,monitoring-processing,,2
27,main,24,menu.link-title.priorities,monitoring-orders-by-weight,,3
28,main,,menu.link-group.finance,,fa-money,6
29,main,28,menu.link-title.balance,finance-balance-companies,,1
30,main,28,menu.link-title.transactions,finance-transactions-companies,,2
31,main,28,menu.link-title.payments,finance-payouts-companies,,3
32,main,,menu.link-group.mutual-settlements,,fa-user,7
33,main,32,menu.link-title.balance,balance-users,,1
34,main,32,menu.link-title.transactions,transaction-users,,2
35,main,32,menu.link-title.payments,payouts-users,,3
36,main,,menu.link-group.cold-sales,,fa-male,8
37,main,36,menu.link-title.all-lists,cold-calls-lists,,1
38,main,36,menu.link-title.upload-list,cold-calls-import,,2
39,main,36,menu.link-title.queues,cold-calls-campaigns,,3
40,main,36,menu.link-title.setting-operators,cold-calls-operators-settings,,4
41,main,36,menu.link-title.offers,cold-call-offers,,5
42,main,36,menu.link-title.create-offer,cold-calls-offers-create,,6
43,main,36,menu.link-title.moderation,cold-calls-moderation,,7
44,main,,menu.link-group.call-rating,,fa-book,9
45,main,44,menu.link-title.operator-errors,operator-mistakes,,1
46,main,44,menu.link-title.successful-calls,success-calls,,2
47,main,,menu.link-group.offers-and-scripts,,fa-inbox,10
48,main,47,menu.link-title.offers,offers,,1
49,main,47,menu.link-title.all-scripts,scripts,,2
50,main,,menu.link-group.storehouse,,fa-cubes,11
51,main,50,menu.link-title.all-storehouse,storages,,1
52,main,50,menu.link-title.movings,movings,,2
53,main,50,menu.link-title.transactions,transactions,,3
54,main,,menu.link-group.collecting,,fa-database,12
55,main,54,menu.link-title.all-orders,collectings,,1
56,main,54,menu.link-title.processed,collectings-processing,,2
57,main,54,menu.link-title.manual-processing,collectings-hand-processing,,3
58,main,54,menu.link-title.auto-call,collectings-auto-processing,,4
59,main,,menu.link-group.operations,operations,fa-globe,13
60,main,,menu.group.settings,,,14
61,main,,menu.link-group.exchange-rates,exchange_rates,fa-cubes,15
62,main,,menu.link-title.templates,sms,fa-send-o (alias),16
63,main,,menu.link-group.projects,projects,fa-briefcase,17
64,main,,menu.link-group.products,products,fa-archive,18
65,main,,menu.link-group.companies,companies,fa-asterisk,19
66,main,,menu.link-group.plans-and-regulations,,fa-calculator,20
67,main,66,menu.link-title.plans,plans,,1
68,main,66,menu.link-title.regulations,plans-rates,,2
69,main,,menu.link-title.users,users,fa-users,21
70,main,,menu.link-group.targets,targets,fa-map-marker,22
71,main,,menu.link-title.all-integrations,integrations,fa-dropbox,23
72,main,,menu.link-title.all-partners,partners,fa-sitemap,24
73,main,,menu.link-group.variables,variables,fa-cubes,25
74,main,,menu.link-group.statuses,statuses-index,fa-bullseye,26
75,main,,menu.link-group.countries,countries,fa-globe,27
76,main,,menu.link-group.categories,categories,fa-certificate,28
77,main,,menu.link-group.translations,translation-get-index,fa-language,29
78,main,,menu.link-group.queues,,fa-asterisk,32
79,main,,menu.group.asterisk,,,31
80,main,78,menu.link-title.all-queues,campaigns,,1
81,main,78,menu.link-title.setting-operators,campaigns-operators,,2
82,main,,general.menu,menu.index,fa-align-justify,30';

        $permission = [];
        foreach (explode("\n", $menuText) as $item) {
            $menu = explode(',', $item);

            $permission[] = [
                'id' => $menu[0],
                'type' => $menu[1],
                'parent_id' => $menu[2] ? $menu[2] : null,
                'title' => $menu[3],
                'route' => $menu[4] ? $menu[4] : null,
                'icon' => $menu[5] ? $menu[5] : null,
                'position' => $menu[6] ? $menu[6] : null,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        \Illuminate\Support\Facades\DB::table('menu')->insert($permission);
    }
}
