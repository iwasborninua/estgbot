<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductToLayout extends Model
{
    protected $table = 'oc_product_to_layout';
    protected $primaryKey = 'product_id';
    public $timestamps = false;

}
