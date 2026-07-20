<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card login-card">
                    <div class="card-header">
                        <h4 class="text-center">Connexion Mobile Money</h4>
                    </div>
                    <div class="card-body">
                        <?php if (session()->get('error')): ?>
                            <div class="alert alert-danger"><?= session()->get('error') ?></div>
                        <?php endif; ?>
                        <?php if (session()->get('errors')): ?>
                            <div class="alert alert-danger">
                                <?php foreach (session()->get('errors') as $error): ?>
                                    <div><?= $error ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <form action="/auth/login" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Numéro de téléphone</label>
                                <input type="text" class="form-control" id="phone" name="phone" required minlength="10" maxlength="10" pattern="[0-9]{10}">
                            </div>
                            <div class="mb-3">
                                <label for="pin" class="form-label">PIN opérateur (optionnel)</label>
                                <input type="password" class="form-control" id="pin" name="pin" maxlength="10">
                                <div class="form-text">Laissez vide pour une connexion client. Entrez 0000 pour l'opérateur.</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
