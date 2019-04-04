<?php

namespace ExpressPHP\controllers;
use ExpressPHP\models\Client;
use ExpressPHP\core\Request;
use ExpressPHP\core\Response;

/**
 * The CtrlClient class is used handle logic associated with clients
 * @author Pete Mann - peter.mann.design@gmail.com
 */
class CtrlClient extends Client {

    /**
     * CtrlUser constructor
     */
    public function __construct() {}

    /**
     * The index method is used to return all clients
     * @param Request $req
     * @param Response $res
     */
    public function index(Request $req, Response $res) {
        $res->json(['clients' => $this->findAll()]);
    }

    /**
     * The update method is used to update a single client in the database
     * @param Request $req
     * @param Response $res
     */
    public function update(Request $req, Response $res) {
        $client = null;
        try  {
            $client = new Client(
                $req->getBody()['client']['clientId'],
                $req->getBody()['client']['name']
            );
        } catch(\InvalidArgumentException $e) {
            $res->send(400, ['title' => 'Client error', 'message' => $e->getMessage()]);
        }

        $this->updateOne($client);
        $res->json(['client' => $this->findOne($client->getClientId())]);
    }

    /**
     * The show method is used to return the client specified in the url using a named param: clientId
     * @param Request $req
     * @param Response $res
     */
    public function show(Request $req, Response $res) {
        $res->json(['client' => $this->findOne($req->getParams()["clientId"])]);
    }

}