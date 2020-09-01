<?php

use App\Controller\HomeController;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);

$app->get('/get-code', HomeController::class . ':handle');
$app->post('/get-code', HomeController::class . ':handle');

$app->run();
