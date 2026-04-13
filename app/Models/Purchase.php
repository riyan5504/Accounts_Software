<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    use CompanyScope;
    protected $guarded = [];

    protected $casts = [
        'date' => 'date', // বা 'datetime' যদি time থাকে
    ];

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_id', 'id');
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
    public function journalEntry()
    {
        return $this->hasMany(JournalEntry::class, 'purchase_id', 'id');
    }
    public function debitAccount()
    {
        return $this->belongsTo(Account::class, 'debit_account_id', 'id');
    }

    public function paymentAccount()
    {
        return $this->belongsTo(Account::class, 'payment_account_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
