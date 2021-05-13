<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;


class Userinfos extends Model
{
    use HasApiTokens, HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'userinfos';
    protected $fillable = [
        'users_id',
        'users_name',
        'profile_image',
        'gender',
        'deleted_at'
    ];
}
