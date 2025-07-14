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
        private InMemoryTransactionRepository $transactionRepository,
    )
    {
    }

    /** @return Account[] */
    public function getAllAccounts(): array
    {
        return $this->accountRepo->all();
    }

    public function getAccountBalance(string $accountId): string
    {
        $account = $this->accountRepo->getById($accountId);
        if (!$account) {
            throw new InvalidArgumentException('Account not found');
        }
        return (string)$account->balance();
    }

    public function executeTransaction(Transaction $transaction): void
    {
        switch (true) {
            case $transaction instanceof Deposit:
                $this->applyDeposit($transaction);
                break;
            case $transaction instanceof Withdrawal:
                $this->applyWithdrawal($transaction);
                break;
            case $transaction instanceof Transfer:
                $this->applyTransfer($transaction);
                break;
            default:
                throw new InvalidArgumentException('Unsupported transaction');
        }
    }

    /** @return Transaction[] */
    public function accountTransactionsSorted(string $accountId, SortStrategyInterface $strategy): array
    {
        return $strategy->sort(
            $this->transactionRepository->forAccount($accountId)
        );
    }

    private function applyDeposit(Deposit $deposit): void
    {
        $account = $this->requireAccount($deposit->account());
        $account->execute($deposit);
        $this->transactionRepository->add($account->id(), $deposit);
    }

    private function applyWithdrawal(Withdrawal $withdrawal): void
    {
        $account = $this->requireAccount($withdrawal->account());
        $account->execute($withdrawal);
        $this->transactionRepository->add($account->id(), $withdrawal);
    }

    private function applyTransfer(Transfer $transfer): void
    {
        $from = $this->requireAccount($transfer->fromAccountId());
        $to = $this->requireAccount($transfer->toAccountId());

        $from->execute($transfer);
        $to->execute($transfer);

        $this->transactionRepository->add($from->id(), $transfer);
        $this->transactionRepository->add($to->id(), $transfer);
    }

    private function requireAccount(string $id): Account
    {
        $acc = $this->accountRepo->getById($id);
        if (!$acc) {
            throw new InvalidArgumentException("Account $id not found");
        }
        return $acc;
    }
}