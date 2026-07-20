<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/client/dashboard">Shop my Money</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/client/dashboard">Dashboard</a>
                <a class="nav-link" href="/auth/logout">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Historique des transactions</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Montant</th>
                                    <th>Frais</th>
                                    <th>Total débité</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $transaction): ?>
                                    <tr>
                                        <td><?= $transaction['created_at'] ?></td>
                                        <td><?= ucfirst($transaction['operation_name']) ?></td>
                                        <td><?= number_format($transaction['amount'], 0, ',', ' ') ?> Ar</td>
                                        <td><?= number_format($transaction['fee'], 0, ',', ' ') ?> Ar</td>
                                        <td><?= number_format($transaction['total_debited'], 0, ',', ' ') ?> Ar</td>
                                        <td><?= $transaction['description'] ?></td>
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
