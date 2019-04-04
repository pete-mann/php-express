<?php

namespace ExpressPHP\core;

/**
 * Class Response
 * The Response class is used to return a response to the client
 * @author Pete Mann - peter.mann.design@gmail.com
 * @package ExpressPHP\core
 */
class Response {

    /**
     * Response constructor.
     */
    public function __construct() { }

    /**
     * The setStatusCode method is used to set the status code of the response
     * @param $statusCode
     */
    public function setStatusCode($statusCode) {
        http_response_code($statusCode);
    }

    /**
     * The setHeader method is used for setting headers on the response
     * @param $headerKey [The key to use in the header]
     * @param $headerValue [The value to use in the header]
     */
    public function setHeader($headerKey, $headerValue) {
        header("{$headerKey}: {$headerValue}");
    }

    /**
     * The failedAuthentication method is used to terminate the request because the user is attempting to access a
     * protected route and the user is not logged in
     * @param $endpoint
     */
    public function failedAuthentication($endpoint, $message = '') {
        $this->send(401, [
            'title' => 'Not Authenticated',
            'message' => "The endpoint {$endpoint} is only available to authenticated users. {$message}"
        ]);
    }

    /**
     * The json method is used to echo JSON as an output from the API, this should be the only echo statement in the
     * application, other uses of echo within the application make the exit point unpredictable. An exception for this is
     * the custom error and exception handling that is also able to echo
     * @param $JSONEncodableData
     */
    public function json($JSONEncodableData) {
        header('Content-Type: application/json');
        echo json_encode($JSONEncodableData);
        $this->done();
    }

    /**
     * The xml method is used to echo xml back to the browser, this method can also be used to echo HTML back to the browser
     * @param $xmlData
     */
    public function xml($xmlData) {
        header('Content-Type: text/xml');
        echo $xmlData->saveXML();
        $this->done();
    }

    /**
     * The send method is used to set the status code and send the data back to the browser, currently only JSON is
     * supported as data
     * @param $statusCode
     * @param array $JSONEncodableData
     */
    public function send($statusCode, array $JSONEncodableData = []) {
        $this->setStatusCode($statusCode);
        if(empty($JSONEncodableData) == false) {
            $this->json($JSONEncodableData);
            $this->done();
        } else {
            echo "";
            $this->done();
        }
    }

    /**
     * The done method is used to complete the request and clean up any resources
     */
    private function done() {
        ob_end_flush();
        DataBase::destroyConnection();
        exit();
    }

}
