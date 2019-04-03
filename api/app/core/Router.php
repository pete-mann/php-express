<?php

namespace ExpressPHP\core;

/**
 * Class Router
 * The Router class is used to take a user defined route and match it to the appropriate request
 * @author Pete Mann - peter.mann.design@gmail.com
 * @package ExpressPHP\core
 */
class Router {

    private $req;

    private $res;

    /**
     * Router constructor.
     * @param $request
     * @param $response
     */
    public function __construct(Request $request, Response $response) {
        $this->req = $request;
        $this->res = $response;
    }

    /**
     * The get method is called by each user defined get method, the user defined get method will be evaluated against
     * the current Request path to determine if it matches the path and therefore should that method execute or not.
     * @param string $path [Is the path of the defined request]
     * @param boolean $isSecure [Is a boolean to determine if the route requires auth]
     * @param boolean $isAuthenticated [Is a boolean to determine if the connecting user has authenticated]
     * @param callable $callback [Is a callable method that is called, the method contains the endpoint]
     */
    public function get($path, $isSecure, $isAuthenticated, $callback) {
        $this->evaluateRequest($path, "GET", $isSecure, $isAuthenticated, $callback);
    }

    /**
     * The put method is called by each user defined put method, the user defined put method will be evaluated against
     * the current Request path to determine if it matches the path and therefore should that method execute or not.
     * @param string $path [Is the path of the defined request]
     * @param boolean $isSecure [Is a boolean to determine if the route requires auth]
     * @param boolean $isAuthenticated [Is a boolean to determine if the connecting user has authenticated]
     * @param callable $callback [Is a callable method that is called, the method contains the endpoint]
     */
    public function put($path, $isSecure, $isAuthenticated, $callback) {
        $this->evaluateRequest($path, "PUT", $isSecure, $isAuthenticated, $callback);
    }

    /**
     * The post method is called by each user defined post method, the user defined post method will be evaluated against
     * the current Request path to determine if it matches the path and therefore should that method execute or not.
     * @param string $path [Is the path of the defined request]
     * @param boolean $isSecure [Is a boolean to determine if the route requires auth]
     * @param boolean $isAuthenticated [Is a boolean to determine if the connecting user has authenticated]
     * @param callable $callback [Is a callable method that is called, the method contains the endpoint]
     */
    public function post($path, $isSecure, $isAuthenticated, $callback) {
        $this->evaluateRequest($path, "POST", $isSecure, $isAuthenticated, $callback);
    }

    /**
     * The patch method is called by each user defined path method, the user defined patch method will be evaluated against
     * the current Request path to determine if it matches the path and therefore should that method execute or not.
     * @param string $path [Is the path of the defined request]
     * @param boolean $isSecure [Is a boolean to determine if the route requires auth]
     * @param boolean $isAuthenticated [Is a boolean to determine if the connecting user has authenticated]
     * @param callable $callback [Is a callable method that is called, the method contains the endpoint]
     */
    public function patch($path, $isSecure, $isAuthenticated, $callback) {
        $this->evaluateRequest($path, "PATCH", $isSecure, $isAuthenticated, $callback);
    }

    /**
     * The delete method is called by each user defined delete method, the user defined delete method will be evaluated against
     * the current Request path to determine if it matches the path and therefore should that method execute or not.
     * @param string $path [Is the path of the defined request]
     * @param boolean $isSecure [Is a boolean to determine if the route requires auth]
     * @param boolean $isAuthenticated [Is a boolean to determine if the connecting user has authenticated]
     * @param callable $callback [Is a callable method that is called, the method contains the endpoint]
     */
    public function delete($path, $isSecure, $isAuthenticated, $callback) {
        $this->evaluateRequest($path, "DELETE", $isSecure, $isAuthenticated, $callback);
    }

    /**
     * The options method is called by each user defined options method, the user defined options method will be evaluated against
     * the current Request path to determine if it matches the path and therefore should that method execute or not.
     * @param string $path [Is the path of the defined request]
     * @param boolean $isSecure [Is a boolean to determine if the route requires auth]
     * @param boolean $isAuthenticated [Is a boolean to determine if the connecting user has authenticated]
     * @param callable $callback [Is a callable method that is called, the method contains the endpoint]
     */
    public function options($path, $isSecure, $isAuthenticated, $callback) {
        $this->evaluateRequest($path, "OPTIONS", $isSecure, $isAuthenticated, $callback);
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
                $isComplete = $callback($this->req, $this->res);
                if($isComplete != null) $this->res = $isComplete;
            }
        }
    }

}
