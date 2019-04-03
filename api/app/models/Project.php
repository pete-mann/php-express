<?php

namespace ExpressPHP\models;
use \ExpressPHP\core\DataBase;

/**
 * The Project class is used handle logic associated with projects
 * @author Pete Mann - peter.mann.design@gmail.com
 */
class Project extends Model {

    public $projectId;

    public $name;

    public $clientId;

    public function __construct($projectId = null, $name = null, $clientId = null) {
        $this->setProjectId($projectId);
        $this->setName($name);
        $this->setClientId($clientId);
    }

    public static function staticConstruct($projectId = null, $name = null, $clientId = null) {
        return new Project($projectId, $name, $clientId);
    }

    /**
     * @return mixed
     */
    public function getProjectId() {
        return $this->projectId;
    }

    /**
     * @param $projectId
     */
    public function setProjectId($projectId) {
        if(!is_int($projectId)) throw new \InvalidArgumentException("Project ID must be a number. Input was: {$projectId}");
        $this->projectId = $projectId;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getClientId() {
        return $this->clientId;
    }

    /**
     * @param mixed $clientId
     */
    public function setClientId($clientId) {
        if(!is_int($clientId)) throw new \InvalidArgumentException("Client ID must be a number. Input was: {$clientId}");
        $this->clientId = $clientId;
    }

    public function save() {
        $stmt = DataBase::getConnection()->prepare("UPDATE project SET name = :name WHERE projectId = :projectId");
        $projectId = $this->getProjectId();
        $name = $this->getName();
        $stmt->bindParam('projectId', $projectId);
        $stmt->bindParam('name', $name);
        $stmt->execute();
    }

    public function findAll() {
        $stmt = DataBase::getConnection()->query("SELECT * from project");
        return $stmt->fetchAll(\PDO::FETCH_FUNC, ['ExpressPHP\models\Project', 'staticConstruct']);
    }

    public function findOne($projectId) {
        $stmt = DataBase::getConnection()->prepare("SELECT * from project where projectId = :projectId LIMIT 1");
        $stmt->bindParam("projectId", $projectId);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_FUNC, ['ExpressPHP\models\Project', 'staticConstruct'])[0];
    }

    public function updateOne(Project $project) {
        $stmt = DataBase::getConnection()->prepare("UPDATE project SET name = :name WHERE projectId = :projectId");
        $projectId = $project->getProjectId();
        $name = $project->getName();
        $stmt->bindParam('projectId', $projectId);
        $stmt->bindParam('name', $name);
        $stmt->execute();
    }

}