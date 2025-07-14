<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Service\Contracts\SortStrategyInterface;
use App\Domain\Entity\Transaction;

final class SortByComment implements SortStrategyInterface
{
    public function sort(array $transactions): array
    {
        usort($transactions, fn(Transaction $a, Transaction $b) => $a->comment() <=> $b->comment());
        return $transactions;
    }
}