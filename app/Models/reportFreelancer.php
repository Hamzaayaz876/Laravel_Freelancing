<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reportFreelancer extends Model
{
    use HasFactory;
    protected $fillable = [
        'Freelancer_id','user_id','Report_type','text'
    ];
    public function user(){
        return $this->belongsToMany(User::class,'user_id');
    }
    public function freelancer(){
        return $this->belongsTo(Freelancer::class,'Freelancer_id');
    }
}
