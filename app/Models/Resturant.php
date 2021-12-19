<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resturant extends Model
{
    use HasFactory;

    protected $casts = [
        'start_shift' => 'date:H:i:s',
        'end_shift'=>'date:H:i:s'
    ];
}
