<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class project_tags extends Model
{
    use HasFactory;
    protected $fillable=[
        'project_id','Tag_name'
    ];
}
