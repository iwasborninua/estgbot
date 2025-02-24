<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDescription extends Model
{
    protected $table = 'oc_product_description';
    protected $primaryKey = 'product_id';
    public $timestamps = false;



}
