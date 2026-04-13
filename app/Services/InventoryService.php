<?php

namespace App\Services;

use App\Models\InventoryLedger;
use Illuminate\Support\Facades\Auth;

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
                'created_by'  => $userId ?? Auth::id(),
            ]);
        }
    }

    /**
     * Get current stock (ledger based)
     */
    public function getCurrentStock(int $itemId): float
    {
        $in  = InventoryLedger::where('item_id', $itemId)->sum('qty_in');
        $out = InventoryLedger::where('item_id', $itemId)->sum('qty_out');

        return $in - $out;
    }

    /**
     * Consume stock for production
     */
    public function consumeForProduction(
        int $itemId,
        float $qty,
        float $unitCost,
        int $productionId,
        string $date
    ): void {

        $stock = $this->getCurrentStock($itemId);

        if ($stock < $qty) {
            throw new \Exception("Insufficient stock for item ID {$itemId}");
        }

        InventoryLedger::create([
            'item_id'     => $itemId,
            'module_type' => 'production',
            'module_id'   => $productionId,
            'qty_in'      => 0,
            'qty_out'     => $qty,
            'unit_cost'   => $unitCost,
            'total_cost'  => $qty * $unitCost,
            'date'        => $date,
            'created_by'  => Auth::id(),
        ]);
    }

    /**
     * Finished goods stock in
     */
    public function addFinishedGoods(
        int $itemId,
        float $qty,
        float $unitCost,
        int $productionId,
        string $date
    ): void {
        InventoryLedger::create([
            'item_id'     => $itemId,
            'module_type' => 'production',
            'module_id'   => $productionId,
            'qty_in'      => $qty,
            'qty_out'     => 0,
            'unit_cost'   => $unitCost,
            'total_cost'  => $qty * $unitCost,
            'date'        => $date,
            'created_by'  => Auth::id(),
        ]);
    }
}
