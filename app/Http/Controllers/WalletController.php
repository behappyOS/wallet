<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepositRequest;
use App\Http\Requests\TransferRequest;
use App\Models\User;
use App\Models\Transaction;
use App\Services\WalletService;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    private WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function index()
    {
        $user = Auth::user();
        $transactions = $user->transactions()->latest()->paginate(10);

        return view('wallet.dashboard', compact('user', 'transactions'));
    }

    public function showDeposit()
    {
        return view('wallet.deposit');
    }

    public function deposit(DepositRequest $request)
    {
        $this->walletService->deposit($request->amount);

        return redirect()->route('dashboard')->with('success', 'Depósito realizado com sucesso!');
    }

    public function showTransfer()
    {
        return view('wallet.transfer');
    }

    public function transfer(TransferRequest $request)
    {
        $receiver = User::where('email', $request->email)->first();

        try {
            $this->walletService->transfer($receiver, $request->amount);
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => $e->getMessage()]);
        }

        return redirect()->route('dashboard')->with('success', 'Transferência realizada com sucesso!');
    }

    public function reverse(Transaction $transaction)
    {
        try {
            $this->walletService->reverse($transaction);
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => $e->getMessage()]);
        }

        return redirect()->route('dashboard')->with('success', 'Transação revertida com sucesso!');
    }
}
