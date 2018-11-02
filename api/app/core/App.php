<?php

require_once './app/core/Router.php';
require_once './app/core/Request.php';
require_once './app/core/Response.php';

/**
 * The App class is used to start the API application, this class will load the environment file,
 * create a router, request and response classes. This class also sets up some of the error
 * handling methods used to catch unhandled errors and exceptions.
 * @author Pete Mann - peter.mann.design@gmail.com
 */
class App {

	private $env;

	private $req;

	private $res;

	private $router;

	/**
	 * The constructor function is used to start the application
	 */
	public function __construct() {
	    session_start(); # start a session
        ob_start(); # Use output buffering
        $this->res = new Response();
		$this->req = new Request();
		$this->router = new Router($this->req, $this->res);
		try {
			$this->loadEnv()
                 ->setDBEnv()
                 ->setEnvironmentErrorMode();
		} catch(Exception $e) {
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
	private function loadEnv() {
		$env = json_decode(file_get_contents('./app/core/.env'), true);
		$this->setEnv($env);
		return $this;
	}

	/**
	 * The setEnv method is used to set the environment variable stdClass object
	 * @param stdClass $env accepts a stdClass object that contains the environmental variables
	 */
	private function setEnv($env) {
		$this->env = $env;
	}

	/**
	 * The getEnv method is used to return the environmental object to the caller
	 * @return stdClass	object that contains the environmental variables
	 */
	public function getEnv() {
		return $this->env;
	}

    /**
     * The setDBEnv method is used for setting the environmental variables in the DataBase class
     * @return App $this for method chaining
     */
    public function setDBEnv() {
        DataBase::setEnv($this->env['database'][$this->env['config']['mode']], $this->env['config']['mode']);
        return $this;
    }

	/**
	 * The setEnvironmentErrorMode is used to set the mode of the application, error reporting and error
	 * display should not be enabled in production. This setting can be toggled in the environmental
	 * variables file
	 */
	private function setEnvironmentErrorMode() {
		$this->setErrorReporting($this->env['config']['reportError']);
		$this->setErrorDisplay($this->env['config']['displayError']);
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
	 * @return boolean used to determine the error reporting mode
	 */
	public function getErrorReporting() {
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
	public function getErrorDisplay() {
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
	 * @param  Throwable $e accepts objects that implement the Throwable interface
	 */
	public function exceptionHandler($e) {
        http_response_code(500);
		$userError = [
		    'title' => 'Something exceptionally bad happened',
			'message' => 'A server error occurred, but dont worry because somewhere, some time a caffeine fueled developer will be fixing it real good',
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
	 * The get method is used to register an api endpoint on the application, these registrations are forwarded to the router.
	 * @param  string   $path    accepts a string that is used to determine the api route and endpoint
	 * @param  callable $handler accepts a callable method that is invoked if the endpoint is considered a match
	 */
	public function get($path, $handler) {
		$this->router->get($path, $handler);
	}

	/**
	 * The put method is used to register an api endpoint on the application, these registrations are forwarded to the router.
	 * @param  string   $path    accepts a string that is used to determine the api route and endpoint
	 * @param  callable $handler accepts a callable method that is invoked if the endpoint is considered a match
	 */
	public function put($path, $handler) {
		$this->router->put($path, $handler);
	}

	/**
	 * The post method is used to register an api endpoint on the application, these registrations are forwarded to the router.
	 * @param  string   $path    accepts a string that is used to determine the api route and endpoint
	 * @param  callable $handler accepts a callable method that is invoked if the endpoint is considered a match
	 */
	public function post($path, $handler) {
		$this->router->post($path, $handler);
	}

	/**
	 * The patch method is used to register an api endpoint on the application, these registrations are forwarded to the router.
	 * @param  string   $path    accepts a string that is used to determine the api route and endpoint
	 * @param  callable $handler accepts a callable method that is invoked if the endpoint is considered a match
	 */
	public function patch($path, $handler) {
		$this->router->patch($path, $handler);
	}

	/**
	 * The delete method is used to register an api endpoint on the application, these registrations are forwarded to the router.
	 * @param  string   $path    accepts a string that is used to determine the api route and endpoint
	 * @param  callable $handler accepts a callable method that is invoked if the endpoint is considered a match
	 */
	public function delete($path, $handler) {
		$this->router->delete($path, $handler);
	}

	/**
	 * The options method is used to register an api endpoint on the application, these registrations are forwarded to the router.
	 * @param  string   $path    accepts a string that is used to determine the api route and endpoint
	 * @param  callable $handler accepts a callable method that is invoked if the endpoint is considered a match
	 */
	public function options($path, $handler) {
		$this->router->options($path, $handler);
	}

    /**
     * The otherwise method is used to handle un-matched routes
     * @param $handler is a callable reference that will be called if no matching route is found
     */
	public function otherwise(callable $handler) {
	    $handler($this->req, $this->res);
    }

    public function getResponse() {
	    return $this->res;
    }

}
