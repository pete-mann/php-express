<?php

/**
 * The Response class is used to return a response to the client
 * @author Pete Mann - peter.mann.design@gmail.com
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
     * The json method is used to echo JSON as an output from the API, this should be the only echo statement in the
     * application, other uses of echo within the application make the exit point unpredictable. An exception for this is
     * the custom error and exception handling that is also able to echo
     * @param $data
     */
    public function json($JSONdata) {
        header('Content-Type: application/json');
        echo json_encode($JSONdata);
        $this->done();
    }

    /**
     * The xml method is used to echo xml back to the browser, this method can also be used to echo HTML back to the browser
     * @param $data
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
     * @param array $data
     */
    public function send($statusCode, array $JSONdata = []) {
        $this->setStatusCode($statusCode);
        if(empty($JSONdata) == false) {
            $this->json($JSONdata);
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
