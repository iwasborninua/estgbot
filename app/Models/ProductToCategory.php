<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductToCategory extends Model
{
    protected $table = 'oc_product_to_category';
    protected $primaryKey = 'product_id';
    public $timestamps = false;


    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
