<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_a_user_instance()
    {
        $user = new User([
            'name' => 'João',
            'email' => 'joao@example.com',
            'password' => password_hash('123456', PASSWORD_BCRYPT),
            'balance' => 0,
        ]);

        $this->assertEquals('João', $user->name);
        $this->assertEquals('joao@example.com', $user->email);
        $this->assertTrue(password_verify('123456', $user->password));
        $this->assertEquals(0, $user->balance);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_increases_balance_on_deposit()
    {
        $user = new User(['balance' => 100]);
        $user->balance += 50;

        $this->assertEquals(150, $user->balance);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_decreases_balance_on_transfer()
    {
        $user = new User(['balance' => 200]);
        $user->balance -= 75;

        $this->assertEquals(125, $user->balance);
    }
}
