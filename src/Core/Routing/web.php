<?php

use App\Controllers\AuthController;
use App\Controllers\LanguageController;
use App\Controllers\UserController;
use App\Controllers\OrganizationController;
use App\Core\Routing\Router;

return function (Router $router, $render) {
    $userController = new UserController($render);
    $orgController = new OrganizationController($render);
    $authController = new AuthController($render);
    $langController = new LanguageController();

    // User Routes
    $router->get('/', [$userController, 'index']);
    $router->post('/users/generate', [$userController, 'generate']);
    $router->post('/users/import', [$userController, 'import']);
    $router->get('/count/(\w+)', [$userController, 'countByField']);

    // Organization Routes
    $router->get('/organizations', [$orgController, 'index']);
    $router->get('/organizations/create', [$orgController, 'create']);
    $router->post('/organizations/store', [$orgController, 'store']);
    $router->get('/organizations/edit/(\d+)', [$orgController, 'edit']);
    $router->post('/organizations/update/(\d+)', [$orgController, 'update']);
    $router->post('/organizations/delete/(\d+)', [$orgController, 'delete']);

    //Authentication Routes
    $router->get('/login', [$authController, 'showLogin']);
    $router->post('/login', [$authController, 'login']);
    $router->post('/logout', [$authController, 'logout']);

    //Language Route
    $router->get('/lang/([a-z]{2})', [$langController, 'switchLanguage']);
};
