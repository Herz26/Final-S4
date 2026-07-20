<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfert Multiple - Mobile Money</title>
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
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Transfert multiple</h4>
                    </div>
                    <div class="card-body">
                        <?php if (session()->get('success')): ?>
                            <div class="alert alert-success"><?= session()->get('success') ?></div>
                        <?php endif; ?>
                        <?php if (session()->get('error')): ?>
                            <div class="alert alert-danger"><?= session()->get('error') ?></div>
                        <?php endif; ?>
                        <form action="/client/transfert-multiple" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label for="total_amount" class="form-label">Montant total à répartir (Ar)</label>
                                <input type="number" class="form-control" id="total_amount" name="total_amount" required min="200" step="100">
                                <div class="form-text text-muted">Ce montant sera divisé équitablement entre tous les destinataires.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Destinataires</label>
                                <div id="recipients-container">
                                    <div class="recipient-row mb-2">
                                        <div class="row align-items-center">
                                            <div class="col-md-10">
                                                <input type="text" class="form-control" name="recipients[0][phone]" placeholder="Numéro de téléphone" required pattern="[0-9]{10}" maxlength="10">
                                            </div>
                                            <div class="col-md-2 d-flex align-items-center">
                                                <button type="button" class="btn btn-danger btn-sm remove-recipient" style="display: none;">Supprimer</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-secondary btn-sm mt-2" id="add-recipient">+ Ajouter un destinataire</button>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="include_withdrawal_fee" name="include_withdrawal_fee" value="1">
                                <label class="form-check-label" for="include_withdrawal_fee">
                                    Inclure les frais de retrait pour tous les destinataires
                                </label>
                            </div>
                            <div class="alert alert-info">
                                <strong>Montant par destinataire :</strong> <span id="per_recipient_amount">0</span> Ar
                            </div>
                            <button type="submit" class="btn btn-info">Transférer</button>
                            <a href="/client/dashboard" class="btn btn-secondary">Annuler</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const totalAmountInput = document.getElementById('total_amount');
        const perRecipientDisplay = document.getElementById('per_recipient_amount');

        function updatePerRecipientAmount() {
            const total = parseFloat(totalAmountInput.value) || 0;
            const rows = document.querySelectorAll('.recipient-row');
            const count = rows.length;
            const perRecipient = count > 0 ? Math.floor(total / count) : 0;
            perRecipientDisplay.textContent = perRecipient.toLocaleString('fr-FR');
        }

        document.getElementById('add-recipient').addEventListener('click', function() {
            const container = document.getElementById('recipients-container');
            const index = container.children.length;
            const div = document.createElement('div');
            div.className = 'recipient-row mb-2';
            div.innerHTML = `
                <div class="row align-items-center">
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="recipients[${index}][phone]" placeholder="Numéro de téléphone (10 chiffres)" required pattern="[0-9]{10}" maxlength="10">
                    </div>
                    <div class="col-md-2 d-flex align-items-center">
                        <button type="button" class="btn btn-danger btn-sm remove-recipient">Supprimer</button>
                    </div>
                </div>
            `;
            container.appendChild(div);
            updateRemoveButtons();
            updatePerRecipientAmount();
        });

        document.getElementById('recipients-container').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-recipient')) {
                e.target.closest('.recipient-row').remove();
                updateRemoveButtons();
                updatePerRecipientAmount();
            }
        });

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.recipient-row');
            rows.forEach((row, index) => {
                const btn = row.querySelector('.remove-recipient');
                if (btn) {
                    btn.style.display = rows.length > 1 ? 'inline-block' : 'none';
                }
            });
        }

        totalAmountInput.addEventListener('input', updatePerRecipientAmount);

        updateRemoveButtons();
        updatePerRecipientAmount();
    </script>
</body>
</html>
