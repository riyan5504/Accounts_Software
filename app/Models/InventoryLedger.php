<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLedger extends Model
{
    use HasFactory, CompanyScope;

    protected $fillable = [
        'company_id',
        'item_id',
        'module_type',
        'module_id',
        'qty_in',
        'qty_out',
        'unit_cost',
        'total_cost',
        'date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}
