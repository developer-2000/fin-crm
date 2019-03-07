<?php

use Illuminate\Database\Seeder;

class AddWeFastTarget extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $wefast = new \App\Models\TargetConfig();
        $wefast->name = 'WeFast';
        $wefast->alias = 'wefast';
        $wefast->integration = 1;
        $wefast->integration_status = 'active';
        $wefast->template = 'product';
        $wefast->target_type = 'approve';
        $wefast->filter_geo = 'vn';
        $wefast->options = '{"track":{"field_title":"Track","field_name":"track","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"","field_show_result":"on","options":[]},"region":{"field_title":"Region","field_name":"region","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"on","options":[]},"district":{"field_title":"District","field_name":"district","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"on","options":[]},"cost_actual":{"field_title":"Total shipping","field_name":"cost_actual","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"","field_show_result":"on","options":[]},"note":{"field_title":"Address","field_name":"note","field_type":"text","field_value":"","field_relation_name":"","field_relation_value":"","field_required":"on","field_show_result":"on","options":[]}}';
        $wefast->active = 1;
        $wefast->save();

    }
}
