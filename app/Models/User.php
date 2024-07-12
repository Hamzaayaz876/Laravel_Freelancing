<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'position',
        'State',
        'freeze',
        'money_amount',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function getTimeLeftForFutureDate()
{
    $futureDate = $this->freeze; // Replace 'your_datetime_attribute' with the actual attribute name in your user table
    if($futureDate){
    $now = Carbon::now();
    $future = Carbon::parse($futureDate);

    if ($future->isFuture()) {
        $timeLeft = $future->diffForHumans($now);
        return $timeLeft;
    }
    }
    return null; // The datetime attribute is not in the future

}

}
