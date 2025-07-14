<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Enum\Currency;
use InvalidArgumentException;

final readonly class Money
{
    public function __construct(private int $amount, private Currency $currency)
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Amount cannot be negative');
        }
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): Currency
    {
        return $this->currency;
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(self $other): self
    {
        $this->assertSameCurrency($other);
        if ($other->amount > $this->amount) {
            throw new InvalidArgumentException('Insufficient amount');
        }
        return new self($this->amount - $other->amount, $this->currency);
    }

    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException('Currencies must match');
        }
    }

    public function __toString(): string
    {
        return sprintf('%s %.2f', $this->currency->value, $this->amount / 100);
    }
}