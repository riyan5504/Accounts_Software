<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function item()
    {
        return $this->hasMany(Item::class, 'cat_id', 'id');
    }
    public function purchaseItem()
    {
        return $this->hasMany(PurchaseItem::class, 'cat_id', 'id');
    }
    
}
