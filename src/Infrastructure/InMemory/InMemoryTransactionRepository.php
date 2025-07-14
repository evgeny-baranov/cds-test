<?php

declare(strict_types=1);

namespace App\Infrastructure\InMemory;

use App\Domain\Entity\Transaction;
use App\Repository\Contracts\TransactionRepositoryInterface;

final class InMemoryTransactionRepository implements TransactionRepositoryInterface
{
    /** @var array<string,Transaction[]> */
    private array $store = [];

    public function add(string $accountId, Transaction $transaction): void
    {
        $this->store[$accountId][] = $transaction;
    }

    public function forAccount(string $accountId): array
    {
        return $this->store[$accountId] ?? [];
    }
}