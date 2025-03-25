<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{

    protected $table = 'oc_manufacturer';
    protected $primaryKey = 'manufacturer_id';


    public function description()
    {
        return $this->hasMany(CategoryDescription::class, 'category_id', 'category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'manufacturer_id', 'manufacturer_id');
    }


    public static function menuQuery($page)
    {
        return Manufacturer::query()
            ->select(['manufacturer_id', 'name'])
            ->withCount(['products' => function ($q) {
                $q->where('quantity', '>', 0);
            }])
            ->whereHas('products', function ($q) {
                $q->where('quantity', '>', 0);
            })
            ->orderBy('tg_sort_order')
            ->paginate(10, ["*"], 'page', $page);
    }

}
