<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait CompanyScope
{
    protected static function bootCompanyScope()
    {
        static::addGlobalScope('company', function (Builder $query) {

            if (app()->runningInConsole()) return;
            if (!auth()->check()) return;

            $user = auth()->user();

            if ($user->role === 'super_admin') return;

            if (!empty($user->company_id)) {
                $query->where('company_id', $user->company_id);
            }
        });

        static::creating(function ($model) {

            if (app()->runningInConsole()) return;
            if (!auth()->check()) return;

            $user = auth()->user();

            if ($user->role !== 'super_admin') {
                $model->company_id = $user->company_id;
            }
        });

        static::updating(function ($model) {

            if (app()->runningInConsole()) return;
            if (!auth()->check()) return;

            $user = auth()->user();

            if ($user->role !== 'super_admin' && $model->isDirty('company_id')) {
                $model->company_id = $model->getOriginal('company_id');
            }
        });
    }
}
