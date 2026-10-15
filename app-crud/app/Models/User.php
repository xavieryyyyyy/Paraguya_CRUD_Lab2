<?php

// User model for the users table. Authentication uses this class to load accounts. HasFactory
// creates test users; Notifiable sends reset/verification notifications. Email verification is
// not enforced because this class does not implement MustVerifyEmail.


namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    // Hide these attributes when the model is converted to arrays/JSON. This does not remove
    // the columns or encrypt stored data; Hash::make and the hashed cast protect password
    // storage.
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    // Convert stored attributes to useful PHP values: verified time becomes a date object;
    // assigned passwords are hashed. Access level remains protected because Eloquent calls the
    // cast configuration internally.
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
