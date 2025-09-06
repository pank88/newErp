<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;

class AnalyticsController extends Controller
{
    public function trends()
    {
        $data = Transaction::selectRaw('YEAR(date) as year, MONTH(date) as month, type, SUM(amount) as total')
            ->where('date', '>', now()->subYear())
            ->groupBy('year', 'month', 'type')
            ->get();
        return $data;
    }
}
