<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    public $timestamps  = false;
    protected $table = 'book';

    protected $fillable = [
        'title',
        'numberOfPages',
        'edition',
        'format',
        'status',
        'price',
        'categoryId',
        'image'
    ];

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'book_author', 'id_book', 'id_author');
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'categoryid');
    }

    public function bookInCarts()
    {
        return $this->hasMany(BookInCart::class, 'bookid');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'book_order', 'bookid', 'orderid')
                    ->withPivot('quantity', 'price');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'bookid');
    }

    public function scopeActive($query)
    {
        return $query->where('status', TRUE);
    }


}
