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
$service->perform(new Deposit('A1', new Money(10_00, Currency::EUR), $now)); // €10 deposit
$service->perform(new Withdrawal('A1', new Money(2_00, Currency::EUR), $now->modify('+1 day'))); // €2 withdrawal
$service->perform(new Transfer('A1', 'A2', 'Payment', new Money(5_00, Currency::EUR), $now->modify('+2 days')));

// query
foreach ($service->allAccounts() as $acc) {
    echo $acc->id() . ' balance: ' . $service->balance($acc->id()) . PHP_EOL;

    echo "Sorted by comment:" . PHP_EOL;
    foreach ($service->accountTransactionsSorted($acc->id(), new SortByComment()) as $t) {
        echo "  - {$t->comment()} ({$t->money()})\n";
    }

    echo "Sorted by date:" . PHP_EOL;
    foreach ($service->accountTransactionsSorted($acc->id(), new SortByDate()) as $t) {
        echo "  - {$t->comment()} ({$t->date()->format('Y-m-d')})\n";
    }
}