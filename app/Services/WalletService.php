<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function deposit(float $amount): void
    {
        DB::transaction(function () use ($amount) {
            $user = Auth::user();

            $user->increment('balance', $amount);

            Transaction::create([
                'user_id' => $user->id,
                'type'    => 'deposit',
                'amount'  => $amount,
                'status'  => 'completed',
            ]);
        });
    }

    public function transfer(User $receiver, float $amount): void
    {
        $sender = Auth::user();

        if ($sender->balance < $amount) {
            throw new \Exception('Saldo insuficiente.');
        }

        DB::transaction(function () use ($sender, $receiver, $amount) {
            $sender->decrement('balance', $amount);

            $transferTx = Transaction::create([
                'user_id' => $sender->id,
                'type'    => 'transfer',
                'amount'  => $amount,
                'status'  => 'completed',
                'meta'    => ['to' => $receiver->id],
            ]);

            $receiver->increment('balance', $amount);

            Transaction::create([
                'user_id' => $receiver->id,
                'type'    => 'receive',
                'amount'  => $amount,
                'status'  => 'completed',
                'meta'    => [
                    'original'        => $transferTx->id,
                    'original_sender' => $sender->id,
                ],
            ]);
        });
    }

    public function reverse(Transaction $transaction): void
    {
        $user = Auth::user();

        if ($transaction->status === 'reverted'
            || (isset($transaction->meta['original'])
                && Transaction::find($transaction->meta['original'])->status === 'reverted')) {

            if ($transaction->status !== 'reverted') {
                $transaction->update(['status' => 'reverted']);
            }

            if (isset($transaction->meta['original'])) {
                $originalTx = Transaction::find($transaction->meta['original']);
                if ($originalTx && $originalTx->status !== 'reverted') {
                    $originalTx->update(['status' => 'reverted']);
                }
            }

            throw new \Exception('Esta transação já foi revertida.');
        }

        $isParticipant = $transaction->user_id === $user->id
            || (isset($transaction->meta['original_sender']) && $transaction->meta['original_sender'] === $user->id)
            || (isset($transaction->meta['to']) && $transaction->meta['to'] === $user->id);

        if (!$isParticipant) {
            throw new \Exception('Acesso negado.');
        }

        DB::transaction(function () use ($transaction, $user) {
            $amount   = $transaction->amount;
            $sender   = $transaction->type === 'transfer'
                ? $transaction->user
                : (isset($transaction->meta['original_sender']) ? User::find($transaction->meta['original_sender']) : null);
            $receiver = $transaction->type === 'receive'
                ? $transaction->user
                : (isset($transaction->meta['to']) ? User::find($transaction->meta['to']) : null);

            if ($transaction->type === 'deposit') {
                $transaction->user->decrement('balance', $amount);
            }

            if ($sender) $sender->increment('balance', $amount);
            if ($receiver) $receiver->decrement('balance', $amount);

            $relatedIds = [$transaction->id];
            if (isset($transaction->meta['original'])) {
                $relatedIds[] = $transaction->meta['original'];
            }
            Transaction::whereIn('id', $relatedIds)->update(['status' => 'reverted']);

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
    }
}
