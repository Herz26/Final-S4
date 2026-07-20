<?php
$dbPath = __DIR__ . '/../writable/mobile_money.db';
$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$queries = [
    "CREATE TABLE IF NOT EXISTS operators (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, prefix TEXT NOT NULL UNIQUE, created_at TEXT NOT NULL DEFAULT (datetime('now')))",
    "CREATE TABLE IF NOT EXISTS inter_operator_commissions (id INTEGER PRIMARY KEY AUTOINCREMENT, from_operator_id INTEGER NOT NULL, to_operator_id INTEGER NOT NULL, commission_percentage REAL NOT NULL, created_at TEXT NOT NULL DEFAULT (datetime('now')), FOREIGN KEY (from_operator_id) REFERENCES operators(id), FOREIGN KEY (to_operator_id) REFERENCES operators(id))",
    "CREATE TABLE IF NOT EXISTS clients (id INTEGER PRIMARY KEY AUTOINCREMENT, phone TEXT NOT NULL UNIQUE, operator_id INTEGER NOT NULL, nom TEXT NOT NULL, created_at TEXT NOT NULL DEFAULT (datetime('now')), FOREIGN KEY (operator_id) REFERENCES operators(id))",
    "CREATE TABLE IF NOT EXISTS accounts (id INTEGER PRIMARY KEY AUTOINCREMENT, client_id INTEGER NOT NULL UNIQUE, operator_id INTEGER NOT NULL, solde REAL NOT NULL DEFAULT 0.00, created_at TEXT NOT NULL DEFAULT (datetime('now')), FOREIGN KEY (client_id) REFERENCES clients(id), FOREIGN KEY (operator_id) REFERENCES operators(id))",
    "CREATE TABLE IF NOT EXISTS operation_types (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL UNIQUE, description TEXT, created_at TEXT NOT NULL DEFAULT (datetime('now')))",
    "CREATE TABLE IF NOT EXISTS operator_fees (id INTEGER PRIMARY KEY AUTOINCREMENT, operator_id INTEGER NOT NULL, operation_type_id INTEGER NOT NULL, min_amount REAL NOT NULL, max_amount REAL NOT NULL, fee REAL NOT NULL, created_at TEXT NOT NULL DEFAULT (datetime('now')), FOREIGN KEY (operator_id) REFERENCES operators(id), FOREIGN KEY (operation_type_id) REFERENCES operation_types(id))",
    "CREATE TABLE IF NOT EXISTS transactions (id INTEGER PRIMARY KEY AUTOINCREMENT, account_id INTEGER NOT NULL, operation_type_id INTEGER NOT NULL, amount REAL NOT NULL, fee REAL NOT NULL DEFAULT 0.00, total_debited REAL NOT NULL, description TEXT, created_at TEXT NOT NULL DEFAULT (datetime('now')), FOREIGN KEY (account_id) REFERENCES accounts(id), FOREIGN KEY (operation_type_id) REFERENCES operation_types(id))",
    "CREATE TABLE IF NOT EXISTS transfers (id INTEGER PRIMARY KEY AUTOINCREMENT, transaction_id INTEGER NOT NULL, from_client_id INTEGER NOT NULL, to_client_id INTEGER NOT NULL, from_phone TEXT NOT NULL, to_phone TEXT NOT NULL, created_at TEXT NOT NULL DEFAULT (datetime('now')), FOREIGN KEY (transaction_id) REFERENCES transactions(id), FOREIGN KEY (from_client_id) REFERENCES clients(id), FOREIGN KEY (to_client_id) REFERENCES clients(id))",
    "CREATE INDEX IF NOT EXISTS idx_accounts_client ON accounts(client_id)",
    "CREATE INDEX IF NOT EXISTS idx_transactions_account ON transactions(account_id)",
    "CREATE INDEX IF NOT EXISTS idx_transactions_type ON transactions(operation_type_id)",
    "CREATE INDEX IF NOT EXISTS idx_transfers_from ON transfers(from_client_id)",
    "CREATE INDEX IF NOT EXISTS idx_transfers_to ON transfers(to_client_id)",
    "CREATE INDEX IF NOT EXISTS idx_operator_fees_op_type ON operator_fees(operator_id, operation_type_id)",
];

foreach ($queries as $query) {
    $pdo->exec($query);
}

$inserts = [
    "INSERT OR IGNORE INTO operators (id, name, prefix) VALUES (1, 'Airtel', '033')",
    "INSERT OR IGNORE INTO operators (id, name, prefix) VALUES (2, 'Orange', '032')",
    "INSERT OR IGNORE INTO operators (id, name, prefix) VALUES (3, 'Orange', '037')",
    "INSERT OR IGNORE INTO operators (id, name, prefix) VALUES (4, 'Yas', '034')",
    "INSERT OR IGNORE INTO operators (id, name, prefix) VALUES (5, 'Yas', '038')",
    "INSERT OR IGNORE INTO operation_types (id, name, description) VALUES (1, 'dépôt', 'Dépôt d argent sur le compte')",
    "INSERT OR IGNORE INTO operation_types (id, name, description) VALUES (2, 'retrait', 'Retrait d argent du compte')",
    "INSERT OR IGNORE INTO operation_types (id, name, description) VALUES (3, 'transfert', 'Transfert d argent vers un autre client')",
];

foreach ($inserts as $insert) {
    $pdo->exec($insert);
}

$fees = [100, 50, 1001, 50, 5001, 100, 10001, 200, 25001, 400, 50001, 800, 100001, 1500, 250001, 1500, 500001, 2500, 1000001, 3000];

for ($op = 1; $op <= 5; $op++) {
    for ($opType = 2; $opType <= 3; $opType++) {
        for ($i = 0; $i < count($fees); $i += 2) {
            $min = $fees[$i];
            $max = $i + 2 < count($fees) ? $fees[$i + 2] - 1 : 2000000;
            $fee = $fees[$i + 1];
            $pdo->exec("INSERT OR IGNORE INTO operator_fees (operator_id, operation_type_id, min_amount, max_amount, fee) VALUES ($op, $opType, $min, $max, $fee)");
        }
    }
}

$commissions = [
    [1, 2, 1.5], [1, 3, 1.5], [1, 4, 1.5], [1, 5, 1.5],
    [2, 1, 1.5], [2, 3, 1.5], [2, 4, 1.5], [2, 5, 1.5],
    [3, 1, 1.5], [3, 2, 1.5], [3, 4, 1.5], [3, 5, 1.5],
    [4, 1, 1.5], [4, 2, 1.5], [4, 3, 1.5], [4, 5, 1.5],
    [5, 1, 1.5], [5, 2, 1.5], [5, 3, 1.5], [5, 4, 1.5],
];

foreach ($commissions as $c) {
    $pdo->exec("INSERT OR IGNORE INTO inter_operator_commissions (from_operator_id, to_operator_id, commission_percentage) VALUES ($c[0], $c[1], $c[2])");
}

echo "Tables created and data inserted successfully.\n";
