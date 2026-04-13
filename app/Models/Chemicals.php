<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chemicals extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function items()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function production()
    {
        return $this->belongsTo(Production::class, 'production_id', 'id');
    }
}
