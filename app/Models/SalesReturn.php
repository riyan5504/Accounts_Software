<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    use HasFactory, CompanyScope;
    protected $fillable = [
        'company_id',
        'customer_id',
        'sales_id',
        'date',
        'invoice_no',
        'reference',
        'narration',
        'payment_status',
        'debit_account_id',
        'credit_account_id',
        'sub_total',
        'vat_amt',
        'dis_percent',
        'dis_amt',
        'grand_total',
        'created_by',
    ];

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id', 'id');
    }

    public function salesReturnItems()
    {
        return $this->hasMany(salesReturnItems::class, 'return_id', 'id');
    }
}
