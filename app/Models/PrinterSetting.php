<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrinterSetting extends Model
{
    protected $fillable = [
        'virtual_device_id', 
        'printer_name',
        'printer_ip', 
        'printer_type',
        'paper_size'
    ];
}
