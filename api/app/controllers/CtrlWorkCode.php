<?php

namespace ExpressPHP\controllers;
use ExpressPHP\models\WorkCode;
use ExpressPHP\core\DataBase;
use ExpressPHP\core\Request;
use ExpressPHP\core\Response;

/**
 * The CtrlWorkCode class is used handle logic associated with workcodes
 * @author Pete Mann - peter.mann.design@gmail.com
 */
class CtrlWorkCode extends WorkCode {

    /**
     * CtrlUser constructor
     */
    public function __construct() {}

    /**
     * The index method is used to return all workcodes
     * @param Request $req
     * @param Response $res
     */
    public function index(Request $req, Response $res) {
        $res->json(['workCodes' => $this->findAll()]);
    }

    public function update(Request $req, Response $res) {
        $workCode = null;
        try  {
            $workCode = new WorkCode(
                $req->getBody()['workCode']['workCodeId'],
                $req->getBody()['workCode']['name'],
                $req->getBody()['workCode']['clientId'],
                $req->getBody()['workCode']['projectId']
            );
        } catch(\InvalidArgumentException $e) {
            $res->send(400, ['title' => 'Client error', 'message' => $e->getMessage()]);
        }

        $this->updateOne($workCode);
        $res->json(['workCode' => $this->findOne($workCode->getWorkCodeId())]);
    }

    public function getClientProjectWorkCode(Request $req, Response $res) {
        $stmt = DataBase::getConnection()->prepare("SELECT * from workCode WHERE clientId = :clientId AND projectId = :projectId");
        $stmt->bindParam("clientId", $req->getParams()["clientId"]);
        $stmt->bindParam("projectId", $req->getParams()["projectId"]);
        $stmt->execute();
        $res->json(['workCodes' => $stmt->fetchAll(\PDO::FETCH_ASSOC)]);
    }

    /**
     * The show method is used to return the workCode specified in the url using a named param: clientId
     * @param Request $req
     * @param Response $res
     */
    public function show(Request $req, Response $res) {
        $res->json(['workCode' => $this->findOne($req->getParams()["workCodeId"])]);
    }

}