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
}
