<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, CompanyScope, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'company_id',
        'item_code',
        'item_name',
        'cat_id',
        'size',
        'unit_price',
        'opening_stock'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'cat_id', 'id');
    }
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class, 'item_id', 'id');
    }

    public function inventoryLedgers()
    {
        return $this->hasMany(InventoryLedger::class, 'item_id', 'id');
    }
    public function productions()
    {
        return $this->hasMany(Production::class, 'item_id', 'id');
    }
    public function chemicals()
    {
        return $this->hasMany(Chemical::class, 'item_id', 'id');
    }
    public function packagingMaterials()
    {
        return $this->hasMany(PackagingMaterial::class, 'item_id', 'id');
    }
}
