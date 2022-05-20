<?php

namespace App\Models\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Design extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image',
        'title',
        'description',
        'slug',
        'close_to_comment',
        'is_live'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
