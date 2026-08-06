<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait CompanyScope
{
    protected static function bootCompanyScope(): void
    {
        // Global scope: automatically filter by company_id
        static::addGlobalScope('company', function (Builder $query) {
            if (self::shouldBypassScope()) {
                return;
            }

            $user = Auth::user();
            if ($user && $user->company_id) {
                $table = $query->getModel()->getTable();
                $query->where("{$table}.company_id", $user->company_id);
            }
        });

        // Auto-set company_id on create
        static::creating(function ($model) {
            if (self::shouldBypassScope()) {
                return;
            }

            $user = Auth::user();
            if ($user && $user->company_id && empty($model->company_id)) {
                $model->company_id = $user->company_id;
            }
        });

        // Prevent changing company_id on update
        static::updating(function ($model) {
            if (self::shouldBypassScope()) {
                return;
            }

            $user = Auth::user();
            if ($user && $user->company_id && $model->isDirty('company_id')) {
                // Only super_admin can change company_id
                if ($user->role !== 'super_admin') {
                    $model->company_id = $model->getOriginal('company_id');
                }
            }
        });

        // Prevent deleting other company's data
        static::deleting(function ($model) {
            if (self::shouldBypassScope()) {
                return;
            }

            $user = Auth::user();
            if ($user && $user->company_id && $model->company_id !== $user->company_id) {
                return false;
            }
        });
    }

    /**
     * Check if scope should be bypassed
     */
    protected static function shouldBypassScope(): bool
    {
        // Bypass in console/queue
        if (app()->runningInConsole()) {
            return true;
        }

        // Bypass if not authenticated
        if (!Auth::check()) {
            return true;
        }

        // Super admin sees all data
        if (Auth::user()->role === 'super_admin') {
            return true;
        }

        return false;
    }

    /**
     * Scope a query to only include records from a specific company
     */
    public function scopeByCompany($query, $companyId = null)
    {
        return $query->where('company_id', $companyId ?? Auth::user()->company_id);
    }
}