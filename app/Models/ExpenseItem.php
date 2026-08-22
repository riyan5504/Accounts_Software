<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseItem extends Model
{
    use HasFactory, CompanyScope, SoftDeletes;

    protected $fillable = [
        'company_id',
        'expense_id',
        'particulars',
        'qty',
        'rate',
        'amount',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id', 'id');
    }
}
