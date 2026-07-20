<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Opérateur - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
</head>
    <body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card login-card">
                    <div class="card-header">
                        <h4 class="text-center">Connexion Opérateur</h4>
                    </div>
                    <div class="card-body">
                        <?php if (session()->get('error')): ?>
                            <div class="alert alert-danger"><?= session()->get('error') ?></div>
                        <?php endif; ?>
                        <form action="/operator/login" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label for="pin" class="form-label">PIN opérateur</label>
                                <input type="password" class="form-control" id="pin" name="pin" required maxlength="10">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                        </form>
                        <p class="text-muted mt-3 text-center">PIN par défaut : 0000</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
