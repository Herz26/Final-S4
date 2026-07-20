<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commissions inter-opérateurs - Mobile Money</title>
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
                    <a href="/operator/operation-types" class="list-group-item list-group-item-action">Types d'opérations</a>
                    <a href="/operator/fees" class="list-group-item list-group-item-action">Barèmes de frais</a>
                    <a href="/operator/commissions" class="list-group-item list-group-item-action active">Commissions inter-opérateurs</a>
                    <a href="/operator/gains" class="list-group-item list-group-item-action">Gains par opération</a>
                    <a href="/operator/settlements" class="list-group-item list-group-item-action">Montants à envoyer</a>
                    <a href="/operator/comptes" class="list-group-item list-group-item-action">Situation des comptes</a>
                    <a href="/operator/transactions" class="list-group-item list-group-item-action">Toutes les transactions</a>
                </div>
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-5">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4>Ajouter / Modifier une commission</h4>
                            </div>
                            <div class="card-body">
                                <?php if (session()->get('success')): ?>
                                    <div class="alert alert-success"><?= session()->get('success') ?></div>
                                <?php endif; ?>
                                <?php if (session()->get('error')): ?>
                                    <div class="alert alert-danger"><?= session()->get('error') ?></div>
                                <?php endif; ?>
                                <form action="/operator/commissions" method="post">
                                    <?= csrf_field() ?>
                                    <div class="mb-3">
                                        <label for="from_operator_id" class="form-label">Opérateur source</label>
                                        <select class="form-select" id="from_operator_id" name="from_operator_id" required>
                                            <?php foreach ($operators as $operator): ?>
                                                <option value="<?= $operator['id'] ?>"><?= $operator['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="to_operator_id" class="form-label">Opérateur destination</label>
                                        <select class="form-select" id="to_operator_id" name="to_operator_id" required>
                                            <?php foreach ($operators as $operator): ?>
                                                <option value="<?= $operator['id'] ?>"><?= $operator['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="commission_percentage" class="form-label">Commission (%)</label>
                                        <input type="number" class="form-control" id="commission_percentage" name="commission_percentage" required min="0" max="100" step="0.1">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-header">
                                <h4>Liste des commissions</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>De</th>
                                            <th>Vers</th>
                                            <th>Commission (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($commissions as $commission): ?>
                                            <tr>
                                                <td><?= $commission['from_operator_name'] ?></td>
                                                <td><?= $commission['to_operator_name'] ?></td>
                                                <td><?= number_format($commission['commission_percentage'], 1, ',', ' ') ?> %</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
