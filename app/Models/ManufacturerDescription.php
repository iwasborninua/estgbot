<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManufacturerDescription extends Model
{

    protected $table = 'oc_manufacturer_description';
    protected $primaryKey = 'manufacturer_id';

    public static function getCategoryMenu()
    {
        return [];
    }
}
