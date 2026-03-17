<?php

use App\Controllers\UserController;
use App\Core\Routing\Router;

return function (Router $router, $render) {
    $userController = new UserController($render);

    $router->get('/', [$userController, 'index']);
    $router->post('/users/generate', [$userController, 'generate']);
    $router->post('/users/import', [$userController, 'import']);
    $router->get('/count/(\w+)', [$userController, 'countByField']);
};
