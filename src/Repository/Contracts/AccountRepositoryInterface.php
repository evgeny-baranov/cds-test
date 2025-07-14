<?php

declare(strict_types=1);

namespace App\Repository\Contracts;

use App\Domain\Entity\Account;

interface AccountRepositoryInterface
{
    /** @return Account[] */
    public function all(): array;

    public function getById(string $id): ?Account;

    public function save(Account $account): void;
}