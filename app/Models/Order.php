<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    public $timestamps  = false;
    protected $table = 'order';

    protected $fillable = [
        'status',
        'purchaseDate',
        'totalPrice',
        'userId',
        'addressId',
    ];

    protected $dates = [
        'purchaseDate',
    ];

    public function bookOrders()
    {
        return $this->hasMany(BookOrder::class, 'orderid');
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_order', 'orderid', 'bookid')
                    ->withPivot('quantity', 'price');
    }

    public function getTotalAttribute()
    {
        return $this->books->sum(function ($book) {
            return $book->pivot->quantity * $book->price;
        });
    }

    public function address()
    {
        return $this->belongsTo(ShippingAddress::class, 'addressid');
    }

}
