<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Money;
use DateTimeImmutable;

abstract readonly class Transaction
{
    public function __construct(
        private string            $account,
        private Money             $money,
        private DateTimeImmutable $date,
        private string            $comment = ''
    )
    {
    }

    public function account(): string
    {
        return $this->account;
    }

    public function comment(): string
    {
        return $this->comment;
    }

    public function money(): Money
    {
        return $this->money;
    }

    public function date(): DateTimeImmutable
    {
        return $this->date;
    }
}