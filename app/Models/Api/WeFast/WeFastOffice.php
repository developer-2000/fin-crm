<?php

namespace App\Models\Api\WeFast;

use App\Models\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WeFastOffice extends Model
{
    protected $table = 'wefast_offices';

    public $timestamps = false;

    protected $fillable = [
        'district_code',
        'district_name',
        'province_code',
        'province_name',
        'ward_code',
        'ward_name',
        'pickup',
        'delivery',
        'active'
    ];

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public static function updateAll()
    {
        return DB::table(self::tableName())->update(['active' => 0]);
    }

    public static function findProvince($find)
    {
        $result = [];

        $data = self::where(function ($query) use ($find) {
                $query->where('province_name', 'like', '%' . $find . '%')
                     ->orWhere('province_code', 'like', '%' . $find . '%');
            })
            ->distinct()
            ->get(['province_code', 'province_name']);

        if ($data->isNotEmpty()) {
            foreach ($data as $datum) {
                $result[] = [
                    'id' => $datum->province_code,
                    'text' => $datum->province_name,
                ];
            }
        }

        return $result;
    }

    public static function findDistrict($find, $provinceCode)
    {
        $result = [];

        $data = self::where('province_code', $provinceCode)
            ->where(function ($query) use ($find) {
                $query->where('district_name', 'like', '%' . $find . '%')
                ->orWhere('district_code', 'like', '%' . $find . '%');
            })
            ->distinct()
            ->get(['district_code', 'district_name']);

        if ($data->isNotEmpty()) {
            foreach ($data as $datum) {
                $result[] = [
                    'id' => $datum->district_code,
                    'text' => $datum->district_name,
                ];
            }
        }

        return $result;
    }

    public static function findWard($find, $provinceCode, $districtCode)
    {
        $result = [];

        $data = self::where('province_code', $provinceCode)
            ->where('district_code', $districtCode)
            ->where(function ($query) use ($find) {
                $query->where('ward_name', 'like', '%' . $find . '%')
                    ->orWhere('ward_code', 'like', '%' . $find . '%');
            })
            ->distinct()
            ->get(['ward_code', 'ward_name']);

        if ($data->isNotEmpty()) {
            foreach ($data as $datum) {
                $result[] = [
                    'id' => $datum->ward_code,
                    'text' => $datum->ward_name,
                ];
            }
        }

        return $result;
    }
}