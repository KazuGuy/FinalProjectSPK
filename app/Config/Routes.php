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
$routes->get('register', 'AuthController::register');
$routes->post('register', 'AuthController::registerProcess');
$routes->get('guest',   'AuthController::guestLogin');
$routes->get('logout',  'AuthController::logout');

// ── Admin ─────────────────────────────────────────────────────
$routes->group('admin', ['filter' => 'role:admin'], function($routes) {
    $routes->get('dashboard',               'Admin\DashboardController::index');

    // Criteria
    $routes->get('criteria',             'Admin\CriteriaController::index');
    $routes->get('criteria/(:num)/edit', 'Admin\CriteriaController::edit/$1');
    $routes->post('criteria/(:num)',     'Admin\CriteriaController::update/$1');

    // Hotels
    $routes->get('hotels',                  'Admin\HotelController::index');
    $routes->get('hotels/create',           'Admin\HotelController::create');
    $routes->post('hotels',                 'Admin\HotelController::store');
    $routes->get('hotels/(:num)/edit',      'Admin\HotelController::edit/$1');
    $routes->put('hotels/(:num)',           'Admin\HotelController::update/$1');
    $routes->delete('hotels/(:num)',        'Admin\HotelController::delete/$1');

    // POI
    $routes->get('poi',                     'Admin\PoiController::index');
    $routes->get('poi/create',              'Admin\PoiController::create');
    $routes->post('poi',                    'Admin\PoiController::store');
    $routes->get('poi/(:num)/edit',         'Admin\PoiController::edit/$1');
    $routes->put('poi/(:num)',              'Admin\PoiController::update/$1');
    $routes->delete('poi/(:num)',           'Admin\PoiController::delete/$1');
});

// ── User / Guest ──────────────────────────────────────────────
$routes->get('hotels',                      'User\HotelController::search');
$routes->get('poi',                         'User\PoiController::index');
$routes->group('evaluation', ['filter' => 'role:user'], function($routes) {
    $routes->get('/',                       'User\EvaluationController::selectAlternatives');
    $routes->post('weights',                'User\EvaluationController::setWeights');
    $routes->post('calculate',              'User\EvaluationController::calculate');
});

// ── API untuk Flutter ─────────────────────────────────────────
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {

    // Auth
    $routes->post('login',    'AuthController::login');
    $routes->post('register', 'AuthController::register');

    // Hotels
    $routes->get('hotels',          'HotelController::index');
    $routes->post('hotels',         'HotelController::create');
    $routes->put('hotels/(:num)',    'HotelController::update/$1');
    $routes->delete('hotels/(:num)','HotelController::delete/$1');

    // Criterias & POI
    $routes->get('criterias',       'CriteriaController::index');
    $routes->post('criterias/(:num)', 'CriteriaController::update/$1');
    $routes->get('poi',             'PoiController::index');
    $routes->post('poi',            'PoiController::create');
    $routes->put('poi/(:num)',     'PoiController::update/$1');
    $routes->delete('poi/(:num)',  'PoiController::delete/$1');

    // Evaluation
    $routes->post('evaluation/calculate', 'EvaluationController::calculate');

    //for CORS please jalan lah bgke
    $routes->options('(:any)', function() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        return service('response')->setStatusCode(204);
    });
});
