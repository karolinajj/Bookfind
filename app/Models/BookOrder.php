<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookOrder extends Model
{
    use HasFactory;
    public $timestamps  = false;
    protected $table = 'book_order';
    
    protected $fillable = [
        'orderId',
        'bookId',
        'quantity',
        'price',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class, 'bookid');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'orderid');
    }

}
