<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Purchase extends Model
{
    use HasFactory, CompanyScope;

    protected $fillable = [
        'company_id',
        'vendor_id',
        'date',
        'invoice_no',
        'sub_total',
        'vat_amt',
        'dis_percent',
        'dis_amt',
        'grand_total',
        'due_amt',
        'reference',
        'narration',
        'pay_to',
        'payment_status',
        'debit_account_id',
        'credit_account_id',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'sub_total' => 'decimal:2',
        'vat_amt' => 'decimal:2',
        'dis_percent' => 'decimal:3',
        'dis_amt' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'due_amt' => 'decimal:2',
    ];

    // Constants
    const STATUS_PAID = 'paid';
    const STATUS_UNPAID = 'unpaid';
    const STATUS_PARTIAL = 'partial';

    const PAYMENT_CASH = 'cash';
    const PAYMENT_BANK = 'bank';
    const PAYMENT_CHEQUE = 'cheque';
    const PAYMENT_MOBILE = 'mobile_bank';
    const PAYMENT_DUE = 'due';

    // Relationships
    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function debitAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'debit_account_id');
    }

    public function creditAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'credit_account_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'module_id')
            ->where('module_type', 'purchase');
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'module_id')
            ->where('module_type', 'purchase');
    }
    public function vendorPayments(): HasMany
    {
        return $this->hasMany(VendorPayment::class, 'module_id')
            ->where('module_type', 'vendor_payment');
    }

    public function inventoryLedgers(): MorphMany
    {
        return $this->morphMany(InventoryLedger::class, 'module', 'module_type', 'module_id');
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('payment_status', self::STATUS_PAID);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', self::STATUS_UNPAID);
    }

    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }

    // Accessors
    public function getTotalPaidAttribute(): float
    {
        return $this->transactions->sum('paid_amt');
    }

    public function getRemainingDueAttribute(): float
    {
        return max(0, $this->grand_total - $this->total_paid);
    }

    public function getPaymentMethodNameAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->transactions->first()?->payment_method ?? ''));
    }

    // Helper Methods
    public function isFullyPaid(): bool
    {
        return $this->payment_status === self::STATUS_PAID;
    }

    public function isUnpaid(): bool
    {
        return $this->payment_status === self::STATUS_UNPAID;
    }

    public function isPartial(): bool
    {
        return $this->payment_status === self::STATUS_PARTIAL;
    }

    public function updatePaymentStatus(): void
    {
        $totalPaid = $this->total_paid;

        if ($totalPaid >= $this->grand_total) {
            $this->payment_status = self::STATUS_PAID;
        } elseif ($totalPaid > 0) {
            $this->payment_status = self::STATUS_PARTIAL;
        } else {
            $this->payment_status = self::STATUS_UNPAID;
        }

        $this->due_amt = $this->remaining_due;
        $this->save();
    }
}