<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'ServiceController::index');
$routes->post('/services/create', 'ServiceController::create');
$routes->post('/services/update/(:num)', 'ServiceController::update/$1');
$routes->get('/services/delete/(:num)', 'ServiceController::delete/$1');
