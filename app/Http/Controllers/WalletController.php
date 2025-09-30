<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
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

    public function deposit(Request $request)
    {
        $amount = str_replace(['.', ','], ['', '.'], $request->amount);
        $amount = (float) $amount;

        $request->merge(['amount' => $amount]);

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($request) {
            $user = Auth::user();

            $user->balance += $request->amount;
            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'type'    => 'deposit',
                'amount'  => $request->amount,
                'status'  => 'completed',
            ]);
        });

        return redirect()->route('dashboard')->with('success', 'Depósito realizado com sucesso!');
    }

    public function showTransfer()
    {
        return view('wallet.transfer');
    }

    public function transfer(Request $request)
    {
        $amount = str_replace(['.', ','], ['', '.'], $request->amount);
        $amount = (float) $amount;

        $request->merge(['amount' => $amount]);

        $request->validate([
            'email'  => 'required|email|exists:users,email',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $sender = Auth::user();
        $receiver = User::where('email', $request->email)->first();

        if ($sender->id === $receiver->id) {
            return back()->withErrors(['email' => 'Você não pode transferir para si mesmo.']);
        }

        if ($sender->balance < $request->amount) {
            return back()->withErrors(['amount' => 'Saldo insuficiente.']);
        }

        DB::transaction(function () use ($sender, $receiver, $request) {
            $sender->balance -= $request->amount;
            $sender->save();

            $receiver->balance += $request->amount;
            $receiver->save();

            Transaction::create([
                'user_id' => $sender->id,
                'type'    => 'transfer',
                'amount'  => $request->amount,
                'status'  => 'completed',
                'meta'    => ['to' => $receiver->id],
            ]);

            Transaction::create([
                'user_id' => $receiver->id,
                'type'    => 'receive',
                'amount'  => $request->amount,
                'status'  => 'completed',
                'meta'    => ['from' => $sender->id],
            ]);
        });

        return redirect()->route('dashboard')->with('success', 'Transferência realizada com sucesso!');
    }

    public function reverse(Transaction $transaction)
    {
        $user = Auth::user();

        if ($transaction->user_id !== $user->id) {
            abort(403, 'Acesso negado');
        }

        if ($transaction->status !== 'completed') {
            return back()->withErrors(['msg' => 'Esta transação já foi revertida ou está inválida.']);
        }

        DB::transaction(function () use ($transaction, $user) {

            if ($transaction->type === 'deposit') {
                $user->balance -= $transaction->amount;
                $user->save();
            } elseif ($transaction->type === 'transfer') {
                $user->balance += $transaction->amount;
                $user->save();

                if (isset($transaction->meta['to'])) {
                    $receiver = User::find($transaction->meta['to']);
                    if ($receiver) {
                        $receiver->balance -= $transaction->amount;
                        $receiver->save();

                        Transaction::create([
                            'user_id' => $receiver->id,
                            'type'    => 'reversal',
                            'amount'  => $transaction->amount,
                            'status'  => 'completed',
                            'meta'    => ['original' => $transaction->id],
                        ]);
                    }
                }
            } elseif ($transaction->type === 'receive') {
                $user->balance -= $transaction->amount;
                $user->save();
            }

            $transaction->update(['status' => 'reverted']);

            Transaction::create([
                'user_id' => $user->id,
                'type'    => 'reversal',
                'amount'  => $transaction->amount,
                'status'  => 'completed',
                'meta'    => ['original' => $transaction->id],
            ]);
        });

        return redirect()->route('dashboard')->with('success', 'Transação revertida com sucesso!');
    }
}
