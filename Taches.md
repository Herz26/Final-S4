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
- Script SQL'`base.sql` a la racine : creation des tables, indexes et donnees initiales
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
- Application fonctionnelle sur `localhost:8080/auth`

## Version 2 (v2) - Livraison a 17h10

### Taches de Herizo
- Mise a jour de `Taches.md` pour la partie v2
- Configuration des prefixes valable pour les autres operateurs dans `base.sql`
- Ajout table `inter_operator_commissions` dans `base.sql` et `scripts/create_tables.php`
- Creation du model'`InterOperatorCommissionModel`
- Mise a jour de `app/Config/Routes.php` pour les nouvelles routes v2
- Mise a jour du controller `Operator.php` :
  - Methode `commissions()` pour la configuration des commissions inter-opérateurs
  - Methode `gains()` modifiee pour separer gains propres et commissions inter-opérateurs
  - Methode `settlements()` pour la situation des montants a envoyer a chaque operateur
- Mise a jour du controller `Client.php` :
  - Option "inclure frais de retrait" dans le transfert simple
  - Methode `transfertMultiple()` pour l'envoi multiple vers plusieurs numeros
- Tests fonctionnels cote operateur et client

### Taches de Kamel
- Views cote operateur v2 :
  - `operator/commissions.php` pour la configuration des commissions inter-opérateurs
  - `operator/settlements.php` pour la situation des montants a envoyer
  - Mise a jour de `operator/gains.php` pour separer operateur et autres operateurs
  - Mise a jour de `operator/dashboard.php` pour ajouter les nouveaux liens dans le menu
- Views cote client v2 :
  - Mise a jour de `client/transfert.php` avec checkbox "inclure frais de retrait"
  - Creation de `client/transfert_multiple.php` pour l'envoi multiple
  - Mise a jour de `client/dashboard.php` avec lien vers transfert multiple
- Integration CSS sur les nouvelles views
- Tests fonctionnels et verification des flux v2


## Fonctionnalites v2
### Cote operateur
- Configuration des prefixes valable pour les autres operateurs
- Configuration pourcentage de commissions pour les transferts vers les autres operateurs
- Page gains separee : gains propres vs commissions inter-operateurs
- Situation des montants a envoyer a chaque operateur (settlements)

### Cote client
- Option "inclure frais de retrait" lors de l'envoi de transfert
- Envoi multiple vers plusieurs numeros avec division du montant

## Livrables v2
- Tag Git `v2`
- Fichier `base.sql` mis a jour a la racine
- Fichier `Taches.md` mis a jour a la racine
- Application fonctionnelle sur `localhost:8080/auth`
