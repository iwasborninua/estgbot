<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{

    protected $table = 'oc_option';
    protected $primaryKey = 'option_id';

    public function description()
    {
        return $this->hasOne(OptionDescription::class, 'option_id', 'option_id')
            ->where('language_id', config('constants.lang'));
    }

}
