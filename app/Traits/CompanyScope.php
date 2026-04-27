<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait CompanyScope
{
    protected static function bootCompanyScope()
    {
        static::addGlobalScope('company', function (Builder $query) {

            if (!auth()->check()) {
                return;
            }

            $user = auth()->user();

            // super admin bypass
            if ($user->role === 'super_admin') {
                return;
            }

            if ($user->company_id) {
                $query->where('company_id', $user->company_id);
            }
        });

        static::creating(function ($model) {

            if (!auth()->check()) {
                return;
            }

            $user = auth()->user();

            if ($user->role !== 'super_admin' && $user->company_id) {
                $model->company_id = $user->company_id;
            }
        });
    }
}