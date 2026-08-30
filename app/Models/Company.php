<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',        
        'address',
        'phone',
        'short_name',
        'website',
        'logo',
        'tax_number',
        'registration_number',
        'contact_person',
        'established_date',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function partners()
    {
        return $this->hasMany(Partner::class, 'company_id', 'id');
    }
}