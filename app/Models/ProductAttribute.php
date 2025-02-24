<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $table = 'oc_product_attribute';
    protected $primaryKey = 'product_id';
    public $timestamps = false;



}
