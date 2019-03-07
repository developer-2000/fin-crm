<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use DB;


class StorageContent extends Model
{
    protected $table = 'storage_contents';

    protected $fillable = ['project_id', 'product_id', 'amount', 'hold'];

    /**
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @param $project_id
     * @param array $products
     * @return array|Collection
     */
    public static function productsExistenceCheck($project_id, array $products)
    {
        foreach ($products as $productKey => $item) {

            $existInStorage = static::where([['project_id', $project_id], ['product_id', $productKey]])->get();
            if ($existInStorage->count() < 1) {
                $storageContent = new static;
                $storageContent->project_id = $project_id;
                $storageContent->product_id = $productKey;
                $storageContent->hold = 0;
                $storageContent->amount = 0;
                $storageContent->save();
            }
        }

        $storageContents = static::with('product', 'project')
            ->whereIn('product_id', array_keys($products))
            ->where('project_id', $project_id)
            ->get()->keyBy('product_id');

        foreach ($products as $id => $amount) {
            if ((!isset($storageContents[$id]) || ($storageContents[$id]['amount'] < $amount)) && !$storageContents[$id]->project->negative) {
                if (!isset($errors)) {
                    $errors = [];
                }
                $errors[] = $storageContents[$id]->product->title
                    . ' - ' . (isset($storageContents[$id]) ? $storageContents[$id]->amount : '0');
            }
        }
        return isset($errors)
            ? ['errors' => 'На складе не хватает продуктов; в наличии : ' . implode($errors, ', ')]
            : $storageContents;
    }

    public static function checkAmountProduct($productId, $subProjectId)
    {
        $subProjectIds = is_array($subProjectId) ? $subProjectId : [$subProjectId];
        $res = DB::table(Project::tableName() . ' AS pr')
            ->where('pr.negative', 1)
            ->whereIn('id', $subProjectIds)
            ->exists();

        if (!$res) {
            $storageContent = DB::table(self::tableName() . ' AS sc')
                ->where('sc.product_id', $productId)
                ->whereIn('sc.project_id', $subProjectIds)
                ->where('sc.amount', '>', 0)
                ->first();

            if (!$storageContent) {
                return false;
            }

            $count = Order::whereHas('products', function ($q) use ($productId) {
                $q->where(OrderProduct::tableName() . '.product_id', $productId);
            })
                ->moderated()
                ->where('target_status', 1)
                ->where('final_target', 0)
                ->count();

            return $storageContent->amount - $count > 0;
        }

        return true;

//        return DB::table(self::tableName() . ' AS sc')
//            ->leftJoin(Project::tableName() . ' AS pr', 'sc.project_id', '=', 'pr.id')
//            ->where('sc.product_id', $productId)
//            ->where(function ($q) use ($subProjectId) {
//                $q->where('sc.amount', '>', 0)
//                    ->where('sc.project_id', $subProjectId)
//                    ->orWhere('pr.negative', 1);
//            })->exists();

    }
}