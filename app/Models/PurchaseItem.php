<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    use HasFactory, CompanyScope;

    protected $fillable = [
        'company_id',
        'purchase_id',
        'item_id',
        'qty',
        'unit_price',
        'price',
        'vat_percent',
        'vat_amount',
        'total_price',
    ];

    protected $casts = [
        'qty' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'price' => 'decimal:2',
        'vat_percent' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    // Calculated price without VAT
    public function getPriceExcludingVatAttribute(): float
    {
        return $this->total_price - $this->vat_amount;
    }
}