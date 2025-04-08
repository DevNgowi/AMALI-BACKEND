<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = 'user_roles';

    protected $fillable = [
        'user_id', 'role_id'
    ];

    // public function users(){
    //     return $this->belongsTo(User::class);
    // }
    // public function roles(){
    //     return $this->belongsTo(Role::class);
    // }
}
