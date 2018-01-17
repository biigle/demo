<?php

$router->group(['middleware' => 'auth:web,api', 'namespace' => 'Api', 'prefix' => 'api/v1'],
    function ($router) {
        $router->post('projects/demo', 'DemoProjectController@store');
    });
