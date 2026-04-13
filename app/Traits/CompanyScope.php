<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait CompanyScope
{
    protected static function bootCompanyScope()
    {
        static::addGlobalScope('company', function (Builder $query) {

            if (app()->runningInConsole()) {
                return;
            }

            if (!auth()->check()) {
                return;
            }

            $user = auth()->user();

            if ($user && ($user->company_id ?? false)) {
                if (!($user->is_admin ?? false)) {
                    $query->where('company_id', $user->company_id);
                }
            }
        });

        static::creating(function ($model) {

            if (app()->runningInConsole()) {
                return;
            }

            if (!auth()->check()) {
                return;
            }

            $user = auth()->user();

            if ($user && ($user->company_id ?? false) && !($user->is_admin ?? false)) {
                $model->company_id = $user->company_id;
            }
        });
    }
}