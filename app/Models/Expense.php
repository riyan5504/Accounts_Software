<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, CompanyScope, SoftDeletes;

    protected $casts = [
        'date' => 'date', // বা 'datetime' যদি time থাকে
    ];

    protected $fillable = [
        'date',
        'company_id',
        'voucher_no',
        'reference_no',
        'expense_account_id',
        'payment_account_id',
        'payment_method',
        'payment_status',
        'sub_total',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'due_amount',
        'attachment',
        'expense_type',
        'created_by',
        'pay_to'
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
