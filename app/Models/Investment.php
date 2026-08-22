<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Investment extends Model
{
    use HasFactory, CompanyScope, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'company_id',
        'partner_id',
        'date',
        'amount',
        'invest_type',
        'attachment',
        'reference',
        'note',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function journals()
    {
        return $this->morphMany(JournalEntry::class, 'module');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
