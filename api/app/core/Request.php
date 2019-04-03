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

  private $originalUrl;

  private $method;

  private $path;

  private $params;

  private $query;

  private $body;

  private $files;

  private $cookie;

  private $headers;

  private $protocol;

    /**
     * Request constructor is used to initialise the Request object, this includes converting the request into an object
     * for the program to use
     * @throws \Exception
     */
    public function __construct() {
    $this->compileOriginalUrl()
         ->compileMethod()
         ->compilePath()
         ->compileParams()
         ->compileQuery()
         ->compileBody()
         ->compileFiles()
         ->compileCookie()
         ->compileHeaders()
         ->compileProtocol();
    }

    /**
     * The test method is used to return the request object for the purpose of debugging
     * @return array
     */
    public function test() {
        return [
            "originalUrl" => $this->originalUrl,
            "method" => $this->method,
            "path" => $this->path,
            "protocol" => $this->protocol,
            "params" => $this->params,
            "query" => $this->query,
            "body" => $this->body,
            "files" => $this->files,
            "cookie" => $this->cookie,
            "headers" => $this->headers
        ];
    }

    /**
     * The compileOriginalUrl method is used to compile the original URI
     * @return $this
     */
    private function compileOriginalUrl() {
        $this->setOriginalUrl($_SERVER['REQUEST_URI']);
        return $this;
    }

    /**
     * The setOriginalUrl method is used to set the originional URL for the API
     * @param $originalUrl
     */
    private function setOriginalUrl($originalUrl) {
        $this->originalUrl = $originalUrl;
    }

    /**
     * The getOriginalUrl method is used to return the original URL
     * @return string of the originional URL
     */
    public function getOriginalUrl() {
        return $this->originalUrl;
    }

    /**
     * The compileMethod method is used to extract the method used to call the API and store the method as part of the
     * Request object
     * @return $this
     * @throws \Exception
     */
    private function compileMethod() {
        $method = $_SERVER["REQUEST_METHOD"];

        if($method == "POST" && array_key_exists("HTTP_X_HTTP_METHOD", $_SERVER)) {
            if($_SERVER["HTTP_X_HTTP_METHOD"] == "DELETE") {
                $method = "DELETE";
            } else if($_SERVER["HTTP_X_HTTP_METHOD"] == "PUT") {
                $method = "PUT";
            } else {
                throw new \Exception('Method not supported');
            }
        }
        $this->setMethod($method);
        return $this;
    }

    /**
     * The setMethod is used to set the method used to call the API
     * @param $method
     */
    private function setMethod($method) {
        $this->method = strtoupper($method);
    }

    /**
     * The getMethod method is used to return the method that was used to call the API
     * @return mixed
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * The compilePath method is used to compile the path that is part of the URL that was used to call the API
     * @return $this
     */
    private function compilePath() {
        $this->setPath('/' . $_GET['path']);
        return $this;
    }

    /**
     * The setPath method is used to set the path that is part of the URL that was used to call this API
     * @param $path
     */
    private function setPath($path) {
        $this->path = $path;
    }

    /**
     * The getPath method is used to return the path that is part of the URL that was used to call this API
     * @return mixed
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * The compileParams method is used to compile the named params if there are any
     * @return $this
     */
    private function compileParams() {
        $this->setParams([]);
        return $this;
    }

    /**
     * The setParams method is used to set the named params of this request if there are any
     * @param $params
     */
    private function setParams($params) {
        $this->params = $params;
    }

    /**
     * The getParams method is used to return any named params associated to this request if there are any
     * @return mixed
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * The compileQuery method is used to compile the query string into an object if there are any queries in the string.
     * This method also extends the url, by allowing serialised arrays to be passed in the form of [1,2,3,4]
     * @return Request
     */
    private function compileQuery() {
        $parsedURL = parse_url($this->originalUrl);
        $queryString = array_key_exists('query', $parsedURL) ? $parsedURL['query'] : '';
        $queries = [];
        if($queryString) parse_str($queryString, $queries);
        foreach($queries as $key => $query) {
            if(is_string($query)) {
                if($query[0] == '[' && substr($query, - 1) == ']') {
                    $queries[$key] = explode(',', substr($query, 1, (strlen($query) - 2)));
                }
            }
        }
        $this->setQuery($queries);
        return $this;
    }

    /**
     * The setQuery method is used to set the query object as a property of the Request object
     * @param $query
     */
    private function setQuery($query) {
        $this->query = $query;
    }

    /**
     * The getQuery method is used to return the query object
     * @return mixed
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * The compileBody method is used to find the JSON data within the message body if it exists, then to parse it
     * @return $this
     */
    private function compileBody() {
        $body = array();
        if($this->method == 'POST' || $this->method == 'PUT') {
            $input = file_get_contents('php://input');
            if($input) $body = json_decode($input, true);
            # Add any data in $_POST
            foreach($_POST as $key => $value) {
                $body[$key] = $value;
            }
        }
        $this->setBody($body);
        return $this;
    }

    /**
     * The setBody method is used to set the body object as a parameter of the Request object
     * @param $body
     */
    private function setBody($body) {
        $this->body = $body;
    }

    /**
     * The getBody method is used to return the parsed body object
     * @return mixed
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * The compileFiles method is used to find all files in the request
     * @return $this
     */
    private function compileFiles() {
        $files = [];
        foreach($_FILES as $key => $value) {
            $files[$key] = $value;
        }
        $this->setFiles($files);
        return $this;
    }

    /**
     * The setFiles method is used to set all files found in the request to the Request object
     * @param $files
     */
    private function setFiles($files) {
        $this->files = $files;
    }

    /**
     * The getFiles method is used to return the files that are sent with the request
     * @return array of files sent with the request
     */
    public function getFiles() {
        return $this->files;
    }

    /**
     * The compileCookie method is used to take the cookie object and store a reference to it within this class
     * @return $this
     */
    private function compileCookie() {
        $this->setCookie($_COOKIE);
        return $this;
    }

    /**
     * The setCookie method is used to replace the Request cookie
     * @param $cookie
     */
    private function setCookie($cookie) {
        $this->cookie = $cookie;
    }

    /**
     * The getCookie method is used to return the Request cookie
     * @return mixed
     */
    public function getCookie() {
        return $this->cookie;
    }

    /**
     * The compileHeaders method is used to find the Request headers
     * @return $this
     */
    private function compileHeaders() {
        $headersOriginal = apache_request_headers();
        $headers = [];
        foreach($headersOriginal as $key => $val) {
            $headers[strtolower($key)] = $val;
        }
        if(array_key_exists('accept-encoding', $headers)) {
            if(stripos($headers['accept-encoding'], 'gzip') !== false) {
                ob_start('ob_gzhandler'); # Use gzip to reduce response sizes
            }
        }
        $this->setHeaders($headers);
        return $this;
    }

    /**
     * The setHeaders method is used to replace the Request headers
     * @param $headers
     */
    private function setHeaders($headers) {
        $this->headers = $headers;
    }

    /**
     * The getHeaders method is used to return the headers object
     * @return mixed
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * The compileProtocol method is used to find the protocol used to call the api, options should be one of either
     * HTTP or HTTPS
     */
    private function compileProtocol() {
        $this->setProtocol(isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : 'HTTP');
    }

    /**
     * The setProtocol method is used to set the protocol used to call the api
     * @param $protocol string should be either HTTP or HTTPS
     */
    private function setProtocol($protocol) {
        $this->protocol = strtoupper($protocol);
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

        $pathPieces = explode('/', substr($this->path, 1));

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
