<?php

namespace App\Services;

use App\Models\InventoryLedger;
use Illuminate\Support\Collection;

class InventoryService
{
    /**
     * Remove previous stock entries (edit safe)
     */
    public function removeOldStock(string $moduleType, int $moduleId): void
    {
        InventoryLedger::where('module_type', $moduleType)
            ->where('module_id', $moduleId)
            ->delete();
    }

    /**
     * Stock IN from purchase
     */
    public function stockInFromPurchase($purchase, ?int $userId = null): void
    {
        if (!$purchase || !$purchase->purchaseItems || $purchase->purchaseItems->isEmpty()) {
            return;
        }

        // Edit safe
        $this->removeOldStock('purchase', $purchase->id);

        foreach ($purchase->purchaseItems as $row) {

            InventoryLedger::create([
                'item_id'     => $row->item_id,
                'module_type' => 'purchase',
                'module_id'   => $purchase->id,
                'qty_in'      => (float) $row->qty,
                'qty_out'     => 0,
                'unit_cost'   => (float) $row->unit_price,
                'total_cost'  => (float) ($row->qty * $row->unit_price),
                'date'        => $purchase->date,
                'created_by'  => $userId,
            ]);
        }
    }

    /**
     * Get current stock
     */
    public function getCurrentStock(int $itemId): float
    {
        $in  = InventoryLedger::where('item_id', $itemId)->sum('qty_in');
        $out = InventoryLedger::where('item_id', $itemId)->sum('qty_out');

        return $in - $out;
    }

    /**
     * Get average cost
     */
    public function getAverageCost(int $itemId): float
    {
        $qty   = InventoryLedger::where('item_id', $itemId)->sum('qty_in');
        $value = InventoryLedger::where('item_id', $itemId)->sum('total_cost');

        return $qty > 0 ? $value / $qty : 0;
    }
}
