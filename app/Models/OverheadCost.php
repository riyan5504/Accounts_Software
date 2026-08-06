<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OverheadCost extends Model
{
    use HasFactory, CompanyScope, SoftDeletes;

    protected $guarded = [];

    public function production()
    {
        return $this->belongsTo(Production::class, 'production_id', 'id');
    }
}
