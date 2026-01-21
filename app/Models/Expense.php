<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'date' => 'date', // বা 'datetime' যদি time থাকে
    ];

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
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function expenseItem()
    {
        return $this->hasMany(ExpenseItem::class, 'expense_id', 'id');
    }
    public function journalEntry()
    {
        return $this->hasMany(JournalEntry::class, 'expense_id', 'id');
    }
}
