<?php

namespace App\Modules\Core\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Client\Models\User;
use App\Modules\Client\Models\Product;
use App\Modules\Client\Models\GameKey;
use App\Modules\Client\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_revenue' => Transaction::where('type', 'purchase')->sum('amount') * -1,
            'total_keys_sold' => GameKey::where('status', 'sold')->count(),
            'total_balance' => User::sum('balance'),
        ];

        $recent_transactions = Transaction::with('user')->orderBy('created_at', 'desc')->take(10)->get();

        return view('admin::dashboard', compact('stats', 'recent_transactions'));
    }
}
