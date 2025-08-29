<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get all of the articles, events, and news for the category.
     */
    public function articles()
    {
        return $this->morphedByMany(Article::class, 'categorizable');
    }
    /**
     * Get all of the articles associated with the category.
     */
    public function events()
    {
        return $this->morphedByMany(Event::class, 'categorizable');
    }

    public function news()
    {
        return $this->morphedByMany(Khabar::class, 'categorizable');
    }

    
    public function magazines()
    {
        return $this->morphedByMany(Magazine::class, 'categorizable');
    }
}
