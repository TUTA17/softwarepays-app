<?php

namespace App\Modules\Shop\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Theme\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('month')) {
            $month = date('m', strtotime($request->query('month')));
            $year = date('Y', strtotime($request->query('month')));
            $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->query('date'));
        }

        $transactions = $query->paginate(30)->withQueryString();

        return response()->json([
            'transactions' => $transactions->through(fn ($t) => [
                'id' => $t->id,
                'user_name' => $t->user->name ?? null,
                'user_email' => $t->user->email ?? null,
                'type' => $t->type,
                'amount' => (float) $t->amount,
                'currency' => $t->currency ?? 'VND',
                'status' => $t->status,
                'description' => $t->description,
                'created_at' => $t->created_at,
            ]),
            'current_page' => $transactions->currentPage(),
            'last_page' => $transactions->lastPage(),
        ]);
    }
}
