<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectApplications extends Model
{
    use HasFactory;
    protected $table = 'project_applications';
    protected $fillable=[
  'Freelancer_id','Project_id','Cover_Letter','State'
    ];

    public function freelancer(){
        return $this->belongsTo(Freelancer::class,'Freelancer_id');
    }
    public function project(){

        return $this->belongsTo(Project::class,'Project_id');

        }
}
