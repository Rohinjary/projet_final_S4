<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', static function () {
    return redirect()->to(site_url('client/login'));
});

$routes->post('client/login', 'Admin\AuthController::clientLogin');
$routes->get('client/dashboard', 'Admin\AuthController::clientDashboard');

$routes->get('admin/login', 'Admin\AuthController::operatorForm');
$routes->post('admin/login', 'Admin\AuthController::operatorLogin');
$routes->get('admin/dashboard', 'Admin\AuthController::adminDashboard');

$routes->get('admin/prefixes', 'Admin\PrefixeController::index');
$routes->post('admin/prefixes', 'Admin\PrefixeController::store');
$routes->post('admin/prefixes/(:num)/delete', 'Admin\PrefixeController::delete/$1');

$routes->get('admin/baremes', 'Admin\BaremeController::index');
$routes->post('admin/baremes', 'Admin\BaremeController::store');
$routes->post('admin/baremes/(:num)', 'Admin\BaremeController::update/$1');
$routes->post('admin/types', 'Admin\BaremeController::storeType');

$routes->get('admin/gains', 'Admin\RapportController::gains');
$routes->get('admin/comptes', 'Admin\RapportController::comptes');

$routes->get('logout', 'Admin\AuthController::logout');
// CLIENT
$routes->get('client/login', 'Client\ClientController::login');
$routes->post('client/authentifier', 'Client\ClientController::authentifier');
$routes->get('client/valider-nouveau-numero', 'Client\ClientController::login');

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
