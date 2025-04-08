<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualDevice extends Model
{
    /** @use HasFactory<\Database\Factories\VirtualDeviceFactory> */
    use HasFactory;

    protected $fillable = [
        'name'
    ];
}
