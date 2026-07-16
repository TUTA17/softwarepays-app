<?php

namespace App\Modules\Core\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Theme\Models\User;
use App\Modules\Theme\Models\Product;
use App\Modules\Theme\Models\GameKey;
use App\Modules\Theme\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Thống kê tổng quan
        $stats = [
            'total_users' => User::count(),
            'total_revenue' => Transaction::where('type', 'purchase')->sum('amount') * -1,
            'total_keys_sold' => GameKey::where('status', 'sold')->count(),
            'total_balance' => User::sum('balance'),
        ];

        // 2. Dữ liệu biểu đồ doanh thu 30 ngày qua
        $thirtyDaysAgo = \Carbon\Carbon::now()->subDays(29)->startOfDay();
        
        // Lấy doanh thu nhóm theo ngày
        $revenueData = Transaction::where('type', 'purchase')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->selectRaw('DATE(created_at) as date, SUM(amount) * -1 as revenue')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->pluck('revenue', 'date')
            ->toArray();

        // Tạo mảng 30 ngày đầy đủ (kể cả ngày không có doanh thu)
        $chartLabels = [];
        $chartData = [];
        
        for ($i = 0; $i < 30; $i++) {
            $date = \Carbon\Carbon::now()->subDays(29 - $i)->format('Y-m-d');
            $chartLabels[] = \Carbon\Carbon::parse($date)->format('d/m');
            $chartData[] = $revenueData[$date] ?? 0;
        }

        // 3. Top 5 Game bán chạy nhất
        $topGames = Product::withCount(['keys as sold_count' => function ($query) {
            $query->where('status', 'sold');
        }])->orderBy('sold_count', 'desc')->take(5)->get();

        // 4. Top 5 Khách hàng nạp nhiều nhất (VIP) — chỉ tính giao dịch nạp đã thành công
        $topUsers = User::withSum(['transactions as total_deposit' => function ($query) {
            $query->where('type', 'deposit')->where('status', 'completed');
        }], 'amount')->orderBy('total_deposit', 'desc')->take(5)->get();

        // 5. Giao dịch gần đây — bỏ qua giao dịch nạp chưa hoàn tất (pending/failed/cancelled)
        // để không hiển thị nhầm thành "đã nạp thành công"
        $recent_transactions = Transaction::with('user')
            ->where(function ($query) {
                $query->where('type', '!=', 'deposit')->orWhere('status', 'completed');
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('core::admin.dashboard', compact('stats', 'chartLabels', 'chartData', 'topGames', 'topUsers', 'recent_transactions'));
    }
}
