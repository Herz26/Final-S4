<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/client/dashboard">Shop my Money</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Téléphone: <?= session()->get('client_phone') ?></span>
                <a class="nav-link" href="/auth/logout">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (session()->get('success')): ?>
            <div class="alert alert-success"><?= session()->get('success') ?></div>
        <?php endif; ?>
        <?php if (session()->get('error')): ?>
            <div class="alert alert-danger"><?= session()->get('error') ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Solde</h5>
                    </div>
                    <div class="card-body">
                        <h2 class="text-success"><?= number_format($account['solde'], 0, ',', ' ') ?> Ar</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <h5>Dépôt</h5>
                        <p>Effectuer un dépôt</p>
                        <a href="/client/depot" class="btn btn-success">Déposer</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <h5>Retrait</h5>
                        <p>Effectuer un retrait</p>
                        <a href="/client/retrait" class="btn btn-warning">Retirer</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <h5>Transfert</h5>
                        <p>Transférer de l'argent</p>
                        <a href="/client/transfert" class="btn btn-info">Transférer</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <h5>Historique</h5>
                        <p>Voir l'historique</p>
                        <a href="/client/historique" class="btn btn-secondary">Historique</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
