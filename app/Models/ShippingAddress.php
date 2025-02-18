<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    use HasFactory;

    public $timestamps  = false;
    
    protected $table = 'address';
    
    protected $fillable = ['name', 'surname', 'email', 'street', 'number', 'code', 'city', 'countryid'];

    public function order()
    {
        return $this->hasOne(Order::class, 'addressid');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'countryid');
    }
}
