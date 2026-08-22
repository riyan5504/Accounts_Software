<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OverheadCost extends Model
{
    use HasFactory, CompanyScope, SoftDeletes;

    protected $fillable = [
        'company_id',
        'production_id',
        'overhead_type',
        'fo_cost_amt',
    ];

    public function production()
    {
        return $this->belongsTo(Production::class, 'production_id', 'id');
    }
}
