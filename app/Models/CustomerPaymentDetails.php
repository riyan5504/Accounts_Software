<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPaymentDetails extends Model
{
    use HasFactory, CompanyScope;

    protected $fillable = [
        'company_id',
        'customer_payment_id',
        'sales_id',
        'paid_amount',
    ];

    public function customerPayment()
    {
        return $this->belongsTo(CustomerPayment::class, 'customer_payment_id');
    }

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id', 'id');
    }
}
