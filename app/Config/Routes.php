<?php
use CodeIgniter\Router\RouteCollection;
/**
 * @var RouteCollection $routes
 */

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
