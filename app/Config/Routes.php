<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', static function () {
    return redirect()->to(site_url('client/login'));
});

// Espace client
$routes->get('client/login', 'Client\ClientController::login');
$routes->post('client/login', 'Client\ClientController::authentifier');
$routes->get('client/choix-prefixe', 'Client\ClientController::choixPrefixe');
$routes->post('client/valider-nouveau-numero', 'Client\ClientController::validerNouveauNumero');
$routes->get('client/info', 'Client\ClientController::info');
$routes->post('client/enregistrer-info', 'Client\ClientController::enregistrerInfo');
$routes->get('client/passer-info', 'Client\ClientController::passerInfo');
$routes->get('client/accueil', 'Client\ClientController::accueil');
$routes->get('client/depot', 'Client\ClientController::depot');
$routes->post('client/traiter-depot', 'Client\ClientController::traiterDepot');
$routes->get('client/retrait', 'Client\ClientController::retrait');
$routes->post('client/traiter-retrait', 'Client\ClientController::traiterRetrait');
$routes->get('client/transfert', 'Client\ClientController::transfert');
$routes->post('client/traiter-transfert', 'Client\ClientController::traiterTransfert');
$routes->get('client/historique', 'Client\ClientController::historique');
$routes->get('client/deconnexion', 'Client\ClientController::deconnexion');

// Authentification opérateur
$routes->get('admin/login', 'Admin\AuthController::operatorForm');
$routes->post('admin/login', 'Admin\AuthController::operatorLogin');
$routes->get('admin/dashboard', 'Admin\AuthController::adminDashboard');
$routes->get('logout', 'Admin\AuthController::logout');

// Configuration opérateur
$routes->get('admin/operateurs', 'Admin\OperateurController::index');
$routes->post('admin/operateurs', 'Admin\OperateurController::store');
$routes->post('admin/operateurs/(:num)', 'Admin\OperateurController::update/$1');

$routes->get('admin/prefixes', 'Admin\PrefixeController::index');
$routes->post('admin/prefixes', 'Admin\PrefixeController::store');
$routes->post('admin/prefixes/(:num)', 'Admin\PrefixeController::update/$1');
$routes->post('admin/prefixes/(:num)/delete', 'Admin\PrefixeController::delete/$1');

$routes->get('admin/commissions', 'Admin\CommissionController::index');
$routes->post('admin/commissions/(:num)', 'Admin\CommissionController::save/$1');

$routes->get('admin/baremes', 'Admin\BaremeController::index');
$routes->post('admin/baremes', 'Admin\BaremeController::store');
$routes->post('admin/baremes/(:num)', 'Admin\BaremeController::update/$1');
$routes->post('admin/types', 'Admin\BaremeController::storeType');

// Rapports
$routes->get('admin/gains', 'Admin\RapportController::gains');
$routes->get('admin/reversements', 'Admin\RapportController::reversements');
$routes->get('admin/comptes', 'Admin\RapportController::comptes');
