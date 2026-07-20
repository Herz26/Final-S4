<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Types d opérations - Mobile Money</title>
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
                    <a href="/operator/operation-types" class="list-group-item list-group-item-action active">Types d opérations</a>
                    <a href="/operator/fees" class="list-group-item list-group-item-action">Barèmes de frais</a>
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
                                <h4>Ajouter un type d opération</h4>
                            </div>
                            <div class="card-body">
                                <?php if (session()->get('success')): ?>
                                    <div class="alert alert-success"><?= session()->get('success') ?></div>
                                <?php endif; ?>
                                <form action="/operator/operation-types" method="post">
                                    <?= csrf_field() ?>
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nom</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <input type="text" class="form-control" id="description" name="description">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Ajouter</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h4>Liste des types</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($types as $type): ?>
                                            <tr>
                                                <td><?= $type['name'] ?></td>
                                                <td><?= $type['description'] ?></td>
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
