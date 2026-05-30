<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

// ── Auth ──────────────────────────────────────────────────────
$routes->get('/',       'AuthController::login');
$routes->get('login',   'AuthController::login');
$routes->post('login',  'AuthController::loginProcess');
$routes->get('logout',  'AuthController::logout');

// ── Admin ─────────────────────────────────────────────────────
$routes->group('admin', ['filter' => 'role:admin'], function($routes) {

    // Criteria
    $routes->get('criteria',                'Admin\CriteriaController::index');
    $routes->get('criteria/create',         'Admin\CriteriaController::create');
    $routes->post('criteria',               'Admin\CriteriaController::store');
    $routes->get('criteria/(:num)/edit',    'Admin\CriteriaController::edit/$1');
    $routes->post('criteria/(:num)',        'Admin\CriteriaController::update/$1');
    $routes->delete('criteria/(:num)',      'Admin\CriteriaController::delete/$1');

    // Hotels
    $routes->get('hotels',                  'Admin\HotelController::index');
    $routes->get('hotels/create',           'Admin\HotelController::create');
    $routes->post('hotels',                 'Admin\HotelController::store');
    $routes->get('hotels/(:num)/edit',      'Admin\HotelController::edit/$1');
    $routes->post('hotels/(:num)',          'Admin\HotelController::update/$1');
    $routes->delete('hotels/(:num)',        'Admin\HotelController::delete/$1');

    // POI
    $routes->get('poi',                     'Admin\PoiController::index');
    $routes->get('poi/create',              'Admin\PoiController::create');
    $routes->post('poi',                    'Admin\PoiController::store');
    $routes->get('poi/(:num)/edit',         'Admin\PoiController::edit/$1');
    $routes->post('poi/(:num)',             'Admin\PoiController::update/$1');
    $routes->delete('poi/(:num)',           'Admin\PoiController::delete/$1');
});

// ── User / Guest ──────────────────────────────────────────────
$routes->get('hotels',                      'User\HotelController::search');
$routes->get('evaluation',                  'User\EvaluationController::selectAlternatives');
$routes->post('evaluation/weights',         'User\EvaluationController::setWeights');
$routes->post('evaluation/calculate',       'User\EvaluationController::calculate');

// ── API untuk Flutter ─────────────────────────────────────────
$routes->group('api', function($routes) {
    $routes->post('auth/login',             'Api\AuthController::login');
    $routes->get('hotels',                  'Api\HotelController::index');
    $routes->get('criterias',               'Api\CriteriaController::index');
    $routes->post('evaluation/calculate',   'Api\EvaluationController::calculate');
});