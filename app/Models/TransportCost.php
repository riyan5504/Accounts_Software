<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportCost extends Model
{
    use HasFactory, CompanyScope, SoftDeletes;

    protected $fillable = [
        'company_id',
        'production_id',
        'transport_type',
        'transport_amt',
    ];

    public function production()
    {
        return $this->belongsTo(Production::class, 'production_id', 'id');
    }
}
