<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookInCart extends Model
{
    use HasFactory;
    public $timestamps  = false;

    protected $table = 'bookincart';
    protected $primaryKey = 'id';
    protected $fillable = ['cartid', 'bookid', 'quantity', 'price'];

    public function book()
    {
        return $this->belongsTo(Book::class, 'bookid');
    }

}