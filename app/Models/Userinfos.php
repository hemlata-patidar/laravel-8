<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Userinfos extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'users_id',
        'users_name',
        'profile_image',
        'gender'
    ];
}
