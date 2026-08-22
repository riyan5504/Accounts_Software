<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model
{
    use HasFactory, CompanyScope;
    protected $fillable = [
        'company_id',
        'customer_id',
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

    public function customerPaymentDetails()
    {
        return $this->hasMany(CustomerPaymentDetails::class, 'sales_id');
    }

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id', 'id');
    }
}
