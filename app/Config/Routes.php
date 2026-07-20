<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', 'Admin\AuthController::index');

$routes->post('client/login', 'Admin\AuthController::clientLogin');
$routes->get('client/dashboard', 'Admin\AuthController::clientDashboard');

$routes->post('admin/login', 'Admin\AuthController::operatorLogin');
$routes->get('admin/dashboard', 'Admin\AuthController::adminDashboard');

$routes->get('admin/prefixes', 'Admin\PrefixeController::index');
$routes->post('admin/prefixes', 'Admin\PrefixeController::store');
$routes->post('admin/prefixes/(:num)/delete', 'Admin\PrefixeController::delete/$1');

$routes->get('admin/baremes', 'Admin\BaremeController::index');
$routes->post('admin/baremes', 'Admin\BaremeController::store');
$routes->post('admin/baremes/(:num)', 'Admin\BaremeController::update/$1');
$routes->post('admin/types', 'Admin\BaremeController::storeType');

$routes->get('logout', 'Admin\AuthController::logout');
// CLIENT
$routes->get('client/login', 'ClientController::login');
$routes->post('client/authentifier', 'ClientController::authentifier');

$routes->get('client/choix-prefixe', 'ClientController::choixPrefixe');
$routes->post('client/valider-nouveau-numero', 'ClientController::validerNouveauNumero');

$routes->get('client/info', 'ClientController::info');
$routes->post('client/enregistrer-info', 'ClientController::enregistrerInfo');
$routes->get('client/passer-info', 'ClientController::passerInfo');

$routes->get('client/accueil', 'ClientController::accueil');

$routes->get('client/depot', 'ClientController::depot');
$routes->post('client/traiter-depot', 'ClientController::traiterDepot');

$routes->get('client/retrait', 'ClientController::retrait');
$routes->post('client/traiter-retrait', 'ClientController::traiterRetrait');

$routes->get('client/transfert', 'ClientController::transfert');
$routes->post('client/traiter-transfert', 'ClientController::traiterTransfert');

$routes->get('client/historique', 'ClientController::historique');

$routes->get('client/deconnexion', 'ClientController::deconnexion');
