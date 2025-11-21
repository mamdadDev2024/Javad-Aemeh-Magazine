<?php

namespace App\Models;

use App\HasSlug;
use Illuminate\Database\Eloquent\Model;

class Recommend extends Model
{
    use HasSlug;

    protected $fillable = [
        'title',
        'user_id',
        'slug',
        'pdf',
        'word',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
