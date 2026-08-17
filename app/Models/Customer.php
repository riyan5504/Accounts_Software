<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
     use HasFactory, CompanyScope, SoftDeletes;
    protected $fillable = [
    'company_id',
    'c_name',
    'phone',
    'email',
    'address',
    'opening_balance',
];

    public function sales()
    {
        return $this->hasMany(Sales::class, 'customer_id', 'id');
    }
    public function journalEntry()
    {
        return $this->hasMany(JournalEntry::class, 'customer_id', 'id');
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'customer_id', 'id');
    }
    public function salesReturn()
    {
        return $this->hasMany(SalesReturn::class, 'customer_id', 'id');
    }
}
