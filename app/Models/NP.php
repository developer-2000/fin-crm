<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;

/* CHECKED */
class NP extends BaseModel
{
    protected $table = 'novaposhta'; 

    /**
     * Получаем все города НП
     * @return object
     */
    function getCity() 
    {
        return DB::table($this->table)->select('cid', 'city_ru')
                                      ->groupBy('cid')
                                      ->orderBy('city_ru')
                                      ->get();
    }   

    /**
     * Получаем отделения НП
     * @param int $id ID города
     * @return object
     */
    function getWarehouse($id) 
    {
        if (!$id) {
            return false;
        }
        return DB::table($this->table)->select('wid', 'whs_address_ru')
                                      ->where('cid', $id)
                                      ->orderBy('whs_number')
                                      ->get();
    }

    /**
     * Проверяем склад и город
     * @return bool
     */
    function checkWarehouseAndCity($value, $field, $returnField) 
    {
        return DB::table($this->table)->where($field, $value)
                                      ->value($returnField);
    }
}