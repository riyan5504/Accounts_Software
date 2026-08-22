<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPaymentDetails extends Model
{
   use HasFactory, CompanyScope;
    protected $fillable = [
        'company_id',
        'vendor_payment_id',
        'purchase_id',
        'paid_amount',
    ];

    public function vendorPayment()
    {
        return $this->belongsTo(VendorPayment::class, 'vendor_payment_id', 'id');
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'id');
    }
}
