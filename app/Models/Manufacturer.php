<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{

    protected $table = 'oc_manufacturer';
    protected $primaryKey = 'manufacturer_id';


    public function description()
    {
        return $this->hasMany(CategoryDescription::class, 'category_id', 'category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'manufacturer_id', 'manufacturer_id');
    }


    public static function menuQuery($page)
    {
        return Manufacturer::query()
            ->select(['manufacturer_id', 'name'])
            ->withCount(['products' => function ($q) {
                $q->where('quantity', '>', 0)->where('status', 1);
            }])
            ->whereHas('products', function ($q) {
                $q->where('quantity', '>', 0)->where('status', 1);
            })
            ->orderBy('tg_sort_order')
            ->paginate(10, ["*"], 'page', $page);
    }

    public static function menuQueryCat(int $page, int $category_id)
    {
        return Manufacturer::query()
            ->select(['manufacturer_id', 'name'])
            ->whereHas('products', function ($q) {
                $q->where('quantity', '>', 0)->where('status', 1);
            })
            ->whereHas('products.category', function ($q) use ($category_id) {
                $q->where('oc_product_to_category.category_id', $category_id);
            })
            ->withCount(['products' => function ($q) use ($category_id) {
                $q->whereHas('category', function ($q) use ($category_id) {
                    $q->where('oc_product_to_category.category_id', $category_id);
                })->where('quantity', '>', 0)->where('status', 1);
            }])
            ->orderBy('tg_sort_order')
            ->paginate(10, ["*"], 'page', $page);
    }
}
