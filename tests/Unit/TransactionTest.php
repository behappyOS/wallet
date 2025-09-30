<?php

namespace Tests\Unit;

use App\Models\Transaction;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_a_completed_transaction()
    {
        $transaction = new Transaction([
            'type' => 'deposit',
            'amount' => 100.00,
            'status' => 'completed',
        ]);

        $this->assertEquals('deposit', $transaction->type);
        $this->assertEquals(100.00, $transaction->amount);
        $this->assertEquals('completed', $transaction->status);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_marks_transaction_as_failed()
    {
        $transaction = new Transaction([
            'type' => 'transfer',
            'amount' => 50.00,
            'status' => 'pending',
        ]);

        $transaction->status = 'failed';

        $this->assertEquals('failed', $transaction->status);
    }
}
