<?php

namespace ExpressPHP\core;

/**
 * Class Server
 * The server class is used to decouple the rest of the program from the server. This class is responsible for handling
 * the values of superglobals $SERVER, $_POST, $_GET, $_COOKIE as well as storing an array of apache request headers.
 * Once this class is instantiated it should remain immutable.
 * @author Pete Mann - peter.mann.design@gmail.com
 * @package ExpressPHP\core
 */
class Server {

    private $originalUrl = '';

    private $method = '';

    private $path = '';

    private $query = [];

    private $body = [];

    private $files = [];

    private $cookie = [];

    private $headers = [];

    private $protocol = '';

    public function __construct(string $url = null,
                                string $method = null,
                                string $path = null,
                                array $body = null,
                                array $files = null,
                                array $cookie = null,
                                array $headers = null,
                                string $protocol = null) {

        $this->compileOriginalUrl($url)
             ->compileMethod($method)
             ->compilePath($path)
             ->compileQuery()
             ->compileBody($body)
             ->compileFiles($files)
             ->compileCookie($cookie)
             ->compileHeaders($headers)
             ->compileProtocol($protocol);
    }

    /**
     * The compileOriginalUrl method is used to compile the original URI
     * @param string|null $url
     * @return Server
     */
    private function compileOriginalUrl(string $url = null): Server {
        $url = is_string($url) ? $url : $_SERVER['REQUEST_URI'];
        $this->setOriginalUrl($url);
        return $this;
    }

    /**
     * The setOriginalUrl method is used to set the originional URL for the API
     * @param $originalUrl
     */
    private function setOriginalUrl(string $originalUrl) {
        $this->originalUrl = $originalUrl;
    }

    /**
     * The getOriginalUrl method is used to return the original URL
     * @return string of the originional URL
     */
    public function getOriginalUrl(): string {
        return $this->originalUrl;
    }

    /**
     * The compileMethod method is used to extract the method used to call the API and store the method as part of the
     * Request object
     * @return $this
     * @throws \BadMethodCallException
     */
    private function compileMethod(string $method = null): Server {
        $method = is_string($method) ? $method : $_SERVER["REQUEST_METHOD"];

        if($method == "POST" && array_key_exists("HTTP_X_HTTP_METHOD", $_SERVER)) {
            if($_SERVER["HTTP_X_HTTP_METHOD"] == "DELETE") {
                $method = "DELETE";
            } else if($_SERVER["HTTP_X_HTTP_METHOD"] == "PUT") {
                $method = "PUT";
            } else {
                throw new \BadMethodCallException('Method not supported');
            }
        }
        $this->setMethod($method);
        return $this;
    }

    /**
     * The setMethod is used to set the method used to call the API
     * @param $method
     */
    private function setMethod(string $method) {
        $this->method = strtoupper($method);
    }

    /**
     * The getMethod method is used to return the method that was used to call the API
     * @return mixed
     */
    public function getMethod(): string {
        return $this->method;
    }

    /**
     * The compilePath method is used to compile the path that is part of the URL that was used to call the API
     * @param string|null $path
     * @return Server
     */
    private function compilePath(string $path = null): Server {
        $this->setPath(is_string($path) ? $path : "/{$_GET['path']}");
        return $this;
    }

    /**
     * The setPath method is used to set the path that is part of the URL that was used to call this API
     * @param $path
     */
    private function setPath(string $path) {
        $this->path = $path;
    }

    /**
     * The getPath method is used to return the path that is part of the URL that was used to call this API
     * @return mixed
     */
    public function getPath(): string {
        return $this->path;
    }

    /**
     * The compileQuery method is used to compile the query string into an object if there are any queries in the string.
     * This method also extends the url, by allowing serialised arrays to be passed in the form of [1,2,3,4]
     * @return Server
     */
    private function compileQuery(): Server {
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
    private function setQuery(array $query) {
        $this->query = $query;
    }

    /**
     * The getQuery method is used to return the query object
     * @return mixed
     */
    public function getQuery(): array {
        return $this->query;
    }

    /**
     * The compileBody method is used to find the JSON data within the message body if it exists, then to parse it
     * @param array|null $body
     * @return Server
     */
    private function compileBody(array $body = null): Server {
        $body = is_array($body) ? $body : [];
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
    private function setBody(array $body) {
        $this->body = $body;
    }

    /**
     * The getBody method is used to return the parsed body object
     * @return mixed
     */
    public function getBody(): array {
        return $this->body;
    }

    /**
     * The compileFiles method is used to find all files in the request
     * @param array|null $files
     * @return Server
     */
    private function compileFiles(array $files = null): Server {
        $files = is_array($files) ? $files : [];
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
    private function setFiles(array $files) {
        $this->files = $files;
    }

    /**
     * The getFiles method is used to return the files that are sent with the request
     * @return array of files sent with the request
     */
    public function getFiles(): array {
        return $this->files;
    }

    /**
     * The compileCookie method is used to take the cookie object and store a reference to it within this class
     * @param array|null $cookie
     * @return Server
     */
    private function compileCookie(array $cookie = null): Server {
        $this->setCookie(is_array($cookie) ? $cookie : $_COOKIE);
        return $this;
    }

    /**
     * The setCookie method is used to replace the Request cookie
     * @param array $cookie
     */
    private function setCookie(array $cookie) {
        $this->cookie = $cookie;
    }

    /**
     * The getCookie method is used to return the Request cookie
     * @return array [An array of cookie key value pairs]
     */
    public function getCookie():array {
        return $this->cookie;
    }

    /**
     * The compileHeaders method is used to find the Request headers
     * @param array|null $headers
     * @return Server
     */
    private function compileHeaders(array $headers = null): Server {
        $headersOriginal = is_array($headers) ? $headers : apache_request_headers();
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
     * @param array $headers
     */
    private function setHeaders(array $headers) {
        $this->headers = $headers;
    }

    /**
     * The getHeaders method is used to return the headers object
     * @return array [An array of header key value pairs]
     */
    public function getHeaders():array {
        return $this->headers;
    }

    /**
     * The compileProtocol method is used to find the protocol used to call the api, options should be one of either
     * HTTP or HTTPS
     * @param string|null $protocol
     * @return Server
     */
    private function compileProtocol(string $protocol = null): Server {
        $this->setProtocol(is_string($protocol) ? $protocol : (isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : 'HTTP'));
        return $this;
    }

    /**
     * The setProtocol method is used to set the protocol used to call the api
     * @param string $protocol [should be either HTTP or HTTPS]
     */
    private function setProtocol(string $protocol) {
        $this->protocol = strtoupper($protocol);
    }

    /**
     * The getProtocol method is used to return the protocol used to call the api
     * @return string [representing the protocol used to call the api, results are HTTP or HTTPS]
     */
    public function getProtocol(): string {
        return $this->protocol;
    }

    /**
     * The getRequest method is used to as a factory to create a new Request object.
     * @return Request [The Request object models the request that was made]
     */
    public function getRequest(): Request {
        return new Request(
            $this->getOriginalUrl(),
            $this->getMethod(),
            $this->getPath(),
            $this->getQuery(),
            $this->getBody(),
            $this->getFiles(),
            $this->getCookie(),
            $this->getHeaders(),
            $this->getProtocol()
        );
    }
}