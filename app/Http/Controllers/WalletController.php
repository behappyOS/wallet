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

        if ($sender->balance < $amount) {
            return back()->withErrors(['msg' => 'Saldo insuficiente.']);
        }

        DB::transaction(function () use ($sender, $receiver, $amount) {

            // Debita remetente
            $sender->balance -= $amount;
            $sender->save();

            // Cria transação remetente
            $transferTx = Transaction::create([
                'user_id' => $sender->id,
                'type'    => 'transfer',
                'amount'  => $amount,
                'status'  => 'completed',
                'meta'    => ['to' => $receiver->id],
            ]);

            // Credita destinatário
            $receiver->balance += $amount;
            $receiver->save();

            // Cria transação destinatário
            Transaction::create([
                'user_id' => $receiver->id,
                'type'    => 'receive',
                'amount'  => $amount,
                'status'  => 'completed',
                'meta'    => [
                    'original'        => $transferTx->id,
                    'original_sender' => $sender->id
                ],
            ]);
        });

        return redirect()->route('dashboard')->with('success', 'Transferência realizada com sucesso!');
    }

    public function reverse(Transaction $transaction)
    {
        $user = Auth::user();

        if ($transaction->status === 'reverted') {
            return back()->withErrors(['msg' => 'Esta transação já foi revertida.']);
        }

        // Verifica se o usuário é participante da transação
        $isParticipant = $transaction->user_id === $user->id
            || (isset($transaction->meta['original_sender']) && $transaction->meta['original_sender'] === $user->id)
            || (isset($transaction->meta['to']) && $transaction->meta['to'] === $user->id);

        if (!$isParticipant) {
            abort(403, 'Acesso negado. Você não participa desta transação.');
        }

        DB::transaction(function () use ($transaction, $user) {

            $amount = $transaction->amount;

            // Remetente e destinatário
            $sender = $transaction->type === 'transfer' ? $transaction->user
                : (isset($transaction->meta['original_sender']) ? User::find($transaction->meta['original_sender']) : null);

            $receiver = $transaction->type === 'receive' ? $transaction->user
                : (isset($transaction->meta['to']) ? User::find($transaction->meta['to']) : null);

            // Ajusta saldos
            if ($transaction->type === 'deposit') {
                $transaction->user->balance -= $amount;
                $transaction->user->save();
            }

            if ($sender) {
                $sender->balance += $amount;
                $sender->save();
            }

            if ($receiver) {
                $receiver->balance -= $amount;
                $receiver->save();
            }

            // Marca transações originais como revertidas
            $relatedTransactions = Transaction::where('id', $transaction->id)
                ->orWhere(function($q) use ($transaction) {
                    $q->where('meta->original', $transaction->id);
                })->get();

            foreach ($relatedTransactions as $tx) {
                $tx->update(['status' => 'reverted']);
            }

            // Cria transação de reversão
            Transaction::create([
                'user_id' => $user->id,
                'type'    => 'reversal',
                'amount'  => $amount,
                'status'  => 'completed',
                'meta'    => [
                    'original' => $transaction->id,
                    'sender'   => $sender->id ?? null,
                    'receiver' => $receiver->id ?? null,
                ],
            ]);
        });

        return redirect()->route('dashboard')->with('success', 'Transação revertida com sucesso!');
    }
}
