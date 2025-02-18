<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    public $timestamps  = false;

    protected $table = 'shoppingcart';
    
    protected $fillable = ['price', 'userid'];

    public function booksincart()
    {
        return $this->hasMany(BookInCart::class, 'cartid', 'id');
    }

}