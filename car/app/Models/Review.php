<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    //
    protected $fillable = ['car_id', 'rating','session_id'];

public function car()
{
    return $this->belongsTo(Car::class);
}
}
