<?php

/**
 * This is the entry point to the API, routes are defined here
 */

require_once 'app/core/App.php';
require_once 'app/user/CtrlUser.php';

$app = new App();
$ctrlUser = new CtrlUser();

/**
 * Specify a callable method of a class like this so as to use a controller to handle the request. This approach will
 * keep this file small
 */
$app->post('/user', [$ctrlUser, 'index']);

/**
 * The request method includes a test method that can be used to simply debug the application. This approach illustrates
 * how the request can also be handled in this file by passing an anonymous callable
 */
$app->get('/test', function($req, $res) {
    $res->json($req->test());
});

/**
 * The otherwise method is used to handle un-matched requests, this endpoint returns a 404 error
 */
$app->otherwise(function(Request $req, Response $res) {
    $res->send(404, [
        'title' => 'Are you lost?',
        'message' => "No endpoint was found matching the request path of: {$req->getPath()}"
    ]);
});
