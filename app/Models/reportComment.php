<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reportComment extends Model
{
    use HasFactory;
    protected $fillable = [
        'comment_id','user_id','Report_type','text'
    ];
    public function user(){
        return $this->belongsToMany(User::class,'user_id');
    }
    public function comment(){

        return $this->belongsTo(ProjectComments::class,'comment_id');

        }




}
