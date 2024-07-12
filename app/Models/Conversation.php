<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;
    protected $fillable=[
        'Freelancer_id','Project_id','State','client_id'
    ];
    public function freelancers()
        {
        return $this->belongsToMany(Freelancer::class,'freelancer_id');
        }

        public function project(){

            return $this->belongsTo(Project::class,'Project_id');

            }
            public function Client(){
                return $this->belongsTo(Client::class,'client_id');
            }
            public function message(){
                return $this->hasMany(message::class,'conversation_id');
            }


}
