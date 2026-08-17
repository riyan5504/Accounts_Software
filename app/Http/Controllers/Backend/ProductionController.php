<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Chemical;
use App\Models\Depreciation;
use App\Models\InventoryLedger;
use App\Models\Item;
use App\Models\JournalEntry;
use App\Models\LaborCost;
use App\Models\OverheadCost;
use App\Models\PackagingMaterial;
use App\Models\Production;
use App\Models\QcCost;
use App\Models\SectionTotalCost;
use App\Models\TransportCost;
use App\Models\UtilityCost;
use App\Services\InventoryService;
use App\Services\JournalService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    protected InventoryService $inventoryService;
    protected JournalService $journalService;

    public function __construct(InventoryService $inventoryService, JournalService $journalService)
    {
        $this->middleware('auth');
        $this->inventoryService = $inventoryService;
        $this->journalService = $journalService;
    }

    public function production()
    {
        return view('production.modiul');
    }

    public function productionAdd()
    {
        $items = Item::all();

        $today = now();
        $year  = $today->format('y');   // 26
        $date  = $today->format('dmy'); // 060226

        $lastBatch = Production::whereYear('created_at', $today->year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastBatch) {
            // last 2 digit serial
            $lastSerial = (int) substr($lastBatch->batch_no, -2);
            $newSerial  = str_pad($lastSerial + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $newSerial = '01';
        }

        $nextBatch = $date . $newSerial;


        return view('production.add', compact('nextBatch', 'newSerial', 'items'));
    }

    public function productionStore(Request $request, JournalService $journalService)
    {
        $production = null;

        try {
            DB::transaction(function () use ($request, &$production) {

                $production = Production::create([
                    'name'           => $request->name,
                    'batch_no'       => $request->batch_no,
                    'batch_size'     => $request->batch_size,
                    'date'           => $request->date,
                    'item_id'        => $request->ra_item_id,
                    'raw_qty'        => $request->raw_qty,
                    'raw_unit'       => $request->raw_unit,
                    'raw_u_price'    => $request->raw_u_price,
                    'raw_t_price'    => $request->raw_t_price,
                    'yield'          => $request->yield,
                    'yield_unit'     => $request->yield_unit,
                    'yield_percent'  => $request->yield_percent,
                    'ex_qty'         => $request->ex_qty,
                    'ex_unit'        => $request->ex_unit,
                    'final_qty'      => $request->final_qty,
                    'final_unit'     => $request->final_unit,
                    'unit_cost'      => $request->unit_cost,
                    'grand_total'    => $request->grand_total,
                ]);

                $productionId = $production->id;

                // Inventory stock out
                $this->inventoryService->consumeForProduction(
                    $request->ra_item_id,
                    (float)$request->raw_qty,
                    (float)$request->raw_u_price,
                    $productionId,
                    $request->date
                );

                // === Chemicals ===
                if ($request->raw_name) {
                    foreach ($request->raw_name as $key => $row) {
                        if (empty($row)) continue;

                        Chemical::create([
                            'production_id' => $productionId,
                            'item_id' => $request->raw_item_id[$key],
                            'used_percent'  => $request->used_percent[$key],
                            'used_qty'      => $request->used_qty[$key],
                            'ch_unit'       => $request->ch_unit[$key],
                            'u_price'       => $request->u_price[$key],
                            't_price'       => $request->t_price[$key],
                        ]);

                        // Inventory stock out
                        $this->inventoryService->consumeForProduction(
                            $request->raw_item_id[$key],
                            (float)$request->used_qty[$key],
                            (float)$request->u_price[$key],
                            $productionId,
                            $request->date
                        );
                    }
                }

                // === Packaging ===
                if ($request->pack_name) {
                    foreach ($request->pack_name as $key => $row) {
                        if (empty($row)) continue;

                        PackagingMaterial::create([
                            'production_id' => $productionId,
                            'item_id' => $request->pack_item_id[$key],
                            'pack_size'     => $request->pack_size[$key],
                            'pack_qty'      => $request->pack_qty[$key],
                            'pack_price'    => $request->pack_price[$key],
                            'total_price'   => $request->total_price[$key],
                        ]);

                        // Inventory stock out
                        $this->inventoryService->consumeForProduction(
                            $request->pack_item_id[$key],
                            (float)$request->pack_qty[$key],
                            (float)$request->pack_price[$key],
                            $productionId,
                            $request->date
                        );
                    }
                }

                // === Labor ===
                if ($request->labor_name) {
                    foreach ($request->labor_name as $key => $row) {
                        if (empty($row)) continue;

                        LaborCost::create([
                            'production_id' => $productionId,
                            'labor_name'    => $request->labor_name[$key],
                            'duty_day'      => $request->duty_day[$key],
                            'd_pay'         => $request->d_pay[$key],
                            'total_pay'     => $request->total_pay[$key] ?? 0,
                        ]);
                    }
                }

                // === Depreciation ===
                if ($request->machine_name) {
                    foreach ($request->machine_name as $key => $row) {
                        if (empty($row)) continue;

                        Depreciation::create([
                            'production_id' => $productionId,
                            'machine_name'  => $request->machine_name[$key],
                            'machine_cost_amt' => $request->machine_cost_amt[$key] ?? 0,
                        ]);
                    }
                }

                // === Utility ===
                if ($request->utility_name) {
                    foreach ($request->utility_name as $key => $row) {
                        if (empty($row)) continue;

                        UtilityCost::create([
                            'production_id' => $productionId,
                            'utility_name'  => $request->utility_name[$key],
                            'cost_amt'      => $request->cost_amt[$key] ?? 0,
                        ]);
                    }
                }

                // === Overhead ===
                if ($request->overhead_type) {
                    foreach ($request->overhead_type as $key => $row) {
                        if (empty($row)) continue;

                        OverheadCost::create([
                            'production_id' => $productionId,
                            'overhead_type' => $row,
                            'fo_cost_amt'   => $request->fo_cost_amt[$key] ?? 0,
                        ]);
                    }
                }

                // === Transport ===
                if ($request->transport_type) {
                    foreach ($request->transport_type as $key => $row) {
                        if (empty($row)) continue;

                        TransportCost::create([
                            'production_id'   => $productionId,
                            'transport_type'  => $request->transport_type[$key],
                            'transport_amt'   => $request->transport_amt[$key] ?? 0,
                        ]);
                    }
                }

                // === QC ===
                if ($request->test_name) {
                    foreach ($request->test_name as $key => $row) {
                        if (empty($row)) continue;

                        QcCost::create([
                            'production_id' => $productionId,
                            'test_name'     => $request->test_name[$key],
                            'qc_amt'        => $request->qc_amt[$key] ?? 0,
                        ]);
                    }
                }

                // === Section Totals ===
                $sectionTotal = SectionTotalCost::create([
                    'production_id'            => $productionId,
                    'raw_grand_price'          => $request->raw_grand_price,
                    'pack_grand_price'         => $request->pack_grand_price,
                    'labor_grand_price'        => $request->labor_grand_price,
                    'depreciation_grand_price' => $request->depreciation_grand_price,
                    'utility_grand_price'      => $request->utility_grand_price,
                    'overhead_grand_price'     => $request->overhead_grand_price,
                    'transport_grand_price'    => $request->transport_grand_price,
                    'qc_grand_price'           => $request->qc_grand_price,
                ]);

                // Finished Goods Item
                $finishedItem = $this->getOrCreateFinishedItem(
                    $request->name,
                    (float) $request->unit_cost
                );

                $finishedItemId = $finishedItem->id;

                $this->inventoryService->addFinishedGoods(
                    $finishedItemId,
                    (float)$request->final_qty,
                    (float)$request->unit_cost,
                    $productionId,
                    $request->date
                );

                /*Production Journal*/

                $journalItems = [];

                if ((float) $request->raw_t_price > 0) {
                    $journalItems[] = [
                        'account' => JournalService::ACCOUNT_RAW_MATERIAL,
                        'debit'   => 0,
                        'credit'  => (float) $request->raw_t_price,
                    ];
                }

                /*Raw Material*/
                if ((float) $request->raw_grand_price > 0) {
                    $journalItems[] = [
                        'account' => JournalService::ACCOUNT_RAW_MATERIAL_EXPENSE,
                        'debit'   => 0,
                        'credit'  => (float) $request->raw_grand_price,
                    ];
                }

                /*Packaging*/
                if ((float) $request->pack_grand_price > 0) {
                    $journalItems[] = [
                        'account' => JournalService::ACCOUNT_PACKAGING_EXPENSE,
                        'debit'   => 0,
                        'credit'  => (float) $request->pack_grand_price,
                    ];
                }

                /*Labor*/
                if ((float) $request->labor_grand_price > 0) {
                    $journalItems[] = [
                        'account' => JournalService::ACCOUNT_LABOR_EXPENSE,
                        'debit'   => 0,
                        'credit'  => (float) $request->labor_grand_price,
                    ];
                }

                /*Depreciation*/
                if ((float) $request->depreciation_grand_price > 0) {
                    $journalItems[] = [
                        'account' => JournalService::ACCOUNT_DEPRECIATION_EXPENSE,
                        'debit'   => 0,
                        'credit'  => (float) $request->depreciation_grand_price,
                    ];
                }

                /*Utility*/
                if ((float) $request->utility_grand_price > 0) {
                    $journalItems[] = [
                        'account' => JournalService::ACCOUNT_UTILITY_EXPENSE,
                        'debit'   => 0,
                        'credit'  => (float) $request->utility_grand_price,
                    ];
                }

                /*Factory Overhead*/
                if ((float) $request->overhead_grand_price > 0) {
                    $journalItems[] = [
                        'account' => JournalService::ACCOUNT_FACTORY_OVERHEAD,
                        'debit'   => 0,
                        'credit'  => (float) $request->overhead_grand_price,
                    ];
                }

                /*Transport*/
                if ((float) $request->transport_grand_price > 0) {
                    $journalItems[] = [
                        'account' => JournalService::ACCOUNT_TRANSPORT_EXPENSE,
                        'debit'   => 0,
                        'credit'  => (float) $request->transport_grand_price,
                    ];
                }

                /*QC*/
                if ((float) $request->qc_grand_price > 0) {
                    $journalItems[] = [
                        'account' => JournalService::ACCOUNT_QC_EXPENSE,
                        'debit'   => 0,
                        'credit'  => (float) $request->qc_grand_price,
                    ];
                }

                /*Finished Goods Debit*/
                $journalItems[] = [
                    'account' => JournalService::ACCOUNT_FINISHED_GOODS,
                    'debit'   => (float) $request->grand_total,
                    'credit'  => 0,
                ];

                /*Create Production Journal*/

                $this->journalService->createJournal([
                    'company_id'   => auth()->user()->company_id,
                    'module_type'  => 'production',
                    'module_id'    => $productionId,
                    'reference_no' => $request->batch_no,
                    'date'         => $request->date,
                    'particulars'  => 'Production - ' . $request->name . ' | Batch: ' . $request->batch_no,
                    'items'        => $journalItems,

                ]);
            }); // end transaction        

            return redirect()->back()->with('success', 'Production Saved Successfully');
        } catch (\Throwable $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function productionList(Request $request)
    {
        $query = Production::query();

        // 🔹 Date filter
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        // 🔹 Product filter
        if ($request->filled('product')) {
            $query->where('name', 'like', '%' . $request->product . '%')
                ->orWhere('batch_no', 'like', '%' . $request->product . '%');
        }

        $productions = $query
            ->select(
                'id',
                'date',
                'batch_no',
                'name',
                'batch_size',
                'final_qty',
                'final_unit',
                'grand_total',
                'unit_cost'
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $summary = [
            'total_batch' => $query->count(),
            'total_qty'   => $query->sum('final_qty'),
            'total_cost'  => $query->sum('grand_total'),
        ];

        return view('production.list', compact('productions', 'summary'));
    }


    public function productionDetails($id)
    {
        $production = Production::with(
            'item',
            'chemicals',
            'depreciation',
            'laborCost',
            'overHeadCost',
            'qcCost',
            'packagingMaterial',
            'sectionTotalCost',
            'transportCost',
            'utilityCost'
        )->find($id);
        $sections = [
            'Raw Material'  => (float) $production->raw_t_price,
            'Chemical'     => (float) ($production->sectionTotalCost->raw_grand_price ?? 0),
            'Packaging'    => (float) ($production->sectionTotalCost->pack_grand_price ?? 0),
            'Labor'        => (float) ($production->sectionTotalCost->labor_grand_price ?? 0),
            'Utility'      => (float) ($production->sectionTotalCost->utility_grand_price ?? 0),
            'Depreciation' => (float) ($production->sectionTotalCost->depreciation_grand_price ?? 0),
            'Overhead'     => (float) ($production->sectionTotalCost->overhead_grand_price ?? 0),
            'Transport'    => (float) ($production->sectionTotalCost->transport_grand_price ?? 0),
            'QC'           => (float) ($production->sectionTotalCost->qc_grand_price ?? 0),
        ];

        $highestCostHead = collect($sections)
            ->sortDesc()
            ->keys()
            ->first();


        $batchSizeNumeric = (float) preg_replace('/[^0-9.]/', '', $production->batch_size);

        $costPerUnit = 0;
        if ($batchSizeNumeric > 0) {
            $costPerUnit = $production->grand_total / $batchSizeNumeric;
        }
        return view('production.details', compact('production', 'costPerUnit', 'highestCostHead'));
    }

    public function productionEdit($id)
    {
        $production = Production::with(
            'item',
            'chemicals',
            'depreciation',
            'laborCost',
            'overHeadCost',
            'qcCost',
            'packagingMaterial',
            'sectionTotalCost',
            'transportCost',
            'utilityCost'
        )->find($id);
        return view('production.edit', compact('production'));
    }

    public function productionUpdate(Request $request, $id)
    {
        DB::transaction(function () use ($request, $id) {

            $production = Production::findOrFail($id);

            // 1️⃣ Remove old inventory & journal
            InventoryLedger::where('module_type', 'production')
                ->where('module_id', $production->id)
                ->delete();

            // 3️⃣ Delete old related data
            $production->chemicals()->delete();
            $production->packagingMaterial()->delete();
            $production->laborCost()->delete();
            $production->depreciation()->delete();
            $production->utilityCost()->delete();
            $production->overHeadCost()->delete();
            $production->transportCost()->delete();
            $production->qcCost()->delete();
            $production->sectionTotalCost()->delete();

            // 4️⃣ Update main production row
            $rawItemId = Item::where('item_name', $request->ra_name)->value('id');

            $production->update([
                'name'           => $request->name,
                'batch_no'       => $request->batch_no,
                'batch_size'     => $request->batch_size,
                'date'           => $request->date,
                'item_id'        => $rawItemId,
                'raw_qty'        => $request->raw_qty,
                'raw_unit'       => $request->raw_unit,
                'raw_u_price'    => $request->raw_u_price,
                'raw_t_price'    => $request->raw_t_price,
                'yield'          => $request->yield,
                'yield_unit'     => $request->yield_unit,
                'yield_percent'  => $request->yield_percent,
                'ex_qty'         => $request->ex_qty,
                'ex_unit'        => $request->ex_unit,
                'final_qty'      => $request->final_qty,
                'final_unit'     => $request->final_unit,
                'unit_cost'      => $request->unit_cost,
                'grand_total'    => $request->grand_total,
            ]);

            $productionId = $production->id;

            // 5️⃣ Raw material stock out
            $this->inventoryService->consumeForProduction(
                $rawItemId,
                (float)$request->raw_qty,
                (float)$request->raw_u_price,
                $productionId,
                $request->date
            );

            // 6️⃣ Chemicals
            if ($request->raw_name) {
                foreach ($request->raw_name as $key => $row) {
                    if (!$row) continue;

                    $itemId = Item::where('item_name', $row)->value('id');

                    Chemical::create([
                        'production_id' => $productionId,
                        'item_id'       => $itemId,
                        'used_percent'  => $request->used_percent[$key],
                        'used_qty'      => $request->used_qty[$key],
                        'ch_unit'       => $request->ch_unit[$key],
                        'u_price'       => $request->u_price[$key],
                        't_price'       => $request->t_price[$key],
                    ]);

                    $this->inventoryService->consumeForProduction(
                        $itemId,
                        (float)$request->used_qty[$key],
                        (float)$request->u_price[$key],
                        $productionId,
                        $request->date
                    );
                }
            }

            // 7️⃣ Packaging
            if ($request->pack_name) {
                foreach ($request->pack_name as $key => $row) {
                    if (!$row) continue;

                    $itemId = Item::where('item_name', $row)->value('id');

                    PackagingMaterial::create([
                        'production_id' => $productionId,
                        'item_id'       => $itemId,
                        'pack_size'     => $request->pack_size[$key],
                        'pack_qty'      => $request->pack_qty[$key],
                        'pack_price'    => $request->pack_price[$key],
                        'total_price'   => $request->total_price[$key],
                    ]);

                    $this->inventoryService->consumeForProduction(
                        $itemId,
                        (float)$request->pack_qty[$key],
                        (float)$request->pack_price[$key],
                        $productionId,
                        $request->date
                    );
                }
            }

            // 8️⃣ Labor
            if ($request->labor_name) {
                foreach ($request->labor_name as $key => $row) {
                    if (!$row) continue;

                    LaborCost::create([
                        'production_id' => $productionId,
                        'labor_name'    => $row,
                        'duty_day'      => $request->duty_day[$key],
                        'd_pay'         => $request->d_pay[$key],
                        'total_pay'     => $request->total_pay[$key] ?? 0,
                    ]);
                }
            }

            // 9️⃣ Depreciation
            if ($request->machine_name) {
                foreach ($request->machine_name as $key => $row) {
                    if (!$row) continue;

                    Depreciation::create([
                        'production_id' => $productionId,
                        'machine_name'  => $row,
                        'machine_cost_amt' => $request->machine_cost_amt[$key] ?? 0,
                    ]);
                }
            }

            // 🔟 Utility
            if ($request->utility_name) {
                foreach ($request->utility_name as $key => $row) {
                    if (!$row) continue;

                    UtilityCost::create([
                        'production_id' => $productionId,
                        'utility_name'  => $row,
                        'cost_amt'      => $request->cost_amt[$key] ?? 0,
                    ]);
                }
            }

            // 1️⃣1️⃣ Overhead
            if ($request->overhead_type) {
                foreach ($request->overhead_type as $key => $row) {
                    if (!$row) continue;

                    OverheadCost::create([
                        'production_id' => $productionId,
                        'overhead_type' => $row,
                        'fo_cost_amt'   => $request->fo_cost_amt[$key] ?? 0,
                    ]);
                }
            }

            // 1️⃣2️⃣ Transport
            if ($request->transport_type) {
                foreach ($request->transport_type as $key => $row) {
                    if (!$row) continue;

                    TransportCost::create([
                        'production_id'  => $productionId,
                        'transport_type' => $row,
                        'transport_amt'  => $request->transport_amt[$key] ?? 0,
                    ]);
                }
            }

            // 1️⃣3️⃣ QC
            if ($request->test_name) {
                foreach ($request->test_name as $key => $row) {
                    if (!$row) continue;

                    QcCost::create([
                        'production_id' => $productionId,
                        'test_name'     => $row,
                        'qc_amt'        => $request->qc_amt[$key] ?? 0,
                    ]);
                }
            }

            // 1️⃣4️⃣ Section totals
            $sectionTotal = SectionTotalCost::create([
                'production_id'            => $productionId,
                'raw_grand_price'          => $request->raw_grand_price,
                'pack_grand_price'         => $request->pack_grand_price,
                'labor_grand_price'        => $request->labor_grand_price,
                'depreciation_grand_price' => $request->depreciation_grand_price,
                'utility_grand_price'      => $request->utility_grand_price,
                'overhead_grand_price'     => $request->overhead_grand_price,
                'transport_grand_price'    => $request->transport_grand_price,
                'qc_grand_price'           => $request->qc_grand_price,
            ]);

            // 1️⃣5️⃣ Finished goods stock in
            $finishedItem = $this->getOrCreateFinishedItem(
                $request->name,
                (float) $request->unit_cost
            );

            $finishedItemId = $finishedItem->id;

            $this->inventoryService->addFinishedGoods(
                $finishedItemId,
                (float)$request->final_qty,
                (float)$request->unit_cost,
                $productionId,
                $request->date
            );

            $journalItems = [];

            if ((float) $request->raw_t_price > 0) {
                $journalItems[] = [
                    'account' => JournalService::ACCOUNT_RAW_MATERIAL,
                    'debit'   => 0,
                    'credit'  => (float) $request->raw_t_price,
                ];
            }

            if ((float) $request->raw_grand_price > 0) {
                $journalItems[] = [
                    'account' => JournalService::ACCOUNT_RAW_MATERIAL_EXPENSE,
                    'debit'   => 0,
                    'credit'  => (float) $request->raw_grand_price,
                ];
            }

            if ((float) $request->pack_grand_price > 0) {
                $journalItems[] = [
                    'account' => JournalService::ACCOUNT_PACKAGING_EXPENSE,
                    'debit'   => 0,
                    'credit'  => (float) $request->pack_grand_price,
                ];
            }

            if ((float) $request->labor_grand_price > 0) {
                $journalItems[] = [
                    'account' => JournalService::ACCOUNT_LABOR_EXPENSE,
                    'debit'   => 0,
                    'credit'  => (float) $request->labor_grand_price,
                ];
            }

            if ((float) $request->depreciation_grand_price > 0) {
                $journalItems[] = [
                    'account' => JournalService::ACCOUNT_DEPRECIATION_EXPENSE,
                    'debit'   => 0,
                    'credit'  => (float) $request->depreciation_grand_price,
                ];
            }

            if ((float) $request->utility_grand_price > 0) {
                $journalItems[] = [
                    'account' => JournalService::ACCOUNT_UTILITY_EXPENSE,
                    'debit'   => 0,
                    'credit'  => (float) $request->utility_grand_price,
                ];
            }

            if ((float) $request->overhead_grand_price > 0) {
                $journalItems[] = [
                    'account' => JournalService::ACCOUNT_FACTORY_OVERHEAD,
                    'debit'   => 0,
                    'credit'   => (float) $request->overhead_grand_price,
                ];
            }

            if ((float) $request->transport_grand_price > 0) {
                $journalItems[] = [
                    'account' => JournalService::ACCOUNT_TRANSPORT_EXPENSE,
                    'debit'   => 0,
                    'credit'   => (float) $request->transport_grand_price,
                ];
            }

            if ((float) $request->qc_grand_price > 0) {
                $journalItems[] = [
                    'account' => JournalService::ACCOUNT_QC_EXPENSE,
                    'debit'   => 0,
                    'credit'   => (float) $request->qc_grand_price,
                ];
            }

            $journalItems[] = [
                'account' => JournalService::ACCOUNT_FINISHED_GOODS,
                'debit'   => (float) $request->grand_total,
                'credit'  => 0,
            ];

            $this->journalService->createJournal([
                'company_id'   => auth()->user()->company_id,
                'module_type'  => 'production',
                'module_id'    => $productionId,
                'reference_no' => $request->batch_no,
                'date'         => $request->date,
                'particulars'  => 'Production - ' . $request->name .
                    ' | Batch: ' . $request->batch_no,
                'items'        => $journalItems,
            ]);
        });

        return redirect('/production/list')->with('success', 'Production updated successfully');
    }

    public function productionDelete($id)
    {
        DB::transaction(function () use ($id) {

            $production = Production::findOrFail($id);

            // 1. Inventory Ledger Delete
            InventoryLedger::where('module_type', 'production')
                ->where('module_id', $production->id)
                ->delete();

            // 2. Journal Delete (Journal Items cascade হলে আলাদা delete লাগবে না)
            JournalEntry::where('module_type', 'production')
                ->where('module_id', $production->id)
                ->delete();

            // 3. Child Tables Delete
            Chemical::where('production_id', $production->id)->delete();

            PackagingMaterial::where('production_id', $production->id)->delete();

            LaborCost::where('production_id', $production->id)->delete();

            Depreciation::where('production_id', $production->id)->delete();

            UtilityCost::where('production_id', $production->id)->delete();

            OverheadCost::where('production_id', $production->id)->delete();

            TransportCost::where('production_id', $production->id)->delete();

            QcCost::where('production_id', $production->id)->delete();

            SectionTotalCost::where('production_id', $production->id)->delete();

            // 4. Production Delete
            $production->delete();
        });

        return back()
            ->with('success', 'Production deleted successfully.');
    }
    public function downloadListPdf(Request $request)
    {
        $query = Production::query();

        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        if ($request->filled('product')) {

            $query->where(function ($q) use ($request) {

                $q->where('name', 'like', '%' . $request->product . '%')
                    ->orWhere('batch_no', 'like', '%' . $request->product . '%');
            });
        }

        $fromDate = $request->from_date;
        $toDate   = $request->to_date;

        $companyName = auth()->user()->company->name ?? 'Company Name';

        $productions = $query
            ->latest()
            ->get();

        $summary = [
            'total_batch' => $productions->count(),
            'total_qty'   => $productions->sum('final_qty'),
            'total_cost'  => $productions->sum('grand_total'),
        ];

        $pdf = Pdf::loadView('production.list_pdf', compact(
            'productions',
            'summary',
            'fromDate',
            'toDate',
            'companyName'
        ));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('Production-List.pdf');
    }

    public function downloadPdf($id)
    {
        $production = Production::with(
            'item',
            'chemicals',
            'depreciation',
            'laborCost',
            'overHeadCost',
            'qcCost',
            'packagingMaterial',
            'sectionTotalCost',
            'transportCost',
            'utilityCost'
        )->findOrFail($id);

        $sections = [
            'Raw Material'  => (float) $production->raw_t_price,
            'Chemical'      => (float) ($production->sectionTotalCost->raw_grand_price ?? 0),
            'Packaging'     => (float) ($production->sectionTotalCost->pack_grand_price ?? 0),
            'Labor'         => (float) ($production->sectionTotalCost->labor_grand_price ?? 0),
            'Utility'       => (float) ($production->sectionTotalCost->utility_grand_price ?? 0),
            'Depreciation'  => (float) ($production->sectionTotalCost->depreciation_grand_price ?? 0),
            'Overhead'      => (float) ($production->sectionTotalCost->overhead_grand_price ?? 0),
            'Transport'     => (float) ($production->sectionTotalCost->transport_grand_price ?? 0),
            'QC'            => (float) ($production->sectionTotalCost->qc_grand_price ?? 0),
        ];

        $highestCostHead = collect($sections)
            ->sortDesc()
            ->keys()
            ->first();

        $batchSizeNumeric = (float) preg_replace('/[^0-9.]/', '', $production->batch_size);

        $costPerUnit = 0;

        if ($batchSizeNumeric > 0) {
            $costPerUnit = $production->grand_total / $batchSizeNumeric;
        }

        $pdf = Pdf::loadView('production.details-pdf', [
            'production'      => $production,
            'costPerUnit'     => $costPerUnit,
            'highestCostHead' => $highestCostHead,
            'companyName'     => auth()->user()->company->name ?? 'Your Company Name',
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Production-' . $production->batch_no . '.pdf');
    }

    private function getOrCreateFinishedItem(string $itemName, float $unitCost): Item
    {
        $finishedCategory = Category::where('cat_name', 'Finished Good')->firstOrFail();

        $item = Item::firstOrCreate(
            [
                'item_name' => $itemName,
                'cat_id'    => $finishedCategory->id,
            ],
            [
                'production_cost' => $unitCost,
            ]
        );

        $item->update([
            'production_cost' => $unitCost,
        ]);

        return $item;
    }
}
