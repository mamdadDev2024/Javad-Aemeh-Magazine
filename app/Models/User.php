<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Overtrue\LaravelLike\Traits\Liker;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Liker, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'age',
        'email',
        'status',
        'password',
        'number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function recommends()
    {
        return $this->hasMany(Recommend::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function magazines()
    {
        return $this->hasMany(Magazine::class);
    }

    public function news()
    {
        return $this->hasMany(khabar::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
