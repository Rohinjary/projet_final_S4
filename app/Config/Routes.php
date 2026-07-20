<?php
use CodeIgniter\Router\RouteCollection;
/**
 * @var RouteCollection $routes
 */

// Auth
$routes->get('/', 'AuthController::login');
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::loginPost');
$routes->get('logout', 'AuthController::logout');

// Caisse
$routes->group('caisse', ['filter' => 'auth'], function($routes) {
    $routes->get('choix',   'CaisseController::choix');
    $routes->post('valider', 'CaisseController::valider');
});

// Achat
$routes->group('achat', ['filter' => 'auth'], function($routes) {
    $routes->get('saisie',        'AchatController::saisie');
    $routes->post('ajouterLigne', 'AchatController::ajouterLigne');
    $routes->post('cloturer',     'AchatController::cloturer');
});