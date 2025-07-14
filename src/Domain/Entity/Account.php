<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Money;
use InvalidArgumentException;

final class Account
{
    /** @var Transaction[] */
    private array $transactions = [];

    public function __construct(private readonly string $id, private Money $balance)
    {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function balance(): Money
    {
        return $this->balance; // value object is immutable
    }

    public function apply(Transaction $transaction): void
    {
        $money = $transaction->money();
        match (true) {
            $transaction instanceof Deposit => $this->balance = $this->balance->add($money),
            $transaction instanceof Withdrawal => $this->balance = $this->balance->subtract($money),
            $transaction instanceof Transfer => $this->balance = $transaction->fromAccountId() === $this->id
                ? $this->balance->subtract($money)
                : $this->balance->add($money),
            default => throw new InvalidArgumentException('Unsupported transaction'),
        };

        $this->transactions[] = $transaction;
    }

    /** @return Transaction[] */
    public function transactions(): array
    {
        return $this->transactions;
    }
}
