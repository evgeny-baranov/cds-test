<?php
require 'vendor/autoload.php';

use App\Application\Service\{SortByComment, SortByDate, TransactionService};
use App\Domain\Entity\{Account, Deposit, Withdrawal, Transfer};
use App\Domain\Enum\Currency;
use App\Domain\ValueObject\Money;
use App\Infrastructure\InMemory\{InMemoryAccountRepository, InMemoryTransactionRepository};

$accounts = new InMemoryAccountRepository();
$txns = new InMemoryTransactionRepository();
$service = new TransactionService($accounts, $txns);

// bootstrap two accounts
$acc1 = new Account('A1', new Money(0, Currency::EUR));
$acc2 = new Account('A2', new Money(0, Currency::EUR));

$accounts->save($acc1);
$accounts->save($acc2);

$now = new DateTimeImmutable();

// perform operations
$service->executeTransaction(
    transaction: new Deposit(
        account: 'A1',
        money: new Money(10_00, Currency::EUR),
        date: $now,
        comment: "A1 deposit 1"
    )
);
$service->executeTransaction(
    transaction: new Deposit(
        account: 'A1',
        money: new Money(13_00, Currency::EUR),
        date: $now,
        comment: "A1 deposit 2"
    )
);
$service->executeTransaction(
    transaction: new Withdrawal(
        account: 'A1',
        money: new Money(2_00, Currency::EUR),
        date: $now->modify('+1 day'),
        comment: "A1 withdrawal 1"
    )
);
$service->executeTransaction(
    transaction: new Transfer(
        fromAccountId: 'A1',
        toAccountId: 'A2',
        comment: 'Payment 1',
        money: new Money(5_00, Currency::EUR),
        date: $now->modify('+2 days')
    )
);
$service->executeTransaction(
    transaction: new Transfer('A2', 'A1', 'Payment 2', new Money(1_00, Currency::EUR), $now->modify('+3 days'))
);

// query
foreach ($service->getAllAccounts() as $acc) {
    echo $acc->id() . ' balance: ' . $service->getAccountBalance($acc->id()) . PHP_EOL;

    echo "Sorted by comment:" . PHP_EOL;
    foreach ($service->accountTransactionsSorted($acc->id(), new SortByComment()) as $t) {
        echo "  - {$t->account()} {$t->comment()} {$t->date()->format('Y-m-d')} ({$t->money()})\n";
    }

    echo "Sorted by date:" . PHP_EOL;
    foreach ($service->accountTransactionsSorted($acc->id(), new SortByDate()) as $t) {
        echo "  - {$t->account()} {$t->comment()} {$t->date()->format('Y-m-d')} ({$t->money()})\n";
    }
}