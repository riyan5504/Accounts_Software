<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;
    use CompanyScope;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date', // বা 'datetime' যদি time থাকে
    ];

    public function items()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
    public function journalEntry()
    {
        return $this->hasMany(JournalEntry::class, 'production_id', 'id');
    }
    public function laborCost()
    {
        return $this->hasMany(LaborCost::class, 'production_id', 'id');
    }
    public function depreciation()
    {
        return $this->hasMany(Depreciation::class, 'production_id', 'id');
    }
    public function chemicals()
    {
        return $this->hasMany(Chemicals::class, 'production_id', 'id');
    }
    public function overHeadCost()
    {
        return $this->hasMany(OverheadCost::class, 'production_id', 'id');
    }
    public function packagingMaterial()
    {
        return $this->hasMany(PackagingMaterial::class, 'production_id', 'id');
    }
    public function qcCost()
    {
        return $this->hasMany(QcCost::class, 'production_id', 'id');
    }
    public function sectionTotalCost()
    {
        return $this->hasOne(SectionTotalCost::class, 'production_id', 'id');
    }
    public function transportCost()
    {
        return $this->hasMany(TransportCost::class, 'production_id', 'id');
    }
    public function utilityCost()
    {
        return $this->hasMany(UtilityCost::class, 'production_id', 'id');
    }
}
