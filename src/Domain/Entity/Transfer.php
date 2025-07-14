<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Money;
use DateTimeImmutable;

final readonly class Transfer extends Transaction
{
    public function __construct(
        private string    $fromAccountId,
        private string    $toAccountId,
        string            $comment,
        Money             $money,
        DateTimeImmutable $date,
    )
    {
        parent::__construct($comment, $money, $date);
    }

    public function fromAccountId(): string
    {
        return $this->fromAccountId;
    }

    public function toAccountId(): string
    {
        return $this->toAccountId;
    }
}