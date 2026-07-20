<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfert - Mobile Money</title>
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
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card login-card">
                    <div class="card-header">
                        <h4>Effectuer un transfert</h4>
                    </div>
                    <div class="card-body">
                        <?php if (session()->get('success')): ?>
                            <div class="alert alert-success"><?= session()->get('success') ?></div>
                        <?php endif; ?>
                        <?php if (session()->get('error')): ?>
                            <div class="alert alert-danger"><?= session()->get('error') ?></div>
                        <?php endif; ?>
                        <form action="/client/transfert" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label for="to_phone" class="form-label">Numéro de téléphone du destinataire</label>
                                <input type="text" class="form-control" id="to_phone" name="to_phone" required>
                            </div>
                            <div class="mb-3">
                                <label for="amount" class="form-label">Montant (Ar)</label>
                                <input type="number" class="form-control" id="amount" name="amount" required min="1">
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="include_withdrawal_fee" name="include_withdrawal_fee" value="1">
                                <label class="form-check-label" for="include_withdrawal_fee">
                                    Inclure les frais de retrait (le destinataire recevra le montant complet)
                                </label>
                            </div>
                            <button type="submit" class="btn btn-info">Transférer</button>
                            <a href="/client/dashboard" class="btn btn-secondary">Annuler</a>
                        </form>
                        <div class="mt-3">
                            <a href="/client/transfert-multiple" class="btn btn-outline-primary">Transfert multiple</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
