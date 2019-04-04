<?php

namespace ExpressPHP\core;

/**
 * Class Request
 * The Request class is used to decode a request into an object. The object can then be used
 * by the application or developer. This class is a singleton, because there is only one client request
 * @author Pete Mann - peter.mann.design@gmail.com
 * @package ExpressPHP\core
 */
class Request {

    private $originalUrl = '';

    private $method = '';

    private $path = '';

    private $params = [];

    private $query = [];

    private $body = [];

    private $files = [];

    private $cookie = [];

    private $headers = [];

    private $protocol = '';

    public function __construct($originalUrl,
                                $method,
                                $path,
                                $query,
                                $body,
                                $files,
                                $cookie,
                                $headers,
                                $protocol) {
        $this->originalUrl = $originalUrl;
        $this->method = $method;
        $this->path = $path;
        $this->query = $query;
        $this->body = $body;
        $this->files = $files;
        $this->cookie = $cookie;
        $this->headers = $headers;
        $this->protocol = $protocol;
    }

    /**
     * The test method is used to return the request object for the purpose of debugging
     * @return array
     */
    public function test() {
        return [
            "originalUrl" => $this->getOriginalUrl(),
            "method" => $this->getMethod(),
            "path" => $this->getPath(),
            "protocol" => $this->getProtocol(),
            "params" => $this->getParams(),
            "query" => $this->getQuery(),
            "body" => $this->getBody(),
            "files" => $this->getFiles(),
            "cookie" => $this->getCookie(),
            "headers" => $this->getHeaders()
        ];
    }

    /**
     * The getOriginalUrl method is used to return the original URL
     * @return string of the originional URL
     */
    public function getOriginalUrl() {
        return $this->originalUrl;
    }

    /**
     * The getMethod method is used to return the method that was used to call the API
     * @return mixed
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * The getPath method is used to return the path that is part of the URL that was used to call this API
     * @return mixed
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * The getParams method is used to return any named params associated to this request if there are any
     * @return mixed
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * The setParams method is used to set the named params of this request if there are any
     * @param $params
     */
    private function setParams($params) {
        $this->params = $params;
    }

    /**
     * The getQuery method is used to return the query object
     * @return mixed
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * The getBody method is used to return the parsed body object
     * @return mixed
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * The getFiles method is used to return the files that are sent with the request
     * @return array of files sent with the request
     */
    public function getFiles() {
        return $this->files;
    }

    /**
     * The getCookie method is used to return the Request cookie
     * @return mixed
     */
    public function getCookie() {
        return $this->cookie;
    }

    /**
     * The getHeaders method is used to return the headers object
     * @return mixed
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * The getProtocol method is used to return the protocol used to call the api
     * @return string representing the protocol used to call the api, results are HTTP or HTTPS
     */
    public function getProtocol() {
        return $this->protocol;
    }

    /**
     * The matchRoutePath method is used to determine if the specified route path matches the path that was used to call
     * the API. Currently path matching is a simple equality operation, with the addition of named parameters e.g
     * /users/:id will
     * @param string $routePath
     * @return bool
     */
    public function matchRoutePath($routePath) {
        $params = [];

        $pathPieces = explode('/', substr($this->getPath(), 1));

        $routePathPieces = explode('/', substr($routePath, 1));

        $isMatch = true;

        if(count($routePathPieces) != count($pathPieces)) {
            $isMatch = false;
        } else {
            for($i = 0; $i < count($routePathPieces); $i++) {
                if(substr($routePathPieces[$i], 0, 1) == ':') {
                    $params[substr($routePathPieces[$i], 1)] = $pathPieces[$i];
                } else if($routePathPieces[$i] != $pathPieces[$i]) {
                    $isMatch = false;
                }
            }
        }

        if($isMatch == true) $this->setParams($params);
        return $isMatch;

        //    $this->setParams($this->parseNamedParams($path));
        //    return $this->path === $path || count($this->getParams()) > 0;
    }

//    /**
//     * The parseNamedParams method is used to parse any named params in the user defined path
//     * @param $path
//     * @return array
//     * @throws Exception
//     */
//    private function parseNamedParams($path) {
//        $patternAsRegex = $this->findRegex($path);
//        preg_match($patternAsRegex, $this->path, $matches);
//        return array_intersect_key(
//            $matches,
//            array_flip(array_filter(array_keys($matches), 'is_string'))
//        );
//    }
//
//    /**
//     * method is unused
//     * @param $pattern
//     * @return string
//     * @throws \Exception
//     */
//    private function findRegex($pattern) {
//        if(preg_match('/[^-:\/_{}()a-zA-Z\d]/', $pattern)) throw new \Exception('Invalid pattern');
//
//        // Turn "(/)" into "/?"
//        $pattern = preg_replace('#\(/\)#', '/?', $pattern);
//
//        // Create capture group for ":parameter"
//        $allowedParamChars = '[a-zA-Z0-9\_\-]+';
//        $pattern = preg_replace(
//            '/:(' . $allowedParamChars . ')/',   # Replace ":parameter"
//            '(?<$1>' . $allowedParamChars . ')', # with "(?<parameter>[a-zA-Z0-9\_\-]+)"
//            $pattern
//        );
//
//        // Create capture group for '{parameter}'
//        $pattern = preg_replace(
//            '/{('. $allowedParamChars .')}/',    # Replace "{parameter}"
//            '(?<$1>' . $allowedParamChars . ')', # with "(?<parameter>[a-zA-Z0-9\_\-]+)"
//            $pattern
//        );
//
//        // Add start and end matching
//        $patternAsRegex = "@^" . $pattern . "$@D";
//
//        return $patternAsRegex;
//    }

}
