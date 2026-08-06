<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
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
        'reference',
        'narration',
        'debit_account_id',
        'credit_account_id',
        'created_by',
        'payment_status',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];
    public function purchaseReturnItems()
    {
        return $this->hasMany(PurchaseReturnItem::class, 'return_id', 'id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'module_id')
            ->where('module_type', 'return');
    }
}
