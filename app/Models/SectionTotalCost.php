<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionTotalCost extends Model
{
    use HasFactory, CompanyScope, SoftDeletes;

    protected $fillable = [
        'company_id',
        'production_id',
        'raw_grand_price',
        'pack_grand_price',
        'labor_grand_price',
        'depreciation_grand_price',
        'utility_grand_price',
        'overhead_grand_price',
        'transport_grand_price',
        'qc_grand_price',
    ];

    public function production()
    {
        return $this->belongsTo(Production::class, 'production_id', 'id');
    }
}
