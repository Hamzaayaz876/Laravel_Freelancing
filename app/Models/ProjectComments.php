<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectComments extends Model
{
    use HasFactory;
    protected $fillable=[
         'Freelancer_id','Project_id','Text','State'
      ];

      public function freelancer(){
        return $this->belongsTo(Freelancer::class, 'Freelancer_id');
    }

    public function project(){

        return $this->belongsTo(Project::class,'Project_id');

        }

        public function report(){

            return $this->hasMany(reportComment::class,'comment_id');

            }

}
