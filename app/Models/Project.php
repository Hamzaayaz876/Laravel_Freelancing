<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable=[
        'client_id','Title','Description','Level','Comments_Number','Applications_Number','State','Budget','Application_Dealine','skill_name','tags','Category'
    ];
    public function Client(){
        return $this->belongsTo(Client::class,'client_id');
    }

    public function tags()
    {
        return $this->hasMany(project_tags::class, 'project_id');
    }

    public function comments(){

        return $this->hasMany(ProjectComments::class);

        }
        public function applications(){

            return $this->hasMany(ProjectApplications::class);

            }
        public function Conversation()
{
    return $this->hasOne(Conversation::class,'project_id');
}
public function rating()
{
    return $this->hasOne(rating::class,'project_id');
}
public function report(){

    return $this->hasMany(reportProject::class,'project_id');

    }
}
