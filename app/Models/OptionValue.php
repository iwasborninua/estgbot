<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionValue extends Model
{

    protected $table = 'oc_option_value';
    protected $primaryKey = 'option_value_id';

    public function description()
    {
        return $this->hasOne(OptionValueDescription::class, 'option_id', 'option_id');
    }

}
