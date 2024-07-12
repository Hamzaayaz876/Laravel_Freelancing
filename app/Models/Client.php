<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $table = 'clients';
    protected $fillable=[
        'user_id','company_name','company_owners','website_link','total_spent','total_posted_jobs'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function projects(){
        return $this->hasMany(Project::class,'client_id');
    }


    public function moneyhandler(){
        return $this->hasMany(moneyHandler::class,'client_id');
    }
    public function Conversation()
{
    return $this->hasMany(Conversation::class,'client_id');
}
public function rating()
{
    return $this->hasMany(Rating::class,'client_id');
}
}
