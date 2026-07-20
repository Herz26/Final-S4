-- Base de données Mobile Money (SQLite)

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
-- 033 = Airtel, 032 et 037 = Orange, 034 et 038 = Yas
INSERT OR IGNORE INTO operators (id, name, prefix) VALUES (1, 'Airtel', '033');
INSERT OR IGNORE INTO operators (id, name, prefix) VALUES (2, 'Orange', '032');
INSERT OR IGNORE INTO operators (id, name, prefix) VALUES (3, 'Orange', '037');
INSERT OR IGNORE INTO operators (id, name, prefix) VALUES (4, 'Yas', '034');
INSERT OR IGNORE INTO operators (id, name, prefix) VALUES (5, 'Yas', '038');

-- Insertion des types d'opérations
INSERT OR IGNORE INTO operation_types (id, name, description) VALUES (1, 'dépôt', 'Dépôt d argent sur le compte');
INSERT OR IGNORE INTO operation_types (id, name, description) VALUES (2, 'retrait', 'Retrait d argent du compte');
INSERT OR IGNORE INTO operation_types (id, name, description) VALUES (3, 'transfert', 'Transfert d argent vers un autre client');

-- Frais pour Airtel (retrait et transfert)
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

-- Frais pour Orange 032 (retrait et transfert)
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

-- Frais pour Orange 037 (retrait et transfert)
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (3, 2, 100, 1000, 50);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (3, 2, 1001, 5000, 50);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (3, 2, 5001, 10000, 100);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (3, 2, 10001, 25000, 200);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (3, 2, 25001, 50000, 400);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (3, 2, 50001, 100000, 800);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (3, 2, 100001, 250000, 1500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (3, 2, 250001, 500000, 1500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (3, 2, 500001, 1000000, 2500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (3, 2, 1000001, 2000000, 3000);

INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (3, 3, 100, 1000, 50);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (3, 3, 1001, 5000, 50);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (3, 3, 5001, 10000, 100);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (3, 3, 10001, 25000, 200);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (3, 3, 25001, 50000, 400);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (3, 3, 50001, 100000, 800);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (3, 3, 100001, 250000, 1500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (3, 3, 250001, 500000, 1500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (3, 3, 500001, 1000000, 2500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (3, 3, 1000001, 2000000, 3000);

-- Frais pour Yas 034 (retrait et transfert)
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (4, 2, 100, 1000, 50);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (4, 2, 1001, 5000, 50);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (4, 2, 5001, 10000, 100);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (4, 2, 10001, 25000, 200);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (4, 2, 25001, 50000, 400);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (4, 2, 50001, 100000, 800);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (4, 2, 100001, 250000, 1500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (4, 2, 250001, 500000, 1500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (4, 2, 500001, 1000000, 2500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (4, 2, 1000001, 2000000, 3000);

INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (4, 3, 100, 1000, 50);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (4, 3, 1001, 5000, 50);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (4, 3, 5001, 10000, 100);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (4, 3, 10001, 25000, 200);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (4, 3, 25001, 50000, 400);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (4, 3, 50001, 100000, 800);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (4, 3, 100001, 250000, 1500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (4, 3, 250001, 500000, 1500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (4, 3, 500001, 1000000, 2500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (4, 3, 1000001, 2000000, 3000);

-- Frais pour Yas 038 (retrait et transfert)
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (5, 2, 100, 1000, 50);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (5, 2, 1001, 5000, 50);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (5, 2, 5001, 10000, 100);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (5, 2, 10001, 25000, 200);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (5, 2, 25001, 50000, 400);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (5, 2, 50001, 100000, 800);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (5, 2, 100001, 250000, 1500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (5, 2, 250001, 500000, 1500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (5, 2, 500001, 1000000, 2500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (5, 2, 1000001, 2000000, 3000);

INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (5, 3, 100, 1000, 50);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (5, 3, 1001, 5000, 50);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (5, 3, 5001, 10000, 100);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (5, 3, 10001, 25000, 200);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (5, 3, 25001, 50000, 400);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (5, 3, 50001, 100000, 800);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (5, 3, 100001, 250000, 1500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (5, 3, 250001, 500000, 1500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (5, 3, 500001, 1000000, 2500);
INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES (5, 3, 1000001, 2000000, 3000);
