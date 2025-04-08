<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyFiscalYear extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'financial_year_from',
        'financial_year_to',
        'description',
        'is_active'
    ];

    protected $casts = [
        'financial_year_from' => 'date',
        'financial_year_to' => 'date',
        'is_active' => 'boolean'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
