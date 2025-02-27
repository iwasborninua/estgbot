<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOptionValue extends Model
{
    protected $table = 'oc_product_option_value';
    protected $primaryKey = 'product_option_value_id';
    public $timestamps = false;


    public function value()
    {
        return $this->hasMany(OptionValue::class, 'option_value_id', 'option_value_id');
    }

    public function description()
    {
        return $this->hasOne(OptionValueDescription::class, 'option_value_id', 'option_value_id')
            ->where('language_id', config('constants.lang'));
    }

}
