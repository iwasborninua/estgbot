<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryDescription extends Model
{

    protected $table = 'oc_category_description';
    protected $primaryKey = 'category_id';

    public static function getCategoryMenu()
    {
        return [];
    }
}
