<?php

use Illuminate\Database\Seeder;

class TargetConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('target_configs')->whereIn('alias', ['novaposhta','russianpost','measoft', 'kazpost', 'belorussia',
            'uzbekistan', 'kyrgyzpost','poland', 'vietnam','azerbaijan','bulgaria','refuse','cancel'])->delete();
        DB::table('target_configs')->insert([
            [
                'name' => 'Новая Почта',
                'alias'=> 'novaposhta',
                'template' => 'custom',
                'target_type' => 'approve',
                'filter_geo'    => 'ua',
                'tag_medium'    => '',
                'options'   => '{"city":{"field_title":"\u0413\u043e\u0440\u043e\u0434","field_name":"city","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]},"warehouse":{"field_title":"\u041e\u0442\u0434\u0435\u043b\u0435\u043d\u0438\u0435","field_name":"warehouse","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]}}',
                'active'    => 1
            ],
            [
                'name' => 'Почта РФ',
                'alias'=> 'russianpost',
                'template' => 'custom',
                'target_type' => 'approve',
                'filter_geo'    => 'ru',
                'tag_medium'    => '',
                'options'   => '{"postal_code":{"field_title":"\u0418\u043d\u0434\u0435\u043a\u0441","field_name":"postal_code","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]},"region":{"field_title":"\u041e\u0431\u043b\u0430\u0441\u0442\u044c","field_name":"region","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]},"district":{"field_title":"\u0420\u0430\u0439\u043e\u043d","field_name":"district","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"","field_show_result":"","options":[]},"locality":{"field_title":"\u041d\u0430\u0441\u0435\u043b.\u043f\u0443\u043d\u043a\u0442","field_name":"locality","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]},"street":{"field_title":"\u0423\u043b\u0438\u0446\u0430","field_name":"street","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]},"house":{"field_title":"\u0414\u043e\u043c","field_name":"house","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]},"flat":{"field_title":"\u041a\u0432\u0430\u0440\u0442\u0438\u0440\u0430","field_name":"flat","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"","field_show_result":"","options":[]},"cost":{"field_title":"\u0426\u0435\u043d\u0430 \u0434\u043e\u0441\u0442\u0430\u0432\u043a\u0438","field_name":"cost","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"","field_show_result":"","options":[]}}',
                'active'    => 1
            ],
            [
                'name' => 'Ксения Москва',
                'alias'=> 'measoft',
                'template' => 'custom',
                'target_type' => 'approve',
                'filter_geo'    => 'ru',
                'tag_medium'    => 'moscow',
                'options'   => '{"date":{"field_title":"\u0414\u0430\u0442\u0430 \u0434\u043e\u0441\u0442\u0430\u0432\u043a\u0438","field_name":"date","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]},"time_min":{"field_title":"\u0412\u0440\u0435\u043c\u044f \u043e\u0442","field_name":"time_min","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]},"time_max":{"field_title":"\u0412\u0440\u0435\u043c\u044f \u0434\u043e","field_name":"time_max","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]},"postal_code":{"field_title":"\u0418\u043d\u0434\u0435\u043a\u0441","field_name":"postal_code","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]},"city":{"field_title":"\u0413\u043e\u0440\u043e\u0434","field_name":"city","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]},"street":{"field_title":"\u0410\u0434\u0440\u0435\u0441","field_name":"street","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]},"cost":{"field_title":"\u0421\u0443\u043c\u043c\u0430 \u0434\u043e\u0441\u0442\u0430\u0432\u043a\u0438","field_name":"cost","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]},"note":{"field_title":"\u0412\u043b\u043e\u0436\u0435\u043d\u0438\u0435","field_name":"note","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]}}',
                'active'    => 1
            ],
            [
                'name' => 'Почта Казахстана',
                'alias'=> 'kazpost',
                'template' => 'custom',
                'target_type' => 'approve',
                'filter_geo'    => 'kz',
                'tag_medium'    => '',
                'options'   => '{"postal_code":{"field_title":"\u0418\u043d\u0434\u0435\u043a\u0441","field_name":"postal_code","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]},"region":{"field_title":"\u041e\u0431\u043b\u0430\u0441\u0442\u044c","field_name":"region","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]},"district":{"field_title":"\u0420\u0430\u0439\u043e\u043d","field_name":"district","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"","field_show_result":"","options":[]},"locality":{"field_title":"\u041d\u0430\u0441\u0435\u043b.\u043f\u0443\u043d\u043a\u0442","field_name":"locality","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]},"street":{"field_title":"\u0423\u043b\u0438\u0446\u0430","field_name":"street","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]},"house":{"field_title":"\u0414\u043e\u043c","field_name":"house","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]},"flat":{"field_title":"\u041a\u0432\u0430\u0440\u0442\u0438\u0440\u0430","field_name":"flat","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"","field_show_result":"","options":[]},"cost":{"field_title":"\u0426\u0435\u043d\u0430 \u0434\u043e\u0441\u0442\u0430\u0432\u043a\u0438","field_name":"cost","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"","field_show_result":"","options":[]}}',
                'active'    => 1
            ],
            [
                'name' => 'Почта Белоруссии',
                'alias'=> 'belorussia',
                'template' => 'custom',
                'target_type' => 'approve',
                'filter_geo'    => 'by',
                'tag_medium'    => '',
                'options'   => '{"note":{"field_title":"\u0417\u0430\u043c\u0435\u0442\u043a\u0430","field_name":"note","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]}}',
                'active'    => 1
            ],
            [
                'name' => 'Почта Узбекистана',
                'alias'=> 'uzbekistan',
                'template' => 'custom',
                'target_type' => 'approve',
                'filter_geo'    => 'uz',
                'tag_medium'    => '',
                'options'   => '{"note":{"field_title":"\u0417\u0430\u043c\u0435\u0442\u043a\u0430","field_name":"note","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]}}',
                'active'    => 1
            ],
            [
                'name' => 'Почта Киргизии',
                'alias'=> 'kyrgyzpost',
                'template' => 'custom',
                'target_type' => 'approve',
                'filter_geo'    => 'kg',
                'tag_medium'    => '',
                'options'   => '{"note":{"field_title":"\u0417\u0430\u043c\u0435\u0442\u043a\u0430","field_name":"note","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]}}',
                'active'    => 1
            ],
            [
                'name' => 'Почта Польши',
                'alias'=> 'poland',
                'template' => 'custom',
                'target_type' => 'approve',
                'filter_geo'    => 'pl',
                'tag_medium'    => '',
                'options'   => '{"postal_code":{"field_title":"Kod pocztowy","field_name":"postal_code","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"","field_show_result":"","options":[]},"city":{"field_title":"Miasto","field_name":"city","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]},"street":{"field_title":"Ulica, nr lokalu","field_name":"street","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"","field_show_result":"","options":[]},"cost":{"field_title":"Dostawa","field_name":"cost","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"","field_show_result":"","options":[]},"note":{"field_title":"Uwaga","field_name":"note","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"","field_show_result":"","options":[]}}',
                'active'    => 1
            ],
            [
                'name' => 'Почта Вьетнама',
                'alias'=> 'vietnam',
                'template' => 'custom',
                'target_type' => 'approve',
                'filter_geo'    => 'vn',
                'tag_medium'    => '',
                'options'   => '{"note":{"field_title":"\u0417\u0430\u043c\u0435\u0442\u043a\u0430","field_name":"note","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]},"track":{"field_title":"\u0422\u0440\u044d\u043a","field_name":"track","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"","field_show_result":"","options":[]}}',
                'active'    => 1
            ],
            [
                'name' => 'Почта Азербайджан',
                'alias'=> 'azerbaijan',
                'template' => 'custom',
                'target_type' => 'approve',
                'filter_geo'    => 'az',
                'tag_medium'    => '',
                'options'   => '{"note":{"field_title":"\u0417\u0430\u043c\u0435\u0442\u043a\u0430","field_name":"note","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]}}',
                'active'    => 1
            ],
            [
                'name' => 'Почта Болгарии',
                'alias'=> 'bulgaria',
                'template' => 'custom',
                'target_type' => 'approve',
                'filter_geo'    => 'bg',
                'tag_medium'    => '',
                'options'   => '{"note":{"field_title":"\u0417\u0430\u043c\u0435\u0442\u043a\u0430","field_name":"note","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":[]}}',
                'active'    => 1
            ],
            [
                'name' => 'Отказ',
                'alias'=> 'refuse',
                'template' => 'custom',
                'target_type' => 'refuse',
                'filter_geo'    => '',
                'tag_medium'    => '',
                'options'   => '{"comment":{"field_title":"\u041a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u0439","field_name":"comment","field_type":"textarea","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"on","options":[]}}',
                'active'    => 1
            ],
            [
                'name' => 'Аннулировка',
                'alias'=> 'cancel',
                'template' => 'custom',
                'target_type' => 'cancel',
                'filter_geo'    => '',
                'tag_medium'    => '',
                'options'   => '{"cause":{"field_title":"\u041f\u0440\u0438\u0447\u0438\u043d\u0430","field_name":"cause","field_type":"radio","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"on","options":{"5":"\u041f\u043e\u0432\u0442\u043e\u0440","6":"\u041d\u0435 \u043a\u043e\u0440\u0440\u0435\u043a\u0442\u043d\u044b\u0439 \u0442\u0435\u043b\u0435\u0444\u043e\u043d","7":"\u041d\u0435 \u043a\u043e\u0440\u0440\u0435\u043a\u0442\u043d\u044b\u0435 \u0434\u0430\u043d\u043d\u044b\u0435","8":"\u0421\u0435\u0440\u0432\u0438\u0441","9":"\u041d\u0435\u0442 \u0432 \u043d\u0430\u043b\u0438\u0447\u0438\u0435","10":"\u0414\u043e\u0441\u0442\u0430\u0432\u043a\u0430 \u043d\u0435 \u0432\u043e\u0437\u043c\u043e\u0436\u043d\u0430","11":"\u0421\u043e\u043f\u0440\u043e\u0432\u043e\u0436\u0434\u0435\u043d\u0438\u0435","12":"\u041d\u0435\u0434\u043e\u0437\u0432\u043e\u043d","13":"\u041d\u0435 \u0437\u0430\u043a\u0430\u0437\u044b\u0432\u0430\u043b"}}}',
                'active'    => 1
            ],
        ]);

        echo "added targets\n";

        $orders = DB::table('orders')->where('time_created', '>=', strtotime('2018-03-01 00:00:00'))->get();
            $targetsA = \App\Models\TargetConfig::getConfigsByTarget('approve');
            $targetsR = \App\Models\TargetConfig::getConfigsByTarget('refuse');
            $targetsC = \App\Models\TargetConfig::getConfigsByTarget('cancel');
        if ($orders) {

            foreach ($orders AS $order) {
                $targets = [
                    'target_approve' => 0,
                    'target_cancel' => 0,
                    'target_refuse' => 0,
                ];
                if ($targetsA) {
                    foreach ($targetsA as $approve) {
                        if ($approve->filter_geo == $order->geo) {
                            $targets['target_approve'] = $approve->id;
                        }
                    }
                }
                if ($targetsR) {
                    foreach ($targetsR as $refuse) {
                        if ($refuse->filter_geo == $order->geo || !$refuse->filter_geo) {
                            $targets['target_refuse'] = $refuse->id;
                        }
                    }
                }
                if ($targetsC) {
                    foreach ($targetsC as $cancel) {
                        if ($cancel->filter_geo == $order->geo || !$cancel->filter_geo) {
                            $targets['target_cancel'] = $cancel->id;
                        }
                    }
                }
                DB::table('orders')->where('id', $order->id)->update($targets);
            }
        }

        echo "updated orders\n";
        $time = strtotime('2018-03-01 00:00:00');
        $timeNow = time();
        while ($time < $timeNow) {
            $targetOrders = DB::table('orders')->where('target_status', '>', 0)->whereBetween('time_created',[$time, $time + 86400])->get();
            if ($targetOrders) {
                $res = [];
                foreach ($targetOrders as $o) {
                    $targetFinal = DB::table('targets_final')->where('order_id', $o->id)->orderBy('order_id', 'desc')->get();
                    $targetGrouped = [];
                    if ($targetFinal) {
                        foreach ($targetFinal as $t) {
                            $targetGrouped[$t->order_id][$t->name] = $t;
                        }
                    }
                    if ($targetFinal) {
                        foreach ($targetGrouped as $oldtarget) {
                            if (isset($oldtarget['type'])) {
                                $alias = $oldtarget['type']->value;
                                $target = \App\Models\TargetConfig::where('alias', $alias)->first();
                                $fields = json_decode($target->options, true);
                                foreach ($oldtarget as $field => $t) {
                                    if ($field != 'type') {
                                        if (isset($fields[$field])) {
                                            $fields[$field]['field_value'] = $t->value;
                                        }
                                    }
                                }

                                $res[$o->id] = [
                                    'target_id' => $target->id,
                                    'order_id' => $o->id,
                                    'time_created' => time(),
                                    'values' => json_encode($fields)
                                ];

                                DB::table('orders')->where('id', $o->id)->where('target_status', 1)->update(['target_approve' => $target->id]);

                            } else if (isset($oldtarget['cancel'])) {
                                $target = \App\Models\TargetConfig::where('alias', 'cancel')->first();
                                $fields = json_decode($target->options, true);
                                foreach ($oldtarget as $field => $t) {
                                    $val = $t->value;
                                    if (isset($fields['cause']['options'][$val])) {
                                        $fields['cause']['field_value'] = $t->value;
                                    }
                                }
                                $res[$o->id] = [
                                    'target_id' => $target->id,
                                    'order_id' => $o->id,
                                    'time_created' => time(),
                                    'values' => json_encode($fields)
                                ];
                                DB::table('orders')->where('id', $o->id)->where('target_status', 3)->update(['target_refuse' => $target->id]);

                            } else if (isset($oldtarget['']->status) && $oldtarget['']->status == 2) {
                                $target = \App\Models\TargetConfig::where('alias', 'refuse')->first();
                                $fields = json_decode($target->options, true);
                                foreach ($oldtarget as $field => $t) {
                                    if (isset($fields['comment'])) {
                                        $fields['comment']['field_value'] = $t->value;
                                    }
                                }
                                $res[$o->id] = [
                                    'target_id' => $target->id,
                                    'order_id' => $o->id,
                                    'time_created' => time(),
                                    'values' => json_encode($fields)
                                ];

                                DB::table('orders')->where('id', $o->id)->where('target_status', 2)->update(['target_cancel' => $target->id]);

                            }
                        }
                    }
                }
                DB::table('target_values')->insert($res);
            }
            $time += 86400;
        }
        echo "added target values\n";

        echo "started to update orders_api\n";
        $time = strtotime('2018-03-01 00:00:00');
        while ($time < $timeNow) {
            $orders = DB::table('orders')->whereBetween('time_created',[$time, $time + 86400])->get();
            if ($orders) {
                $orderModel = new \App\Models\Order();
                foreach ( $orders as $o) {
                    $orderModel->getProcessingStatusOrderApi($o->id);

                }
            }
            $time += 86400;
        }
    }
}
