<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;
    protected $table = 'visits';
    protected $fillable = [
        'destination_id',
        'user_id',
       'lattitude',
        'longitude',
       'remarks',
        'dest_img',
       'meter_img'
    ];


    

    public function destination(){
        return $this->belongsTo(Destination::class,'id','visit_id');
    }
    
    public function user(){
        return $this->belongsTo(User::class,'id','destination_id');
    }
}
