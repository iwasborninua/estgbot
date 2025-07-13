<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOptionSpecial extends Model
{
    protected $table = 'oc_product_option_special';
    protected $primaryKey = 'option_special_id';
    public $timestamps = false;


    public function values()
    {
        return $this->hasMany(ProductOptionSpecial::class, 'product_option_value_id', 'product_option_value_id');
    }
}
