# Financial Transactions System

This project is a demonstration of a financial transactions system implemented in pure PHP (no frameworks or databases), applying modern OOP design principles including SOLID, GRASP, and key design patterns.

## ✅ Features

- Deposit, Withdrawal, and Transfer operations between accounts
- Immutability and type-safety for money operations
- In-memory persistence (no DB requirement)
- Sortable transaction listings (by comment or date)
- Fully PSR-compliant and compatible with `phpstan` and `phpcs`

## 🧱 Architecture Overview

### 1. **Domain Layer**
- `Account` — aggregates all related transactions and current balance.
- `Transaction` (abstract) — base class extended by `Deposit`, `Withdrawal`, `Transfer`.
- `Money` — value object enforcing immutability and currency safety.
- `Currency` — enum representing supported currencies.

### 2. **Application Layer**
- `TransactionService` — orchestrates domain logic (Controller pattern).
- `SortByComment`, `SortByDate` — implements `SortStrategyInterface` to sort transactions.

### 3. **Infrastructure Layer**
- `InMemoryAccountRepository`, `InMemoryTransactionRepository` — store data using arrays for testability and speed.

## 🧠 Design Patterns & Principles Used

| Concept | Description |
|--------|-------------|
| **Strategy Pattern** | Used in `SortByComment` and `SortByDate` to sort transactions dynamically without modifying core logic. |
| **Repository Pattern** | Interfaces and in-memory implementations abstract data access, enabling future DB swap without affecting core logic. |
| **Value Object Pattern** | `Money` is an immutable, self-validating object for handling amounts and currencies. |
| **GRASP - Controller** | `TransactionService` acts as a coordinator between application and domain logic. |
| **SOLID - SRP** | Each class focuses on a single responsibility. |
| **SOLID - OCP** | Sorting logic is extendable via new strategy implementations. |
| **SOLID - DIP** | Application code depends on abstractions, not concrete implementations. |

## ▶️ Usage

Run the example via CLI:
```bash
php example.php
