<?php

namespace App\Models;

use App\Models\Model;

class ProductOption extends Model
{
    protected $table = 'product_options';
    protected $fillable = ['product_id', 'value'];

    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public static function addOptions($options, $productId)
    {
        $oldOptions = self::where('product_id', $productId)->get();

        if ($options) {
            foreach ($options as $option) {
                if (isset($option['value']) && $option['value']) {
                    if (!isset($option['id']) || !$option['id']) {
                        self::insert([
                            'product_id' => $productId,
                            'value'      => $option['value']
                        ]);
                    } else if (isset($option['id'])) {
                        self::where('id', $option['id'])->update(['value' => $option['value']]);
                    }
                }
            }

            $options = collect($options)->keyBy('id');

            if ($oldOptions->isNotEmpty()) {
                foreach ($oldOptions as $option) {
                    if (!isset($options[$option->id])) {
                        self::where('id', $option->id)->delete();
                    }
                }
            }
        }
    }

    public static function findOptions($term, $productId)
    {
        $products = \DB::table(self::tableName())
            ->select('id', 'value', 'product_id')
            ->where('value', 'LIKE', '%' . $term . '%');

        if ($productId) {
            if (is_array($productId)) {
                $products->whereIn('product_id', $productId);
            } else {
                $products->where('product_id', $productId);
            }
        }

        return $products->get();
    }
}