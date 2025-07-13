<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSpecial extends Model
{
    protected $table = 'oc_product_special';
    protected $primaryKey = 'product_special_id';
    public $timestamps = false;

    public function options ()
    {
        return $this->hasMany(ProductOptionSpecial::class, 'special_id', 'oc_product_special_id');
    }

}
