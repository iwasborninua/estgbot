<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $table = 'oc_product_attribute';
    protected $primaryKey = 'product_id';
    public $timestamps = false;

    public function attributeDescription()
    {
        return $this->hasOne(AttributeDescription::class, 'attribute_id', 'attribute_id')
            ->where('language_id', config('constants.lang'));
    }

    public function attribute()
    {
        return $this->hasOne(Attribute::class, 'attribute_id', 'attribute_id');
    }

}
