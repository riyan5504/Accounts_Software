<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $guarded = [];

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
    public function production()
    {
        return $this->hasMany(Production::class, 'item_id', 'id');
    }
}
