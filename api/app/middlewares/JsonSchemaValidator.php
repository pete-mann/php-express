<?php

namespace ExpressPHP\middlewares;
use ExpressPHP\core\Request;
use ExpressPHP\core\Response;

/**
 * Class JsonSchemaValidator
 * The JsonSchemaValidator class is used to validate the body of requests (that have bodies) using a JSON schema document.
 * JSON schema documents are loaded based on the request path and method type.
 * @author Pete Mann - peter.mann.design@gmail.com
 * @package ExpressPHP\middlewares
 */
class JsonSchemaValidator implements Middleware {

    public function __construct() {}

    public function handle(Request $req, Response $res) {
        // TODO
        // Load swagger schema for this exact request - based on request path and method type.

        // Examples:
        // For a route path of /client using POST method
        // $schema = file_get_contents("docs/schemas/{$req->getRequestId()}.schema.json);
        // Result would load a schema from: docs/schemas/post.client.schema.json

        // For a route path of /client/:clientId using PUT method
        // $schema = file_get_contents("docs/schemas/{$req->getRequestId()}.schema.json);
        // Result would load a schema from: docs/schemas/put.client.clientid.schema.json

        // If possible return the error from the schema document, if it is possible to define custom errors in the document.

        // Get the file path
        $filePath = dirname(__DIR__) . "/docs/schemas/{$req->getRequestId()}.schema.json";

        // Check the file exists
        if(file_exists($filePath) == false) {
            $res->send(404, [
                'title' => 'Schema not found',
                'message' => "There is no schema defined ({$req->getRequestId()}) for the endpoint {$req->getPath()}"
            ]);
        }

        // Load the schema
        $schema = json_decode(
            file_get_contents($filePath),
            true
        );

        $doesBodyMatchSchema = false;
        // Send back the schema as a test
        if($doesBodyMatchSchema == false) $res->send(
            400,
            $schema
        );
    }

}