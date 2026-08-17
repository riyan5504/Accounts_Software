<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    use HasFactory, CompanyScope;

    protected $fillable = [
        'company_id',
        'module_type',
        'module_id',
        'reference_no',
        'vendor_id',
        'customer_id',
        'payment_method',
        'paid_amt',
        'receive_amt',
        'return_amt',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
        'paid_amt' => 'decimal:2',
        'receive_amt' => 'decimal:2',
        'return_amt' => 'decimal:2',
    ];

    // Constants
    const METHOD_CASH = 'cash';
    const METHOD_BANK = 'bank';
    const METHOD_CHEQUE = 'cheque';
    const METHOD_MOBILE = 'mobile_bank';
    const METHOD_DUE = 'due';

    // Relationships
    public function module(): MorphTo
    {
        return $this->morphTo('module', 'module_type', 'module_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    // Scopes
    public function scopeByModule($query, $type, $id)
    {
        return $query->where('module_type', $type)->where('module_id', $id);
    }

    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopePayments($query)
    {
        return $query->where('paid_amt', '>', 0);
    }

    public function scopeReturns($query)
    {
        return $query->where('return_amt', '>', 0);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}