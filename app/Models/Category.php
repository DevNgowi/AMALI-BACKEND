<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = ['name', 'item_group_id', 'description'];

    public function itemGroup()
    {
        return $this->belongsTo(ItemGroup::class);
    }
}
