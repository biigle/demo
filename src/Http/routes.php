<?php

$router->group(
    ['middleware' => ['api', 'auth:web,api'], 'namespace' => 'Api', 'prefix' => 'api/v1'],
    function ($router) {
        $router->post('projects/demo', 'DemoProjectController@store');
    }
);
