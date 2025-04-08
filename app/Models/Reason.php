<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    /** @use HasFactory<\Database\Factories\ReasonFactory> */
    use HasFactory;

    protected $table = 'reasons';

    protected $fillable = [
       'reason_type_id', 'description'
    ];

    public function reasonType(){
        return $this->belongsTo(ReasonType::class);
    }
}
