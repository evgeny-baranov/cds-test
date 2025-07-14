<?php

declare(strict_types=1);

namespace App\Repository\Contracts;

use App\Domain\Entity\Transaction;

interface TransactionRepositoryInterface
{
    public function add(string $accountId, Transaction $transaction): void;

    /** @return Transaction[] */
    public function forAccount(string $accountId): array;
}
