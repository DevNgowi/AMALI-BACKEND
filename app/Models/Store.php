<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;


class Store extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'name',
        'location',
        'manager_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_stores');
    }

    public function stocks()
    {
       
        return $this->belongsToMany(Item::class, 'item_stores', 'store_id', 'item_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

}
