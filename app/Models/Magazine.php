<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelLike\Traits\Likeable;

class Magazine extends Model
{
    use HasFactory , Likeable;
    protected $fillable = [
        "title",
        "slug",
        "image",
        "user_id",
        "pdf",
        "body"
    ];
    public function comments(){
        return $this->morphMany(Comment::class , "commentable");
    }
    public function articles(){
        return $this->hasMany(Article::class);
    }
    public function views(){
        return $this->morphMany(View::class , "viewable");
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function categories(){
        return $this->morphToMany(Category::class , "categorizable");
    }
}
