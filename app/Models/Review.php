<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    public $timestamps  = false;
    protected $table = 'review';

    protected $fillable = ['score', 'content', 'userid', 'bookid' ];

    public function user()
    {
        return $this->belongsTo(UserLbaw::class, 'userid');
    }
    
}
