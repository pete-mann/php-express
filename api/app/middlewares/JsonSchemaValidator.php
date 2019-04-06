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
        // Load swagger schema for this exact request - based on request path and method type.

        // Examples:
        // For a route path of /client using POST method
        // $schema = file_get_contents("docs/schemas/{$req->getUniqueId()}.schema.json);
        // Result would load a schema from: docs/schemas/post.client.schema.json

        // For a route path of /client/:clientId using PUT method
        // $schema = file_get_contents("docs/schemas/{$req->getUniqueId()}.schema.json);
        // Result would load a schema from: docs/schemas/put.client.clientid.schema.json
    }

}