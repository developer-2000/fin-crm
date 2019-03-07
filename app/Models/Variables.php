<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Variables extends Model
{
    protected $table = 'variables';
    public $timestamps = false;

    public function getVariable($key)
    {
        $variable = self::where('key', $key)->first();

        if ($variable && $variable->value == 1) {
            $variable->count_started += 1;
            self::where('key', $key)->update([
                'count_started' => $variable->count_started
            ]);

            if ($variable->count_started > 20) {
                $variable->count_started = 0;
                $variable->value = 0;
                self::where('key', $key)->update([
                    'count_started' => $variable->count_started,
                    'value'         => $variable->value
                ]);
            }
        } elseif ($variable && $variable->value == 0) {
            $variable->count_started = 0;
            self::where('key', $key)->update([
                'count_started' => $variable->count_started,
            ]);
        }

        return $variable;
    }

    public function setVariable($key, $var)
    {
        $res = $this->getVariable($key);

        if ($res) {
            return DB::table($this->table)
                ->where('key', $key)
                ->update([
                'value' => $var
            ]);
        }

        return DB::table($this->table)
            ->insert([
                'key'   => $key,
                'value' => $var
            ]);

    }
}
