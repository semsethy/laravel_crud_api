<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'title', 'email', 'phone_number', 'facebook_link', 'instagram_link', 'twitter_link', 'icon', 'logo'
    ];
}
