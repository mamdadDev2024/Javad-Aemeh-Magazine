<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    use HasFactory;
    protected $fillable = [
        "text"
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function resaults(){
        return $this->hasMany(search_resault::class);
    }
}
