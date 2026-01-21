<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'date' => 'date', // বা 'datetime' যদি time থাকে
    ];

    public function module()
    {
        return $this->morphTo(__FUNCTION__, 'module_type', 'module_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id', 'id');
    }
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'id');
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }
    public function production()
    {
        return $this->belongsTo(Production::class, 'production_id', 'id');
    }
    
}
