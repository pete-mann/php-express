<?php

/**
 * This is the entry point to the API, routes are defined here
 * @author Pete Mann - peter.mann.design@gmail.com
 */
require __DIR__ . "/../vendor/autoload.php";

use \ExpressPHP\core\App;
use \ExpressPHP\core\Request;
use \ExpressPHP\core\Response;
use \ExpressPHP\middlewares\JWT;
use \ExpressPHP\controllers\CtrlAuth;
use \ExpressPHP\controllers\CtrlClient;
use \ExpressPHP\controllers\CtrlProject;
use \ExpressPHP\controllers\CtrlWorkCode;
use \ExpressPHP\controllers\CtrlActivity;

$app = new App();
$ctrlAuth = new CtrlAuth();
$ctrlClient = new CtrlClient();
$ctrlProject = new CtrlProject();
$ctrlWorkCode = new CtrlWorkCode();
$ctrlActivity = new CtrlActivity();
$jwt = new JWT();

/**
 * Specify a callable method of a class like this so as to use a controller to handle the request. This approach will
 * keep this file small
 */

// Auth
$app->post('/auth', [$ctrlAuth, 'auth']);

$app->get('/test', function($req, $res) {
    $res->json($req->test());
});

// Client
$app->middleware($jwt)->get('/client', [$ctrlClient, 'index']);
$app->middleware($jwt)->get('/client/:clientId', [$ctrlClient, 'show']);
$app->middleware($jwt)->post('/client', [$ctrlClient, 'update']);

// Project
$app->middleware($jwt)->get('/client/:clientId/project', [$ctrlProject, 'getClientProjects']);
$app->middleware($jwt)->get('/client/:clientId/project/:projectId', [$ctrlProject, 'show']);
$app->middleware($jwt)->post('/client/:clientId/project/:projectId', [$ctrlProject, 'update']);

// WorkCode
$app->middleware($jwt)->get('/client/:clientId/project/:projectId/workcode', [$ctrlWorkCode, 'getClientProjectWorkCode']);
$app->middleware($jwt)->get('/client/:clientId/project/:projectId/workcode/:workCodeId', [$ctrlWorkCode, 'show']);
$app->middleware($jwt)->post('/client/:clientId/project/:projectId/workcode/:workCodeId', [$ctrlWorkCode, 'update']);

// Activity
$app->middleware($jwt)->get('/client/:clientId/project/:projectId/workcode/:workCodeId/activity', [$ctrlActivity, 'getClientProjectWorkCodeActivities']);
$app->middleware($jwt)->get('/client/:clientId/project/:projectId/workcode/:workCodeId/activity/:activityId', [$ctrlActivity, 'show']);
$app->middleware($jwt)->post('/client/:clientId/project/:projectId/workcode/:workCodeId/activity/:activityId', [$ctrlActivity, 'update']);

/**
 * The otherwise method is used to handle un-matched requests, this endpoint returns a 404 error
 */
$app->otherwise(function(Request $req, Response $res) {
    $res->send(404, [
        'title' => 'Are you lost? ¿Estas perdido? Es-tu perdu?',
        'message' => "No endpoint was found matching the request path of: {$req->getPath()}"
    ]);
});
