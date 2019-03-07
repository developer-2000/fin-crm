<?php

use Illuminate\Database\Seeder;

class UpdateTargetValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $options = DB::table('target_configs')->where('target_type', 'approve')->orderBy('id', 'desc')->get();
        $ids = [];
        foreach ($options as $option) {
            $ids[$option->id] = $option->id;
            $config = json_decode($option->options, true);
            foreach ($config as &$item) {
                $item['field_show_result'] = 'on';
            }
            DB::table('target_configs')->where('id', $option->id)->update(['options' => json_encode($config)]);
        }

        echo "updated target \n";

        $values = DB::table('target_values')->whereIn('target_id', $ids)->orderBy('id', 'desc')->get();
        if ($values) {
            foreach ($values as $value) {
                $target = json_decode($value->values, true);
                foreach ($target as &$item) {
//                    if ($item['field_required']) {
                        $item['field_show_result'] = 'on';
//                    }
                }
                DB::table('target_values')->where('id', $value->id)->update(['values' => json_encode($target)]);
            }
        }
    }
}
