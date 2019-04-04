<?php

namespace ExpressPHP\core;

use ExpressPHP\middlewares\Middleware;

/**
 * Class App
 * The App class is used to start the API application, this class will load the environment file,
 * and instantiate the request and response classes. This class also sets up some of the error
 * handling methods used to catch unhandled errors and exceptions.
 * @author Pete Mann - peter.mann.design@gmail.com
 * @package ExpressPHP\core
 */
class App {

    private $env;

    private $req;

    private $res;

    private $middlewares = [];

    private $isAuthenticated = false;

    /**
     * The constructor function is used to start the application
     */
    public function __construct() {
        session_start(); # start a session
        ob_start(); # Use output buffering
        $this->res = new Response();
        try {
            $server = new Server(); # Init the Server class
            $this->req = $server->getRequest(); # Create a new Request
        } catch(\BadMethodCallException $e) {
            $this->res->send(400, [
                'title' => 'An error occurred',
                'message' => ($this->env['config']['mode'] === 'dev') ? $e->getMessage() : 'Please contact your system administrator'
            ]);
        }
        try {
            $this->loadEnv()
                 ->setDBEnv()
                 ->setEnvironmentErrorMode()
                 ->setCustomHeader();
        } catch(\Exception $e) {
            $this->exceptionHandler($e);
        }
        register_shutdown_function([$this, 'shutdownHandler']); # Handle shutdown
        set_exception_handler([$this, 'exceptionHandler']); # Handle uncaught exceptions
        set_error_handler([$this, 'errorHandler']); # Handle uncaught exceptions
    }

    /**
     * The loadEnv method is used to load the environment file into the application
     * @return App so that this method can be chained
     */
    private function loadEnv(): App {
        $env = json_decode(file_get_contents('./app/core/.env'), true);
        $this->setEnv($env);
        return $this;
    }

    /**
     * The setEnv method is used to set the environment variable stdClass object
     * @param array $env accepts a stdClass object that contains the environmental variables
     */
    private function setEnv($env) {
        $this->env = $env;
    }

    /**
     * The getEnv method is used to return the environmental object to the caller
     * @return array	object that contains the environmental variables
     */
    public function getEnv(): array {
        return $this->env;
    }

    /**
     * The setDBEnv method is used for setting the environmental variables in the DataBase class
     * @return App $this for method chaining
     */
    public function setDBEnv(): App {
        DataBase::setEnv($this->env['database'][$this->env['config']['mode']], $this->env['config']['mode']);
        return $this;
    }

    /**
     * The setEnvironmentErrorMode is used to set the mode of the application, error reporting and error
     * display should not be enabled in production. This setting can be toggled in the environmental
     * variables file
     * @return App $this for method chaining
     */
    private function setEnvironmentErrorMode(): App {
        $this->setErrorReporting($this->env['config']['reportError']);
        $this->setErrorDisplay($this->env['config']['displayError']);
        return $this;
    }

    /**
     * The setCustomHeader method is used to set a custom header for each response
     * @return App $this for method chaining
     */
    private function setCustomHeader(): App {
        header('X-Powered-By: php-express');
        return $this;
    }

    /**
     * The setErrorReporting method is used to set the mode of error reporting, this method accepts an int value
     * that is used to set the error reporting for the application. Note that 0 turns error_reporting off.
     * @param string $erroReportSetting accepts a string that is used to set the error_reporting value, the value is
     * converted to an int by accessing the bitmask assigned to the constant of the error setting used
     */
    private function setErrorReporting($erroReportSetting) {
        error_reporting($erroReportSetting == 0 ? 0 : constant($erroReportSetting));
    }

    /**
     * The getErrorReporting method is used to return the value that is assigned to the error_reporting method
     * @return bool used to determine the error reporting mode
     */
    public function getErrorReporting(): bool {
        return $this->env['config']['reportError'];
    }

    /**
     * The setErrorDisplay method is used to set the errors output of the application, specifically if errors should
     * be printed to the screen and visible to users. This should be disabled in production for obvious reasons
     * @param bool $isDisplayError [description]
     */
    private function setErrorDisplay($isDisplayError) {
        ini_set('log_errors', true);
        ini_set('display_errors', $isDisplayError);
        ini_set('display_startup_errors', $isDisplayError);
    }

    /**
     * The getErrorDisplay method is used to return the value that is assigned to the display_errors and display_startup_errors
     * @return boolean used to determine the error display mode
     */
    public function getErrorDisplay(): bool {
        return $this->env['config']['displayError'];
    }

    /**
     * The shutdownHandler is used to clean up the application before the application closes
     */
    public function shutdownHandler() {
        DataBase::destroyConnection();
    }

    /**
     * The exceptionHandler method is used to catch any uncaught Throwable objects. This method makes sure that application errors are not
     * sent to the output buffer and to the client. This is important in production as errors can contain sensitive data
     * @param  \Exception $e accepts objects that implement the Throwable interface
     */
    public function exceptionHandler($e) {
        http_response_code(500);
        $userError = [
            'title' => 'Something exceptionally bad happened',
            'message' => 'A server error occurred, but dont worry because somewhere, some time, a caffeine fueled developer will be fixing it real good',
            'error' => $this->env['config']['mode'] === 'dev' ? $e->getMessage() : 'Please contact the administrator'
        ];
        if($this->env['config']['mode'] === 'dev') $userError['stackTrace'] = $e->getTraceAsString();
        $this->res->json($userError);
    }

    /**
     * The errorHandler method is used to catch any uncaught Errors. This method makes sure that application errors are not
     * sent to the output buffer and to the client. This is important in production as errors can contain sensetive data
     * @param  int    $errno  accepts the error number
     * @param  string $errstr accepts the error string
     */
    public function errorHandler($errno, $errstr) {
        http_response_code(500);
        $out = [
            'title' => 'Its bad, really bad',
            'message' => 'A server error occurred but fear not a super human programmer will fix this',
            'error' => $this->env['config']['mode'] === 'dev' ? $errstr : 'Please contact the administrator'
        ];
        if($this->env['config']['mode'] === 'dev') {
            $out['errorNumber'] = $errno;
            $out['stackTrace'] = debug_backtrace();
        }
        $this->res->json($out);
    }

    /**
     * The setIsAuthenticated method is used to set the authenticity of the connecting user, this is used to protect
     * endpoints from unwanted public access.
     * @param $isAuthenticated
     */
    public function setIsAuthenticated($isAuthenticated) {
        $this->isAuthenticated = $isAuthenticated;
    }

    /**
     * The middleware method is used to register a Middleware class type to the specified endpoint. The Middleware class
     * will be called before the request controller logic is executed.
     * @param Middleware ...$middlewares
     * @return App
     */
    public function middleware(Middleware ...$middlewares): App {
        $this->middlewares = $middlewares;
        return $this;
    }

    /**
     * The resetMiddlewares is used to reset the middlewares array for the next endpoint to be evaluated.
     */
    private function resetMiddlewares() {
        $this->middlewares = [];
    }

    /**
     * The registerEndpoint method is used to register every endpoint on the API. 
     * @param array $endPoint [accepts an array that is used to define the properties of the endpoint]
     */
    private function registerEndpoint($endPoint) {
        $route = ['path' => null, 'isSecure' => false, 'handler' => null];
        foreach($endPoint['args'] as $arg) {
            if(is_string($arg)) $route['path'] = $arg;
            if(is_bool($arg)) $route['isSecure'] = $arg;
            if(is_callable($arg)) $route['handler'] = $arg;
        }
        $this->evaluateRequest($route['path'], $endPoint['method'], $route['isSecure'], $this->isAuthenticated, $route['handler']);
    }

    /**
     * The evaluateRequest method is called by each user defined options method, the user defined options method will be evaluated against
     * the current Request path to determine if it matches the path and therefore should that methed execute or not.
     * @param string $path [Is the path of the defined request]
     * @param $method [Is the type of method of the defined request]
     * @param boolean $isSecure [Is a boolean to determine if the route requires auth]
     * @param boolean $isAuthenticated [Is a boolean to determine if the connecting user has authenticated]
     * @param callable $callback [Is a callable method that is called, the method contains the endpoint]
     */
    private function evaluateRequest($path, $method, $isSecure, $isAuthenticated, $callback) {
        if(strtoupper($this->req->getMethod()) === strtoupper($method)) {
            if($this->req->matchRoutePath($path)) {
                if($isSecure == true && $isAuthenticated == false) $this->res->failedAuthentication($path);
                foreach($this->middlewares as $middleware) {
                    $middleware->handle($this->req, $this->res);
                }
                $this->resetMiddlewares();
                $callback($this->req, $this->res);
            }
        }
        $this->resetMiddlewares();
    }

    /**
     * The get method is used to register an api endpoint on the application.
     * @param array $args [accepts an array that is used to define the properties of the endpoint]
     */
    public function get(...$args) {
        $this->registerEndpoint([
            'args' => $args,
            'method' => 'get'
        ]);
    }

    /**
     * The put method is used to register an api endpoint on the application.
     * @param array $args [accepts an array that is used to define the properties of the endpoint]
     */
    public function put(...$args) {
        $this->registerEndpoint([
            'args' => $args,
            'method' => 'put'
        ]);
    }

    /**
     * The post method is used to register an api endpoint on the application.
     * @param array $args [accepts an array that is used to define the properties of the endpoint]
     */
    public function post(...$args) {
        $this->registerEndpoint([
            'args' => $args,
            'method' => 'post'
        ]);
    }

    /**
     * The patch method is used to register an api endpoint on the application.
     * @param array $args [accepts an array that is used to define the properties of the endpoint]
     */
    public function patch(...$args) {
        $this->registerEndpoint([
            'args' => $args,
            'method' => 'patch'
        ]);
    }

    /**
     * The delete method is used to register an api endpoint on the application.
     * @param array $args [accepts an array that is used to define the properties of the endpoint]
     */
    public function delete(...$args) {
        $this->registerEndpoint([
            'args' => $args,
            'method' => 'delete'
        ]);
    }

    /**
     * The options method is used to register an api endpoint on the application.
     * @param array $args [accepts an array that is used to define the properties of the endpoint]
     */
    public function options(...$args) {
        $this->registerEndpoint([
            'args' => $args,
            'method' => 'options'
        ]);
    }

    /**
     * The otherwise method is used to handle un-matched routes
     * @param callable $handler [is a callable reference that will be called if no matching route is found]
     */
    public function otherwise(callable $handler) {
        $handler($this->req, $this->res);
    }

}
