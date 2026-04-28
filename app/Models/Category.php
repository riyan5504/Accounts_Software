<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, CompanyScope, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'company_id',
        'cat_name',
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'cat_id', 'id');
    }
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class, 'cat_id', 'id');
    }
    
}
