<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'destName',
        'contactNo',
        'Location',
        'status',
        'visited'
    ];
    public function user(){
        return $this->belongsTo(user::class,'id','user_id');
    }

    public function visit()
    {
        return $this->hasOne(Visit::class,'destination_id','id');
    }

   
}
