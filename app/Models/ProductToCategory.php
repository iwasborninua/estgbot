<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductToCategory extends Model
{
    protected $table = 'oc_product_to_category';
    protected $primaryKey = 'product_id';
    public $timestamps = false;

}
