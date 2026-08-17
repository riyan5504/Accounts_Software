<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturnItems extends Model
{
    use HasFactory, CompanyScope;
    protected $guarded = [];

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id', 'id');
    }

    public function salesReturn()
    {
        return $this->belongsTo(SalesReturn::class, 'return_id', 'id');
    }
}
