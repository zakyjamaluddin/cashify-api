<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::query()->with(['wallet', 'category', 'recorder']);

        if ($request->filled('wallet_id')) {
            $query->where('wallet_id', $request->string('wallet_id'));
        }
        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }
        if ($request->filled('from')) {
            $query->where('date', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->where('date', '<=', $request->date('to'));
        }

        return response()->json($query->orderByDesc('date')->paginate(20));
    }

    public function store(StoreTransactionRequest $request)
    {
        $tx = Transaction::create(array_merge($request->validated(), [
            'id' => (string) Str::uuid(),
            'recorded_by' => $request->user()->id,
        ]));

        return response()->json($tx->load(['wallet', 'category', 'recorder']), 201);
    }

    public function show(Transaction $transaction)
    {
        return response()->json($transaction->load(['wallet', 'category', 'recorder']));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $transaction->update($request->validated());
        return response()->json($transaction->load(['wallet', 'category', 'recorder']));
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return response()->json(null, 204);
    }
}



