<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa cadastro de usuário
     */
    public function test_user_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'Gabriel',
            'email' => 'gabriel@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', ['email' => 'gabriel@test.com']);
    }

    /**
     * Testa login
     */
    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Testa depósito
     */
    public function test_user_can_deposit()
    {
        $user = User::factory()->create(['balance' => 0]);

        $this->actingAs($user)
            ->post('/deposit', ['amount' => 100])
            ->assertRedirect('/');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => 100,
        ]);

        $this->assertEquals(100, $user->fresh()->balance);
    }

    /**
     * Testa transferência
     */
    public function test_user_can_transfer()
    {
        $sender = User::factory()->create(['balance' => 200]);
        $receiver = User::factory()->create(['balance' => 0]);

        $this->actingAs($sender)
            ->post('/transfer', [
                'email' => $receiver->email,
                'amount' => 50,
            ])->assertRedirect('/');

        $this->assertEquals(150, $sender->fresh()->balance);
        $this->assertEquals(50, $receiver->fresh()->balance);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $sender->id,
            'type' => 'transfer',
            'amount' => 50,
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $receiver->id,
            'type' => 'receive',
            'amount' => 50,
        ]);
    }

    /**
     * Testa reversão de transferência
     */
    public function test_user_can_reverse_transfer()
    {
        $sender = User::factory()->create(['balance' => 200]);
        $receiver = User::factory()->create(['balance' => 0]);

        $this->actingAs($sender)
            ->post('/transfer', [
                'email' => $receiver->email,
                'amount' => 100,
            ]);

        $transaction = Transaction::where('user_id', $sender->id)
            ->where('type', 'transfer')->first();

        $this->actingAs($sender)
            ->patch("/transactions/{$transaction->id}/reverse")
            ->assertRedirect('/');

        $this->assertEquals(200, $sender->fresh()->balance);
        $this->assertEquals(0, $receiver->fresh()->balance);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $sender->id,
            'type' => 'reversal',
            'amount' => 100,
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $receiver->id,
            'type' => 'reversal',
            'amount' => 100,
        ]);
    }
}
