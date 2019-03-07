<?php

use Illuminate\Database\Seeder;

class UpdateMeasoftTargets extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $track = [
            'field_title'          => 'Track',
            'field_name'           => 'track',
            'field_type'           => 'text',
            'field_value'          => '',
            'field_relation_name'  => '',
            'field_relation_value' => '',
            'field_required'       => '',
            'field_show_result'    => 'on',
            'options'              => []
        ];
        $target = \App\Models\TargetConfig::where('alias', 'measoft')->first();
        $fields = json_decode($target->options, true);
        if (!isset($fields['target'])) {
            $fields['track'] = $track;
            $target->options = json_encode($fields);
            $target->save();
        }

        $targetValues = \App\Models\TargetValue::where('target_id', $target->id)->get();

        if ($targetValues) {
            foreach ($targetValues as $value) {
                $fields = json_decode($value->values, true);
                if (!isset($fields['target'])) {
                    $fields['track'] = $track;
                }

                if ($value->track && isset($fields['target'])) {
                    $fields['target']['field_value'] = $value->track;
                }


                $value->values = json_encode($fields);
                $value->save();
                echo $value->order_id . "\n";
            }
        }
    }
}
