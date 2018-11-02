<?php

/**
 * The Router class is used to take a user defined route and match it to the appropriate request
 * @author Pete Mann - peter.mann.design@gmail.com
 */
class Router {

    private $req; # singleton

    private $res; # singleton

    /**
     * Router constructor.
     * @param $request
     * @param $response
     */
    public function __construct($request, $response) {
        $this->req = $request;
        $this->res = $response;
    }

    /**
     * The get method is called by each user defined get method, the user defined get method will be evaluated against
     * the current Request path to determine if it matches the path and therefore should that methoed execute or not.
     * @param $path
     * @param $callback
     */
    public function get($path, $callback) {
        if($this->req->getMethod() === "GET") {
            if($this->req->matchRoutePath($path)) {
                $isComplete = $callback($this->req, $this->res);
                if($isComplete != null) $this->res = $isComplete;
            }
        }
    }

    /**
     * The put method is called by each user defined put method, the user defined put method will be evaluated against
     * the current Request path to determine if it matches the path and therefore should that methoed execute or not.
     * @param $path
     * @param $callback
     */
    public function put($path, $callback) {
        if($this->req->getMethod() === "PUT") {
            if($this->req->matchRoutePath($path)) {
                $isComplete = $callback($this->req, $this->res);
                if($isComplete != null) $this->res = $isComplete;
            }
        }
    }

    /**
     * The post method is called by each user defined post method, the user defined post method will be evaluated against
     * the current Request path to determine if it matches the path and therefore should that methoed execute or not.
     * @param $path
     * @param $callback
     */
    public function post($path, $callback) {
        if($this->req->getMethod() === "POST") {
            if($this->req->matchRoutePath($path)) {
                $isComplete = $callback($this->req, $this->res);
                if($isComplete != null) $this->res = $isComplete;
            }
        }
    }

    /**
     * The patch method is called by each user defined path method, the user defined patch method will be evaluated against
     * the current Request path to determine if it matches the path and therefore should that methoed execute or not.
     * @param $path
     * @param $callback
     */
    public function patch($path, $callback) {
        if($this->req->getMethod() === "PATCH") {
            if($this->req->matchRoutePath($path)) {
                $isComplete = $callback($this->req, $this->res);
                if($isComplete != null) $this->res = $isComplete;
            }
        }
    }

    /**
     * The delete method is called by each user defined delete method, the user defined delete method will be evaluated against
     * the current Request path to determine if it matches the path and therefore should that methoed execute or not.
     * @param $path
     * @param $callback
     */
    public function delete($path, $callback) {
        if($this->req->getMethod() === "DELETE") {
            if($this->req->matchRoutePath($path)) {
                $isComplete = $callback($this->req, $this->res);
                if($isComplete != null) $this->res = $isComplete;
            }
        }
    }

    /**
     * The options method is called by each user defined options method, the user defined options method will be evaluated against
     * the current Request path to determine if it matches the path and therefore should that methoed execute or not.
     * @param $path
     * @param $callback
     */
    public function options($path, $callback) {
        if($this->req->getMethod() === "OPTIONS") {
            if($this->req->matchRoutePath($path)) {
                $isComplete = $callback($this->req, $this->res);
                if($isComplete != null) $this->res = $isComplete;
            }
        }
    }

}
