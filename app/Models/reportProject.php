<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reportProject extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id','user_id','Report_type','text'
    ];
    public function user(){
        return $this->belongsToMany(User::class,'user_id');
    }
    public function project(){

        return $this->belongsTo(Project::class,'project_id');

        }
}
