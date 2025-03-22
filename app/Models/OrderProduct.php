<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{

    protected $table = 'oc_order_product';
    protected $primaryKey = 'order_product_id';


    public function orderOption()
    {
        return $this->hasOne(OrderOption::class, 'order_product_id', 'order_product_id');
    }

}
