<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesItems extends Model
{
    use HasFactory, CompanyScope;
    protected $fillable = [
        'sales_id',
        'item_id',
        'qty',
        'sales_price',
        'price',
        'item_vat_percent',
        'item_vat_amt',
        'total_price',
    ];

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id', 'id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
