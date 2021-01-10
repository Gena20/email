<?php

use App\Controller\GetCodeController;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// $app->addErrorMiddleware(true, true, true);

$app->get('/get-code', GetCodeController::class . ':handle');
$app->post('/get-code', GetCodeController::class . ':handle');

$app->run();
