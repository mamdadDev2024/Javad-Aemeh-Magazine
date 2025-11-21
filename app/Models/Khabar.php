<?php

namespace App\Models;

use App\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelLike\Traits\Likeable;

class Khabar extends Model
{
    use HasFactory, HasSlug , Likeable;

    protected $fillable = [
        'title',
        'body',
        'user_id',
        'image',
        'pdf',
        'scope_id',
        'slug',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function scope()
    {
        return $this->belongsTo(Scope::class);
    }

    public function views()
    {
        return $this->morphMany(View::class, 'viewable');
    }

    public function categories()
    {
        return $this->morphToMany(Category::class, 'categorizable');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished($query)
    {
        return $query->where('status', 1);
    }
}
