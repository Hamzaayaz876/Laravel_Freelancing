<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class moneyHandler extends Model
{
    use HasFactory;
    protected $fillable=[
        'Freelancer_id','Project_id','amountOfMoney'
    ];

    public function user(){

        return $this->belongsTo(user::class);

        }
        public function freelancers()
        {
        return $this->belongsToMany(Freelancer::class,'freelancer_id');
        }
    public function project(){

        return $this->belongsTo(Project::class,'Project_id');

        }
    public function client(){
        return $this->belongsToMany(client::class);

        }


}
