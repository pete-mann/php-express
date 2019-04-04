<?php

namespace ExpressPHP\controllers;
use ExpressPHP\models\Project;
use ExpressPHP\core\DataBase;
use ExpressPHP\core\Request;
use ExpressPHP\core\Response;

/**
 * The CtrlProject class is used handle logic associated with projects
 * @author Pete Mann - peter.mann.design@gmail.com
 */
class CtrlProject extends Project {

    /**
     * CtrlUser constructor
     */
    public function __construct() {}

    /**
     * The index method is used to return all projects
     * @param Request $req
     * @param Response $res
     */
    public function index(Request $req, Response $res) {
        $res->json(['projects' => $this->findAll()]);
    }

    public function update(Request $req, Response $res) {
        $project = null;
        try  {
            $project = new Project(
                $req->getBody()['project']['projectId'],
                $req->getBody()['project']['name'],
                $req->getBody()['project']['clientId']
            );
        } catch(\InvalidArgumentException $e) {
            $res->send(400, ['title' => 'Client error', 'message' => $e->getMessage()]);
        }

        $this->updateOne($project);
        $res->json(['project' => $this->findOne($project->getProjectId())]);
    }

    public function getClientProjects(Request $req, Response $res) {
        $stmt = DataBase::getConnection()->prepare("SELECT * from project WHERE clientId = :clientId");
        $stmt->bindParam("clientId", $req->getParams()["clientId"]);
        $stmt->execute();
        $res->json(['projects' => $stmt->fetchAll(\PDO::FETCH_ASSOC)]);
    }

    /**
     * The show method is used to return the client specified in the url using a named param: clientId
     * @param Request $req
     * @param Response $res
     */
    public function show(Request $req, Response $res) {
        $res->json(['project' => $this->findOne($req->getParams()["projectId"])]);
    }

}