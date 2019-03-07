<?php

use Illuminate\Database\Seeder;

class AddCdekTarget extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $wefast = new \App\Models\TargetConfig();
        $wefast->name = 'Cdek';
        $wefast->alias = 'cdek';
        $wefast->integration = 1;
        $wefast->integration_status = 'active';
        $wefast->template = 'custom';
        $wefast->target_type = 'approve';
        $wefast->filter_geo = 'ru';
        $wefast->options = '{"delivery_mode":{"field_title":"\u0420\u0435\u0436\u0438\u043c \u0434\u043e\u0441\u0442\u0430\u0432\u043a\u0438","field_name":"delivery_mode","field_type":"select","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"","options":{"136":"&#160; C\u043a\u043b\u0430\u0434-\u0441\u043a\u043b\u0430\u0434 (\u0421-\u0421)","137":"&#160; C\u043a\u043b\u0430\u0434-\u0434\u0432\u0435\u0440\u044c (\u0421-\u0414)"}}, "postal_code":{"field_title":"\u0418\u043d\u0434\u0435\u043a\u0441","field_name":"postal_code","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"on","options":[]},"region":{"field_title":"\u041e\u0431\u043b\u0430\u0441\u0442\u044c","field_name":"region","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"on","options":[]},"street":{"field_title":"\u0423\u043b\u0438\u0446\u0430","field_name":"street","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"","field_show_result":"on","options":[]},"house":{"field_title":"\u0414\u043e\u043c","field_name":"house","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"","field_show_result":"on","options":[]},"flat":{"field_title":"\u041a\u0432\u0430\u0440\u0442\u0438\u0440\u0430","field_name":"flat","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"","field_show_result":"on","options":[]},"cost":{"field_title":"\u0414\u043e\u0441\u0442\u0430\u0432\u043a\u0430 \u043f\u0440\u0438\u0445.","field_name":"cost","field_type":"number","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"","field_show_result":"on","options":[]},"cost_actual":{"field_title":"\u0414\u043e\u0441\u0442\u0430\u0432\u043a\u0430 \u0444\u0430\u043a\u0442.","field_name":"cost_actual","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"","field_show_result":"","options":[]},"note":{"field_title":"\u0417\u0430\u043c\u0435\u0442\u043a\u0430","field_name":"note","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"","field_show_result":"on","options":[]},"track":{"field_title":"Track","field_name":"track","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"","field_show_result":"on","options":[]},"date":{"field_title":"Date","field_name":"date","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"","field_show_result":"on","options":[]},"warehouse":{"field_title":"PVZ","field_name":"warehouse","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"","field_show_result":"on","options":[]}}';
        $wefast->active = 1;
        $wefast->save();

    }
}
