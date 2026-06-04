<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/api/v1/categories', 'GET');
$controller = app()->make(App\Http\Controllers\Api\V1\CategoryController::class);
$response = $controller->get_categories($request);

echo json_encode($response->getData());
