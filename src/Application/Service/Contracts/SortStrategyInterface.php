<?php

declare(strict_types=1);

namespace App\Application\Service\Contracts;

use App\Domain\Entity\Transaction;

interface SortStrategyInterface
{
    /**
     * @param Transaction[] $transactions
     * @return Transaction[]
     */
    public function sort(array $transactions): array;
}