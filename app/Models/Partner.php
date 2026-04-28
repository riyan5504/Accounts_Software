<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory, CompanyScope;
    protected $fillable = [
        'company_id',
        'p_name',
        'p_phone',
        'p_email',
        'p_address'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
