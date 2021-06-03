<?php

namespace App\Models;

//use Overtrue\LaravelFollow\Traits\CanBeLiked;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelLike\Traits\Likeable;

class Post extends Model
{
    use HasFactory, Likeable;

    protected $fillable = [
        'title',
        'user_id',
        'image',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // public function user()
    // {
    //     return $this->hasOne(User::class);
    // }

    
}
