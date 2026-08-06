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
     * Get current stock (ledger based)
     */
    public function getCurrentStock(int $itemId): float
    {
        $in  = InventoryLedger::where('item_id', $itemId)->sum('qty_in');
        $out = InventoryLedger::where('item_id', $itemId)->sum('qty_out');

        return $in - $out;
    }

    /**
     * Opening stock entry when item created
     */
    public function openingStockEntry($item, ?int $userId = null): void
    {
        if (!$item || $item->opening_stock <= 0) {
            return;
        }

        // 🔥 old opening remove (edit safe)
        $this->removeOldStock('opening', $item->id);

        InventoryLedger::create([
            'company_id' => $item->company_id,
            'item_id'     => $item->id,
            'module_type' => 'opening',
            'module_id'   => $item->id,
            'qty_in'      => (float) $item->opening_stock,
            'qty_out'     => 0,
            'unit_cost'   => (float) $item->unit_price,
            'total_cost'  => (float) ($item->opening_stock * $item->unit_price),
            'date'        => now(),
            'created_by'  => $userId ?? Auth::id(),
        ]);
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
                'company_id' => Auth::user()->company_id,
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
     * Consume stock for production
     */
    public function consumeForProduction(
        int $itemId,
        float $qty,
        float $unitCost,
        int $productionId,
        string $date
    ): void {

    if ($qty <= 0) {
            return;
        }

        $stock = $this->getCurrentStock($itemId);

        if ($stock < $qty) {
            throw new \Exception("Insufficient stock for item ID {$itemId}");
        }

        InventoryLedger::create([
            'company_id' => Auth::user()->company_id,
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
        if ($qty <= 0) {
            return;
        }
        InventoryLedger::create([
            'company_id' => Auth::user()->company_id,
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
