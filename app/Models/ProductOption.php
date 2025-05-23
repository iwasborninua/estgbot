<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    protected $table = 'oc_product_option';
    protected $primaryKey = 'product_option_id';
    public $timestamps = false;

    public function values()
    {
        return $this->hasMany(ProductOptionValue::class, 'product_option_id', 'product_option_id')
            ->where('status', 1);
    }

    public function description()
    {
        return $this->hasMany(OptionDescription::class, 'option_id', 'option_id');
    }
}
