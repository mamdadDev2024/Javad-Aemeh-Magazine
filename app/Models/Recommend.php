<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recommend extends Model
{
    protected $fillable = [
        "title",
        "user_id",
        "slug",
        "pdf",
        "word"
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
