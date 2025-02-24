<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductToStore extends Model
{
    protected $table = 'oc_product_to_store';
    protected $primaryKey = 'product_id';
    public $timestamps = false;

}
