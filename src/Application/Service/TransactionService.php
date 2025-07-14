<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Service\Contracts\SortStrategyInterface;
use App\Domain\Entity\Account;
use App\Domain\Entity\Deposit;
use App\Domain\Entity\Transaction;
use App\Domain\Entity\Transfer;
use App\Domain\Entity\Withdrawal;
use App\Infrastructure\InMemory\InMemoryAccountRepository;
use App\Infrastructure\InMemory\InMemoryTransactionRepository;
use InvalidArgumentException;

final readonly class TransactionService
{
    public function __construct(
        private InMemoryAccountRepository     $accountRepo,
        private InMemoryTransactionRepository $txnRepo,
    )
    {
    }

    /** @return Account[] */
    public function allAccounts(): array
    {
        return $this->accountRepo->all();
    }

    public function balance(string $accountId): string
    {
        $account = $this->accountRepo->find($accountId);
        if (!$account) {
            throw new InvalidArgumentException('Account not found');
        }
        return (string)$account->balance();
    }

    public function perform(Transaction $txn): void
    {
        switch (true) {
            case $txn instanceof Deposit:
                $this->applyDeposit($txn);
                break;
            case $txn instanceof Withdrawal:
                $this->applyWithdrawal($txn);
                break;
            case $txn instanceof Transfer:
                $this->applyTransfer($txn);
                break;
            default:
                throw new InvalidArgumentException('Unsupported transaction');
        }
    }

    /** @return Transaction[] */
    public function accountTransactionsSorted(string $accountId, SortStrategyInterface $strategy): array
    {
        $txns = $this->txnRepo->forAccount($accountId);
        return $strategy->sort($txns);
    }

    // ----- private helpers -------------------------------------------------

    private function applyDeposit(Deposit $deposit): void
    {
        $account = $this->requireAccount($deposit->comment()); // comment stores accountId here for simplicity
        $account->apply($deposit);
        $this->txnRepo->add($account->id(), $deposit);
    }

    private function applyWithdrawal(Withdrawal $withdrawal): void
    {
        $account = $this->requireAccount($withdrawal->comment());
        $account->apply($withdrawal);
        $this->txnRepo->add($account->id(), $withdrawal);
    }

    private function applyTransfer(Transfer $transfer): void
    {
        $from = $this->requireAccount($transfer->fromAccountId());
        $to = $this->requireAccount($transfer->toAccountId());

        $from->apply($transfer);
        $to->apply($transfer);

        $this->txnRepo->add($from->id(), $transfer);
        $this->txnRepo->add($to->id(), $transfer);
    }

    private function requireAccount(string $id): Account
    {
        $acc = $this->accountRepo->find($id);
        if (!$acc) {
            throw new InvalidArgumentException("Account $id not found");
        }
        return $acc;
    }
}