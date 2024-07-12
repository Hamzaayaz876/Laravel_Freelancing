<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;
    protected $fillable = [
        'Freelancer_id','client_id','Review','Project_id','number'
    ];
    public function client(){
        return $this->belongsTo(Client::class,'client_id');
    }
    public function freelancer(){
        return $this->belongsTo(Freelancer::class,'Freelancer_id');
    }
    public function project(){
        return $this->belongsTo(Project::class,'Project_id');
    }

}
