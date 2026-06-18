<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ── Services (Kelola Layanan) ───────────────────────────────────────────────
$routes->get('/', 'ServiceController::index');
$routes->post('/services/create', 'ServiceController::create');
$routes->post('/services/update/(:num)', 'ServiceController::update/$1');
$routes->get('/services/delete/(:num)', 'ServiceController::delete/$1');

// ── Orders (Pesanan Laundry) ────────────────────────────────────────────────
$routes->get('/orders', 'OrderController::index');
$routes->post('/orders/create', 'OrderController::create');
$routes->post('/orders/status/(:num)', 'OrderController::updateStatus/$1');
$routes->post('/orders/complete/(:num)', 'OrderController::complete/$1');
$routes->get('/orders/history', 'OrderController::history');
$routes->get('/orders/delete/(:num)', 'OrderController::delete/$1');
