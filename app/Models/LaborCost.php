<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaborCost extends Model
{
    use HasFactory, CompanyScope, SoftDeletes;
    protected $fillable = [
        'company_id',
        'production_id',
        'labor_name',
        'duty_day',
        'd_pay',
        'total_pay',
    ];

    public function production()
    {
        return $this->belongsTo(Production::class, 'production_id', 'id');
    }
}
