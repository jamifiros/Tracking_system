<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResetCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'created_at',
    ];

    public function user(){
        return $this->belongsTo(User::class,'id','user_id');
    }
}
