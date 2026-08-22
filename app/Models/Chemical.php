<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chemical extends Model
{
    use HasFactory, CompanyScope, SoftDeletes;

    protected $fillable = [
        'company_id',
        'production_id',
        'item_id',
        'used_percent',
        'used_qty',
        'ch_unit',
        'u_price',
        't_price',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function production()
    {
        return $this->belongsTo(Production::class, 'production_id', 'id');
    }
}
