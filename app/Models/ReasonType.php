<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReasonType extends Model
{
    protected $fillable = ['name'];

    public function reasons()
    {
        return $this->hasMany(Reason::class);
    }
}
