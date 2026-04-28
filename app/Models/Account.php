<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, CompanyScope, SoftDeletes;
    protected $fillable = [
        'company_id',
        'account_name',
        'ac_type',
        'ac_cat',
        'op_balance'
    ];
    
    // Expense এর expense_account_id এর সাথে relation
    public function expenseEntries()
    {
        return $this->hasMany(Expense::class, 'expense_account_id', 'id');
    }
    public function purchase()
    {
        return $this->hasMany(Purchase::class, 'account_id', 'id');
    }

    // Expense এর payment_account_id এর সাথে relation
    public function paymentEntries()
    {
        return $this->hasMany(Expense::class, 'payment_account_id', 'id');
    }
    public function journalEntry()
    {
        return $this->hasMany(JournalEntry::class, 'account_id', 'id');
    }

    public function partners()
    {
        return $this->hasMany(Partner::class, 'account_id', 'id');
    }   
}
