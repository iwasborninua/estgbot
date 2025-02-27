<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{

    protected $table = 'oc_attribute';
    protected $primaryKey = 'attribute_id';

    public function description()
    {
        return $this->hasMany(AttributeDescription::class, 'attribute_id', 'attribute_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

}
