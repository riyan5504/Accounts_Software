<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use HasFactory, CompanyScope, SoftDeletes;
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
    public function investments()
    {
        return $this->hasMany(Investment::class, 'partner_id', 'id');
    }
    public function journals()
    {
        return $this->hasManyThrough(
            JournalEntry::class,
            Investment::class,
            'partner_id', // Investment table foreign key
            'module_id',  // Journal table foreign key
            'id',
            'id'
        )->where('module_type', 'investment');
    }
}
