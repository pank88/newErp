<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function summary()
    {
        return [
            'total_inventory_items' => InventoryItem::count(),
            'total_inventory_value' => InventoryItem::sum('cost_price'),
            'total_sales_value'     => InventoryItem::sum('sale_price'),
            'total_transactions'    => Transaction::count(),
            'total_income'          => Transaction::where('type', 'income')->sum('amount'),
            'total_expense'         => Transaction::where('type', 'expense')->sum('amount'),
        ];
    }
}
