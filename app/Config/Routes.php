<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('auth', 'Auth::index');
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/logout', 'Auth::logout');

$routes->group('client', ['filter' => 'client'], static function ($routes) {
    $routes->get('dashboard', 'Client::dashboard');
    $routes->get('depot', 'Client::depot');
    $routes->post('depot', 'Client::depot');
    $routes->get('retrait', 'Client::retrait');
    $routes->post('retrait', 'Client::retrait');
    $routes->get('transfert', 'Client::transfert');
    $routes->post('transfert', 'Client::transfert');
    $routes->get('transfert-multiple', 'Client::transfertMultiple');
    $routes->post('transfert-multiple', 'Client::transfertMultiple');
    $routes->get('historique', 'Client::historique');
});

$routes->group('operator', ['filter' => 'operator'], static function ($routes) {
    $routes->get('/', 'Operator::index');
    $routes->get('prefixes', 'Operator::prefixes');
    $routes->post('prefixes', 'Operator::prefixes');
    $routes->get('fees', 'Operator::fees');
    $routes->post('fees', 'Operator::fees');
    $routes->get('operation-types', 'Operator::operationTypes');
    $routes->post('operation-types', 'Operator::operationTypes');
    $routes->get('commissions', 'Operator::commissions');
    $routes->post('commissions', 'Operator::commissions');
    $routes->get('gains', 'Operator::gains');
    $routes->get('settlements', 'Operator::settlements');
    $routes->get('comptes', 'Operator::comptes');
    $routes->get('transactions', 'Operator::transactions');
});

$routes->get('operator/login', 'Operator::login');
$routes->post('operator/login', 'Operator::login');
$routes->get('operator/logout', 'Operator::logout');
