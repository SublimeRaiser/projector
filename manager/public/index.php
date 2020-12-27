<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$errorMiddleware = $app->addErrorMiddleware((bool) getenv('APP_DEBUG'), true, true);

$app->get('/', function (Request $request, Response $response, array $args) {
    $data = [
        'name'  => 'Projector',
        'param' => $request->getQueryParams()['param'] ?? null,
    ];
    $response->getBody()->write((string) json_encode($data));
    $response = $response->withHeader('Content-Type', 'application/json');

    return $response;
});

$app->run();
