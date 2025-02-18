<?php

namespace App\Models;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserLbaw extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    public $timestamps  = false;

    protected $table = 'user';

    /**
     * @var array<int, array{
     *     id: int,
     *     username: string,
     *     email: string,
     *     password: string,
     *     status: bool
     * }> $user
     */

    protected $fillable = [
        'username',
        'email',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

}