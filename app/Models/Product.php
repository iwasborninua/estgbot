<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'oc_product';
    protected $primaryKey = 'product_id';
    public $timestamps = false;

    public static function productDataForEdit($productId, $optionsValues)
    {
        return self::query()
            ->with('options', function ($q) use ($optionsValues) {
                $q->select(['product_id', 'product_option_id'])
                    ->with('values', function ($q) use ($optionsValues) {
                        $q->select(['product_option_value_id', 'quantity', 'option_value_id', 'price', 'product_option_id'])
                            ->whereIn('product_option_value_id', array_keys($optionsValues))
                            ->with('description', function ($q) {
                                $q->select(['name', 'option_value_id']);
                            });
                    });
            })
            ->with('description', function ($q) {
                $q->select(['name', 'product_id'])->where('language_id', config('constants.lang'));
            })
            ->where('product_id', $productId)
            ->first(['product_id', 'price', 'image']);
    }

    public function description()
    {
        return $this->hasMany(ProductDescription::class, 'product_id', 'product_id');
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class, 'product_id', 'product_id');
    }

    public function options()
    {
        return $this->hasMany(ProductOption::class, 'product_id', 'product_id');
    }

    public function inCart(): bool
    {
        return \Arr::has(\Auth::user()->getCart(), $this->product_id);
    }

    public function inCartCount()
    {
        if ($this->inCart()) {
            return array_sum(\Auth::user()->getCart()[$this->product_id]);
        } else {
            \Log::warning("product [$this->product_id] not in cart, but trying to get count of options from cart");
            return 0;
        }
    }

    public function category()
    {
        return $this->belongsToMany(Category::class, 'oc_product_to_category', 'product_id', 'category_id', 'product_id', 'category_id');
    }

    public function special()
    {
        return $this->hasOne(ProductSpecial::class, 'product_id', 'product_id')
            ->where('priority', 1)
            ->where('customer_group_id', 1)
            ->whereRaw('current_date() between date_start and date_end');
    }

    public function price()
    {
        return $this->special ? $this->special->price : $this->price;
    }
}
