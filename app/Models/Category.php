<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $table = 'oc_category';
    protected $primaryKey = 'category_id';



    public function description()
    {
        return $this->hasMany(CategoryDescription::class, 'category_id', 'category_id');
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, ProductToCategory::class,
            'category_id', 'product_id', 'category_id', 'product_id');
    }


    public static function menuQuery()
    {
        $ids = Setting::query()->where('key', Setting::CATEGORIES_KEY)->value('value');
        return Category::query()
            ->select(['category_id'])
            ->with('description', function ($q) {
                $q->select('category_id', 'name')->where('language_id', 3)->orderBy('name');
            })
            ->withCount(['products' => function ($q) {
                $q->where('quantity', '>', 0)->where('status', 1);
            }])
            ->whereIn('category_id', unserialize($ids))
            ->orderBy('tg_sort_order')
            ->get();
    }

    public function productsPagination($page)
    {
        return ProductToCategory::query()
            ->select(['category_id', 'product_id',])
            ->with(['category' => function ($q) {
                $q->select('category_id');
            }, 'product' => function ($q) {
                $q->select(['product_id', 'quantity', 'price']);
            }, 'product.ukrDescription' => function ($q) {
                $q->select(['product_id', 'name']);
            }])
            ->whereHas('category', function ($q) {
                $q->where('status', 1)->where('main_category', 1);
            })
            ->whereHas('product', function ($q) {
                $q->where('quantity', '>', 0)->where('status', 1);
            })
            ->where('category_id', $this->category->category_id)
            ->paginate(10, ["*"], 'page', $page);
    }
}
