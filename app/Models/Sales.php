<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sales extends Model
{
    use HasFactory, CompanyScope, SoftDeletes;

    protected $fillable = [
        'company_id',
        'customer_id',
        'date',
        'invoice_no',
        'reference',
        'narration',
        'payment_status',
        'payment_account_id',
        'sub_total',
        'vat_amt',
        'dis_percent',
        'dis_amt',
        'grand_total',
        'due_amt',
        'pay_receive',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'payment_account_id', 'id');
    }

    public function salesItems()
    {
        return $this->hasMany(SalesItems::class, 'sales_id', 'id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'module_id')
            ->where('module_type', 'sales');
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'module_id')
            ->where('module_type', 'purchase');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeByDateRange($query, $fromDate, $toDate)
    {
        return $query->whereBetween('date', [$fromDate, $toDate]);
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function customerPayment()
    {
        return $this->hasMany(CustomerPayment::class, 'sales_id', 'id');
    }

    public function customerPaymentDetails()
    {
        return $this->hasMany(CustomerPaymentDetails::class, 'sales_id', 'id');
    }

    public function salesReturns()
    {
        return $this->hasMany(SalesReturn::class, 'sales_id', 'id');
    }

    public function salesReturnItems()
    {
        return $this->hasMany(salesReturnItems::class, 'sales_id', 'id');
    }
}
