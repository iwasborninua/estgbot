<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'tg_users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'id',
        'username',
        'first_name',
        'last_name',
        'language_code',
        'is_bot',
    ];

    public function getCart()
    {
        if (!cache()->has($this->id . 'cart')) {
            return [];
        }
        return cache()->get($this->id . 'cart');
    }

    public function setCart($cart)
    {
        cache()->set($this->id . 'cart', $cart, 60000);
    }
}
