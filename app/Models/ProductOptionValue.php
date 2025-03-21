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

    public function option()
    {
        return $this->hasOne(Option::class, 'option_id', 'option_id');
    }

    public function description()
    {
        return $this->hasOne(OptionValueDescription::class, 'option_value_id', 'option_value_id')
            ->where('language_id', config('constants.lang'));
    }

    public function inCart(): bool
    {
        return (\Arr::has(\Auth::user()->getCart(), $this->product_id) and
            \Arr::has(\Auth::user()->getCart()[$this->product_id], $this->product_option_value_id));
    }

    public function inCartCount()
    {
        if ($this->inCart()) {
            return \Auth::user()->getCart()[$this->product_id][$this->product_option_value_id];
        } else {
            \Log::warning("product_option_value_id [$this->product_option_value_id] not in cart, but trying to get count of options from cart");
            return 0;
        }
    }
}
