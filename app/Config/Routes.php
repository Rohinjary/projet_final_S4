<?php
use CodeIgniter\Router\RouteCollection;
/**
 * @var RouteCollection $routes
 */

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
