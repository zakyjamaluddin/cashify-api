<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWalletRequest;
use App\Http\Requests\UpdateWalletRequest;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $wallets = Wallet::query()
            ->where(function ($q) use ($request) {
                $q->where('admin_id', $request->user()->id)
                  ->orWhereHas('members', function ($mq) use ($request) {
                      $mq->where('user_id', $request->user()->id);
                  });
            })
            ->withCount('members')
            ->get();

        return response()->json($wallets);
    }

    public function store(StoreWalletRequest $request)
    {
        $wallet = Wallet::create([
            'id' => (string) Str::uuid(),
            'name' => $request->name,
            'privacy' => $request->input('privacy', 'Private'),
            'admin_id' => $request->user()->id,
            'member_count' => 1,
        ]);

        return response()->json($wallet, 201);
    }

    public function show(Wallet $wallet)
    {
        return response()->json($wallet);
    }

    public function update(UpdateWalletRequest $request, Wallet $wallet)
    {
        $wallet->update($request->validated());
        return response()->json($wallet);
    }

    public function destroy(Wallet $wallet)
    {
        $wallet->delete();
        return response()->json(null, 204);
    }
}



