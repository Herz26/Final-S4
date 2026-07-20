# Taches - Projet Mobile Money

## Binome - S4 Info
- Herizo4089
- Kamel4093

## Version 1 (v1) - Livraison a 13h

### Taches de Herizo
- Mise a jour de `Taches.md` pour sa partie
- Configuration de la base de donnees SQLite dans `app/Config/Database.php`
- Creation des models : `OperatorModel`, `ClientModel`, `AccountModel`, `OperationTypeModel`, `TransactionModel`, `OperatorFeeModel`, `TransferModel`
- Controllers metier : `Auth.php`, `Client.php`
- Configuration des routes et filtres : `app/Config/Routes.php`, `app/Filters/Client.php`, `app/Filters/Operator.php`
- Script SQL `base.sql` a la racine : creation des tables, indexes et donnees initiales
- Initialisation de la base SQLite dans `writable/` et verification du fonctionnement
- Mise a jour de `Taches.md` pour sa partie

### Taches de Kamel

- Views cote client : `auth/login.php`, `client/dashboard.php`, `client/depot.php`, `client/retrait.php`, `client/transfert.php`, `client/historique.php`
- Views cote operateur : `operator/dashboard.php`, `operator/prefixes.php`, `operator/fees.php`, `operator/operation_types.php`, `operator/gains.php`, `operator/comptes.php`, `operator/transactions.php`
- Controller operateur : `Operator.php`
- Integration Bootstrap 5 et CSS/JS sur les views
- Tests fonctionnels et verification des flux client et operateur



## Fonctionnalites v1
### Cote operateur
- Configuration des prefixes telephoniques valides
- Creation des types d operations : depot, retrait, transfert
- Barèmes de frais modifiables par tranche de montant
- Consultation des gains via les frais de retrait et transfert
- Situation des comptes clients

### Cote client
- Login automatique par numero de telephone, sans inscription prealable
- Voir le solde
- Depot automatique
- Retrait automatique
- Transfert vers un autre client
- Historique des transactions

## Livrables
- Tag Git `v1`
- Fichier `base.sql` a la racine
- Fichier `Taches.md` a la racine
- Application fonctionnelle sur `localhost:8993`
