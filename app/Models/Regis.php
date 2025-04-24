<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regis extends Model
{
    use HasFactory;
    protected $fillable = [
        'member_id',
        'package_id',
        'name',
        'email',
        'package_name',
        'duration',
        'join_date',
        'end_date',
        'status',
    ];
}
