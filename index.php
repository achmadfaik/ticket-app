<?php
require_once __DIR__ . '/vendor/autoload.php';
use App\Router\Router;
use App\Router\Request;
use App\Router\Response;
$router = new Router(new Request);

$router->get('/tickets', function($request) {return \App\Http\Controller\TicketController::index($request);});
$router->post('/tickets-update', function($request) {return \App\Http\Controller\TicketController::update($request);});

$router->get('/', function() {
    return Response::json(200, [
        'version' => '1.0',
        'data' => [
            'status' => 'OK',
        ],
    ]);
});
