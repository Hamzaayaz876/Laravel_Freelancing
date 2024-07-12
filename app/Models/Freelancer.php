<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Freelancer extends Model
{
    use HasFactory;
    protected $table = 'freelancer';
    protected $fillable=[
        'user_id','picture','cv','bio','firstname','lastname','skill_name','Category','Total_Rating','total_compeleted_jobs','Total_Rated_times'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function moneyhandler(){
        return $this->hasMany(moneyHandler::class,'freelancer_id');
    }

    public function tags(){

        return $this->hasMany(Freelancer_tags::class,'freelancer_id');

        }


        public function application(){
            return $this->hasMany(ProjectApplicants::class,'freelancer_id');
    }
    public function comment(){
        return $this->hasMany(ProjectComments::class,'freelancer_id');
    }
    public function Conversation()
{
    return $this->hasMany(Conversation::class,'freelancer_id');
}
public function rating()
{
    return $this->hasMany(Rating::class,'freelancer_id');
}
public function freelancerReport()
{
    return $this->hasMany(reportFreelancer::class);
}
}
