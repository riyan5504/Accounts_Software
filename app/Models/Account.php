<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'date' => 'date', // বা 'datetime' যদি time থাকে
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
    
}
