<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class message extends Model
{
    use HasFactory;
    protected $fillable=[
        'conversation_id','meassage','sender','reciever'
    ];
    public function conversation(){

        return $this->belongsTo(Project::class,'conversation_id');

        }
}
