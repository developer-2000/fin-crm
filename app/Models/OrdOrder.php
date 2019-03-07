<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrdOrder extends Model
{
    protected $table = 'ord_orders';

    protected $guarded = [];


    // возможные цвета товара
    public static function getSetingsColor(){
        return [
            1 => trans('products.red'),
            2 => trans('products.black'),
            3 => trans('products.blue'),
            4 => trans('products.white'),
        ];
    }


    // возможные статусы заказа
    public static function getStatus(){
        return [
            1 => 'Новый',
            2 => 'Подтвержден',
            3 => 'Отвечен',
        ];
    }

//    ====================================================================
//    belongsTo     ======================================================
//    ====================================================================
    public function user()
    {
        return $this->belongsTo(User::class, 'order_user', 'id')
        ->withDefault([ 'name' => 'Неизвестный автор' ]);
    }

//    ====================================================================
//    hasMany       ======================================================
//    ====================================================================
// выводит сязаные продукты с заказом
    public function ordProduct()
    {
        return $this->hasMany(OrdProduct::class, 'order_id', 'id');
    }

//    выводит связанные коментарии с заказом
    public function ordComent()
    {
        return $this->hasMany(OrdComent::class, 'order_id', 'id');
    }


//    связь с продуктами через ord_product
//$query = OrdOrder::with('products:products.id as prod_id,products.title as prod_title');
//выводит id и title из продуктов и 'color_id', 'color_amount' из посредника
    public function products()
    {
        return $this->belongsToMany(Product::class, 'ord_products', 'order_id', 'product_id')
            ->withPivot('color_id', 'color_amount');
    }

//    ====================================================================
//    SCOPE         ======================================================
//    ====================================================================


    /**
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeChangeData($query) {
        $mv = $this->table;
        $pn = Projects_new::tableName();

        $query->Join($pn . ' as pn', 'pn.id', '=', 'ord_orders.project_id')
            ->Join($pn . ' as pn2', 'pn2.id', '=', 'ord_orders.subproject_id')
            ->select($mv .  '.*', 'pn.name as project_name', 'pn2.name as sub_name');


        return $query;
    }













}
