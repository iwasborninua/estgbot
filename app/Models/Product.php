<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'oc_product';
    protected $primaryKey = 'product_id';
    public $timestamps = false;

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
}
