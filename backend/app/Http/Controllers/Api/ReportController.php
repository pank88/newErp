<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\Transaction;

class ReportController extends Controller
{
    public function inventorySummary()
    {
        return [
            'total_items' => InventoryItem::count(),
            'total_value' => InventoryItem::sum('cost_price'),
            'items_by_location' => InventoryItem::selectRaw('location, count(*) as count')->groupBy('location')->get(),
        ];
    }

    public function accountingSummary()
    {
        return [
            'income_by_month' => Transaction::where('type', 'income')
                ->selectRaw('MONTH(date) as month, SUM(amount) as total')
                ->groupBy('month')->get(),
            'expense_by_month' => Transaction::where('type', 'expense')
                ->selectRaw('MONTH(date) as month, SUM(amount) as total')
                ->groupBy('month')->get(),
        ];
    }
}
