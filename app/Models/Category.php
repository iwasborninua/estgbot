<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $table = 'oc_category';
    protected $primaryKey = 'category_id';

    public function description()
    {
        return $this->hasMany(CategoryDescription::class, 'category_id', 'category_id');
    }
}
