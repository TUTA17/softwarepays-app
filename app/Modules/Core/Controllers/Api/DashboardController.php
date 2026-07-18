<?php

namespace App\Modules\Core\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Theme\Models\GameKey;
use App\Modules\Theme\Models\Product;
use App\Modules\Theme\Models\Transaction;
use App\Modules\Theme\Models\User;
use Carbon\Carbon;

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

        $thirtyDaysAgo = Carbon::now()->subDays(29)->startOfDay();

        $revenueData = Transaction::where('type', 'purchase')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->selectRaw('DATE(created_at) as date, SUM(amount) * -1 as revenue')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->pluck('revenue', 'date')
            ->toArray();

        $chart = [];
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays(29 - $i)->format('Y-m-d');
            $chart[] = [
                'date' => $date,
                'label' => Carbon::parse($date)->format('d/m'),
                'revenue' => (float) ($revenueData[$date] ?? 0),
            ];
        }

        $topGames = Product::withCount(['keys as sold_count' => function ($query) {
            $query->where('status', 'sold');
        }])->orderBy('sold_count', 'desc')->take(5)->get(['id', 'name'])
            ->map(fn ($p) => ['name' => $p->name, 'sold_count' => $p->sold_count]);

        $topUsers = User::withSum(['transactions as total_deposit' => function ($query) {
            $query->where('type', 'deposit')->where('status', 'completed');
        }], 'amount')->orderBy('total_deposit', 'desc')->take(5)->get(['id', 'name'])
            ->map(fn ($u) => ['name' => $u->name, 'total_deposit' => (float) $u->total_deposit]);

        $recentTransactions = Transaction::with('user')
            ->where(function ($query) {
                $query->where('type', '!=', 'deposit')->orWhere('status', 'completed');
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(fn ($t) => [
                'user_name' => $t->user->name ?? null,
                'type' => $t->type,
                'amount' => (float) $t->amount,
                'currency' => $t->currency ?? 'VND',
                'status' => $t->status,
                'created_at' => $t->created_at,
            ]);

        return response()->json([
            'stats' => $stats,
            'chart' => $chart,
            'top_games' => $topGames,
            'top_users' => $topUsers,
            'recent_transactions' => $recentTransactions,
        ]);
    }
}
