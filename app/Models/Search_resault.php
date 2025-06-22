<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Search_resault extends Model
{
    use HasFactory;
    public function search(){
        return $this->belongsTo(search::class);
    }
    public function articles(){
        return $this->hasMany(article::class);
    }
    public function news(){
        return $this->hasMany(khabar::class);
    }
    public function events(){
        return $this->hasMany(event::class);
    }
}
