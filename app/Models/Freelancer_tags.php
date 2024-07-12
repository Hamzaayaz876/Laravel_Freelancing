<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Freelancer_tags extends Model
{
    use HasFactory;
    protected $fillable=[
        'Freelancer_id','Tag_name'
    ];
    public function freelancers()
{
    return $this->belongsTo(Freelancer::class,'freelancer_id');
}


}
