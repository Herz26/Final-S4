<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/operator">Opérateur - Mobile Money</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Opérateur: <?= session()->get('operator_name') ?></span>
                <a class="nav-link" href="/operator/logout">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="list-group">
                    <a href="/operator" class="list-group-item list-group-item-action">Dashboard</a>
                    <a href="/operator/prefixes" class="list-group-item list-group-item-action">Configuration des préfixes</a>
                    <a href="/operator/operation-types" class="list-group-item list-group-item-action">Types d opérations</a>
                    <a href="/operator/fees" class="list-group-item list-group-item-action">Barèmes de frais</a>
                    <a href="/operator/gains" class="list-group-item list-group-item-action">Gains par opération</a>
                    <a href="/operator/comptes" class="list-group-item list-group-item-action">Situation des comptes</a>
                    <a href="/operator/transactions" class="list-group-item list-group-item-action active">Toutes les transactions</a>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h4>Toutes les transactions</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Téléphone</th>
                                    <th>Type</th>
                                    <th>Montant</th>
                                    <th>Frais</th>
                                    <th>Total débité</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $transaction): ?>
                                    <tr>
                                        <td><?= $transaction['created_at'] ?></td>
                                        <td><?= $transaction['phone'] ?></td>
                                        <td><?= ucfirst($transaction['operation_name']) ?></td>
                                        <td><?= number_format($transaction['amount'], 0, ',', ' ') ?></td>
                                        <td><?= number_format($transaction['fee'], 0, ',', ' ') ?></td>
                                        <td><?= number_format($transaction['total_debited'], 0, ',', ' ') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
