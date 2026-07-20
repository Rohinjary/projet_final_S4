<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', 'Admin\AuthController::index');

$routes->post('client/login', 'Admin\AuthController::clientLogin');
$routes->get('client/dashboard', 'Admin\AuthController::clientDashboard');

$routes->post('admin/login', 'Admin\AuthController::operatorLogin');
$routes->get('admin/dashboard', 'Admin\AuthController::adminDashboard');

$routes->get('logout', 'Admin\AuthController::logout');
