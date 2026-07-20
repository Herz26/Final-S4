<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barèmes - Mobile Money</title>
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
                    <a href="/operator/fees" class="list-group-item list-group-item-action active">Barèmes de frais</a>
                    <a href="/operator/gains" class="list-group-item list-group-item-action">Gains par opération</a>
                    <a href="/operator/comptes" class="list-group-item list-group-item-action">Situation des comptes</a>
                    <a href="/operator/transactions" class="list-group-item list-group-item-action">Toutes les transactions</a>
                </div>
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4>Ajouter un barème</h4>
                            </div>
                            <div class="card-body">
                                <?php if (session()->get('success')): ?>
                                    <div class="alert alert-success"><?= session()->get('success') ?></div>
                                <?php endif; ?>
                                <form action="/operator/fees" method="post">
                                    <?= csrf_field() ?>
                                    <div class="mb-3">
                                        <label for="operator_id" class="form-label">Opérateur</label>
                                        <select class="form-select" id="operator_id" name="operator_id" required>
                                            <?php foreach ($operators as $operator): ?>
                                                <option value="<?= $operator['id'] ?>"><?= $operator['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="operation_type_id" class="form-label">Type d'opération</label>
                                        <select class="form-select" id="operation_type_id" name="operation_type_id" required>
                                            <?php foreach ($types as $type): ?>
                                                <option value="<?= $type['id'] ?>"><?= $type['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="min_amount" class="form-label">Montant minimum</label>
                                        <input type="number" class="form-control" id="min_amount" name="min_amount" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="max_amount" class="form-label">Montant maximum</label>
                                        <input type="number" class="form-control" id="max_amount" name="max_amount" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="fee" class="form-label">Frais</label>
                                        <input type="number" class="form-control" id="fee" name="fee" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Ajouter</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h4>Liste des barèmes</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Opérateur</th>
                                            <th>Type</th>
                                            <th>Min</th>
                                            <th>Max</th>
                                            <th>Frais</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($fees as $fee): ?>
                                            <tr>
                                                <td><?= $fee['operator_name'] ?></td>
                                                <td><?= $fee['operation_name'] ?></td>
                                                <td><?= number_format($fee['min_amount'], 0, ',', ' ') ?></td>
                                                <td><?= number_format($fee['max_amount'], 0, ',', ' ') ?></td>
                                                <td><?= number_format($fee['fee'], 0, ',', ' ') ?></td>
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
