<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JournalEntry extends Model
{
    use HasFactory, CompanyScope;

    protected $fillable = [
        'company_id',
        'module_type',
        'module_id',
        'reference_no',
        'date',
        'particulars',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Relationships
    public function module(): MorphTo
    {
        return $this->morphTo('module', 'module_type', 'module_id');
    }

    public function journalItems(): HasMany
    {
        return $this->hasMany(JournalItems::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getTotalDebitAttribute(): float
    {
        return $this->journalItems->sum('debit');
    }

    public function getTotalCreditAttribute(): float
    {
        return $this->journalItems->sum('credit');
    }

    public function isBalanced(): bool
    {
        return $this->total_debit === $this->total_credit;
    }

    // Scopes
    public function scopeByModule($query, $type, $id)
    {
        return $query->where('module_type', $type)->where('module_id', $id);
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }
}