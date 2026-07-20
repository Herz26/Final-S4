
-- Base de données Mobile Money

CREATE DATABASE IF NOT EXISTS mobile_money;
USE mobile_money;

-- Version 1


-- Table des opérateurs (préfixes téléphoniques)
CREATE TABLE IF NOT EXISTS operators (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    prefix TEXT NOT NULL UNIQUE,
    created_at TEXT NOT NULL DEFAULT (datetime('now'))
);

-- Table des clients
CREATE TABLE IF NOT EXISTS clients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    phone TEXT NOT NULL UNIQUE,
    operator_id INTEGER NOT NULL,
    nom TEXT NOT NULL,
    created_at TEXT NOT NULL DEFAULT (datetime('now')),
    FOREIGN KEY (operator_id) REFERENCES operators(id)
);

-- Table des comptes clients
CREATE TABLE IF NOT EXISTS accounts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id INTEGER NOT NULL UNIQUE,
    operator_id INTEGER NOT NULL,
    solde REAL NOT NULL DEFAULT 0.00,
    created_at TEXT NOT NULL DEFAULT (datetime('now')),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (operator_id) REFERENCES operators(id)
);

-- Table des types d'opérations
CREATE TABLE IF NOT EXISTS operation_types (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    description TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now'))
);

-- Table des barèmes de frais par opérateur et par tranche
CREATE TABLE IF NOT EXISTS operator_fees (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operator_id INTEGER NOT NULL,
    operation_type_id INTEGER NOT NULL,
    min_amount REAL NOT NULL,
    max_amount REAL NOT NULL,
    fee REAL NOT NULL,
    created_at TEXT NOT NULL DEFAULT (datetime('now')),
    FOREIGN KEY (operator_id) REFERENCES operators(id),
    FOREIGN KEY (operation_type_id) REFERENCES operation_types(id)
);

-- Table des transactions
CREATE TABLE IF NOT EXISTS transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    account_id INTEGER NOT NULL,
    operation_type_id INTEGER NOT NULL,
    amount REAL NOT NULL,
    fee REAL NOT NULL DEFAULT 0.00,
    total_debited REAL NOT NULL,
    description TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now')),
    FOREIGN KEY (account_id) REFERENCES accounts(id),
    FOREIGN KEY (operation_type_id) REFERENCES operation_types(id)
);

-- Table des transferts (pour tracer qui a transféré à qui)
CREATE TABLE IF NOT EXISTS transfers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    transaction_id INTEGER NOT NULL,
    from_client_id INTEGER NOT NULL,
    to_client_id INTEGER NOT NULL,
    from_phone TEXT NOT NULL,
    to_phone TEXT NOT NULL,
    created_at TEXT NOT NULL DEFAULT (datetime('now')),
    FOREIGN KEY (transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (from_client_id) REFERENCES clients(id),
    FOREIGN KEY (to_client_id) REFERENCES clients(id)
);

-- Index pour améliorer les performances
CREATE INDEX IF NOT EXISTS idx_accounts_client ON accounts(client_id);
CREATE INDEX IF NOT EXISTS idx_transactions_account ON transactions(account_id);
CREATE INDEX IF NOT EXISTS idx_transactions_type ON transactions(operation_type_id);
CREATE INDEX IF NOT EXISTS idx_transfers_from ON transfers(from_client_id);
CREATE INDEX IF NOT EXISTS idx_transfers_to ON transfers(to_client_id);
CREATE INDEX IF NOT EXISTS idx_operator_fees_op_type ON operator_fees(operator_id, operation_type_id);

-- Données de test

-- Insertion des opérateurs
INSERT OR IGNORE INTO operators (id, name, prefix) VALUES (1, 'Orange', '033');
INSERT OR IGNORE INTO operators (id, name, prefix) VALUES (2, 'Airtel', '037');

-- Insertion des types d'opérations
INSERT OR IGNORE INTO operation_types (id, name, description) VALUES (1, 'dépôt', 'Dépôt d argent sur le compte');
INSERT OR IGNORE INTO operation_types (id, name, description) VALUES (2, 'retrait', 'Retrait d argent du compte');
INSERT OR IGNORE INTO operation_types (id, name, description) VALUES (3, 'transfert', 'Transfert d argent vers un autre client');

-- Insertion des barèmes de frais pour Orange
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (1, 2, 100, 1000, 50);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (1, 2, 1001, 5000, 50);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (1, 2, 5001, 10000, 100);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (1, 2, 10001, 25000, 200);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (1, 2, 25001, 50000, 400);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (1, 2, 50001, 100000, 800);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (1, 2, 100001, 250000, 1500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (1, 2, 250001, 500000, 1500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (1, 2, 500001, 1000000, 2500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (1, 2, 1000001, 2000000, 3000);

-- Frais pour transfert Orange (mêmes tranches)
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (1, 3, 100, 1000, 50);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (1, 3, 1001, 5000, 50);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (1, 3, 5001, 10000, 100);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (1, 3, 10001, 25000, 200);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (1, 3, 25001, 50000, 400);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (1, 3, 50001, 100000, 800);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (1, 3, 100001, 250000, 1500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (1, 3, 250001, 500000, 1500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (1, 3, 500001, 1000000, 2500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (1, 3, 1000001, 2000000, 3000);

-- Frais pour Airtel (mêmes tranches)
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (2, 2, 100, 1000, 50);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (2, 2, 1001, 5000, 50);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (2, 2, 5001, 10000, 100);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (2, 2, 10001, 25000, 200);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (2, 2, 25001, 50000, 400);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (2, 2, 50001, 100000, 800);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (2, 2, 100001, 250000, 1500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (2, 2, 250001, 500000, 1500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (2, 2, 500001, 1000000, 2500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (2, 2, 1000001, 2000000, 3000);

INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (2, 3, 100, 1000, 50);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (2, 3, 1001, 5000, 50);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (2, 3, 5001, 10000, 100);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (2, 3, 10001, 25000, 200);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (2, 3, 25001, 50000, 400);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (2, 3, 50001, 100000, 800);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (2, 3, 100001, 250000, 1500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (2, 3, 250001, 500000, 1500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (2, 3, 500001, 1000000, 2500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (2, 3, 1000001, 2000000, 3000);
