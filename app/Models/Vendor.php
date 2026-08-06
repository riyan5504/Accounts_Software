<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, CompanyScope, SoftDeletes;
    protected $fillable = [
    'company_id',
    'v_name',
    'phone',
    'email',
    'address',
    'opening_balance',
];

    public function purchase()
    {
        return $this->hasMany(Purchase::class, 'vendor_id', 'id');
    }
    public function journalEntry()
    {
        return $this->hasMany(JournalEntry::class, 'vendor_id', 'id');
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'vendor_id', 'id');
    }
    public function purchaseReturn()
    {
        return $this->hasMany(PurchaseReturn::class, 'vendor_id', 'id');
    }
}
