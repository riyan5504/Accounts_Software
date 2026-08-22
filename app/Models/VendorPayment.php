<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPayment extends Model
{
    use HasFactory, CompanyScope;

    protected $fillable = [
        'company_id',
        'vendor_id',
        'date',
        'voucher_no',
        'reference',
        'debit_account_id',
        'credit_account_id',
        'payment_method',
        'payment_status',
        'paid_amount',
        'remarks',
        'created_by',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'id');
    }
    public function vendorPaymentDetails()
    {
        return $this->hasMany(VendorPaymentDetails::class, 'vendor_payment_id', 'id');
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'module_id')
            ->where('module_type', 'return');
    }
}
