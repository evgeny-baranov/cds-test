<?php

declare(strict_types=1);

namespace App\Infrastructure\InMemory;

use App\Domain\Entity\Account;
use App\Repository\Contracts\AccountRepositoryInterface;

final class InMemoryAccountRepository implements AccountRepositoryInterface
{
    /** @var array<string,Account> */
    private array $store = [];

    public function all(): array
    {
        return array_values($this->store);
    }

    public function getById(string $id): ?Account
    {
        return $this->store[$id] ?? null;
    }

    public function save(Account $account): void
    {
        $this->store[$account->id()] = $account;
    }
}