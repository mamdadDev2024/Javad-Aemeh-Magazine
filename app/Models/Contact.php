<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        "body",
        "number"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replay($reply)
    {
        $this->replay = $reply;
        $this->status = 1;
        $this->save();
    }
}
