## Php Express
This project is a light weight version of the popular expressjs Nodejs framework.

### Requirements
This is a LAMP or WAMP project, written for PHP 7. 
- Composer is required.
- Npm and Nodejs are required for tests.
- Mod rewrite must be enabled.
- Mysql is required. Currently only Mysql is supported.

### Installation
Please read the requirements section first as this section will direct you to use some of the pre-requisites.
1. Clone the directory using `git clone https://github.com/pete-mann/php-express.git`
2. Install dependencies with Composer, `cd php-express && composer install`
3. If you wish to using system tests, it is necessary to install the npm dependencies. From the project root `cd tests/system && npm install`

### About
While this is similar to Expressjs, this project does not attempt to do everything that Expressjs does. For example regex 
route matching is currently not supported, however named params are.

The project is designed for Apache with mod_rewrite support required. NGINX support is not included, but that could be 
added if required. 

### Composer
This project requires composer for autoloading and loading some dependencies such as:
- Firebase JWT
- Justin Rainbow json-schema

### Tests
Some tests have been written using PHPUnit, Mocha and Chai. The following testing:
- Performance testing will be written using Apache Bench (AB).
- System testing will be written using Mocha and Chai.
- Integration testing will be written using PHPUnit 7.
- Unit testing will be written using PHPUnit 7.

### Test Guides

#### Performance Test Guide
Performance testing can be executed by running the `tests/performance/performanceTest.sh` file.

#### System Test Guide
System testing can be conducted from the `tests/system` directory by issuing `npm test`. This will run every `*.test.js` 
file in the `tests/system` directory.

#### Integration Test Guide
Integration tests can be conducted using PHPUnit from the `tests/integration` directory.
 
#### Unit Test Guide
Unit testing can be conducted using PHPUnit from the `tests/unit` directory.


### TODO
- Include JSON schema validation of request body data.
- Write Swagger documentation including schemas for each endpoint. 
- Try to use Swagger schemas in testing and validation.

### Example usage
This example usage is included in the `/api/index.php` file and is presented here for the benfit of readers.
```
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
```