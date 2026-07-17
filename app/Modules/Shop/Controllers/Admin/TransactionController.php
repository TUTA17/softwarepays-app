<?php
namespace App\Modules\Shop\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Theme\Models\Transaction;

class TransactionController extends Controller
{
public function transactions(Request $request)
    {
        $query = Transaction::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('month')) {
            $month = date('m', strtotime($request->month));
            $year = date('Y', strtotime($request->month));
            $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $transactions = $query->paginate(50)->withQueryString();
        return view('shop::admin.transactions', compact('transactions'));
    }

public function exportTransactions(Request $request)
    {
        $query = Transaction::with('user')
            ->where('type', 'deposit')
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc');

        if ($request->filled('month')) {
            $month = date('m', strtotime($request->month));
            $year = date('Y', strtotime($request->month));
            $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
            $filename = "Lich_su_nap_tien_{$month}_{$year}.csv";
        } else {
            $filename = "Lich_su_nap_tien_Tat_ca.csv";
        }

        $transactions = $query->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($transactions) {
            $file = fopen('php://output', 'w');
            // Thêm BOM để Excel đọc đúng UTF-8
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID', 'Khách hàng', 'Số tiền', 'Đơn vị', 'Mô tả', 'Mã tham chiếu', 'Thời gian']);

            foreach ($transactions as $tx) {
                fputcsv($file, [
                    $tx->id,
                    $tx->user ? $tx->user->name : 'N/A',
                    $tx->amount,
                    $tx->currency ?? 'VND',
                    $tx->description,
                    $tx->reference_id,
                    $tx->created_at->format('d/m/Y H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
