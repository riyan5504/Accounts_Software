<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model
{
    use HasFactory, CompanyScope;
    protected $guarded = [];

    public function customerPaymentDetails()
    {
        return $this->hasMany(CustomerPaymentDetails::class, 'sales_id');
    }

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id', 'id');
    }
}
