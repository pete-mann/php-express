<?php

namespace ExpressPHP\models;
use \ExpressPHP\core\DataBase;

/**
 * The WorkCode class is used handle logic associated with work codes
 * @author Pete Mann - peter.mann.design@gmail.com
 */
class WorkCode extends Model {

    public $workCodeId;

    public $name;

    public $clientId;

    public $projectId;

    public function __construct($workCodeId = null, $name = null, $clientId = null, $projectId = null) {
        $this->setWorkCodeId($workCodeId);
        $this->setName($name);
        $this->setClientId($clientId);
        $this->setProjectId($projectId);
    }

    public static function staticConstruct($workCodeId = null, $name = null, $clientId = null, $projectId = null) {
        return new WorkCode($workCodeId, $name, $clientId, $projectId);
    }

    /**
     * @return mixed
     */
    public function getWorkCodeId() {
        return $this->workCodeId;
    }

    /**
     * @param $workCodeId
     */
    public function setWorkCodeId($workCodeId) {
        if(!is_int($workCodeId)) throw new \InvalidArgumentException("WorkCode ID must be a number. Input was: {$workCodeId}");
        $this->workCodeId = $workCodeId;
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

    /**
     * @return mixed
     */
    public function getProjectId() {
        return $this->projectId;
    }

    /**
     * @param mixed $projectId
     */
    public function setProjectId($projectId) {
        if(!is_int($projectId)) throw new \InvalidArgumentException("Project ID must be a number. Input was: {$projectId}");
        $this->projectId = $projectId;
    }

    public function save() {
        $stmt = DataBase::getConnection()->prepare("UPDATE workCode SET name = :name WHERE workCodeId = :workCodeId");
        $workCodeId = $this->getWorkCodeId();
        $name = $this->getName();
        $stmt->bindParam('workCodeId', $workCodeId);
        $stmt->bindParam('name', $name);
        $stmt->execute();
    }

    public function findAll() {
        $stmt = DataBase::getConnection()->query("SELECT * from workCode");
        return $stmt->fetchAll(\PDO::FETCH_FUNC, ['ExpressPHP\models\WorkCode', 'staticConstruct']);
    }

    public function findOne($workCodeId) {
        $stmt = DataBase::getConnection()->prepare("SELECT * from workCode where workCodeId = :workCodeId LIMIT 1");
        $stmt->bindParam("workCodeId", $workCodeId);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_FUNC, ['ExpressPHP\models\WorkCode', 'staticConstruct'])[0];
    }

    public function updateOne(WorkCode $workCode) {
        $stmt = DataBase::getConnection()->prepare("UPDATE workCode SET name = :name WHERE workCodeId = :workCodeId");
        $workCodeId = $workCode->getWorkCodeId();
        $name = $workCode->getName();
        $stmt->bindParam('workCodeId', $workCodeId);
        $stmt->bindParam('name', $name);
        $stmt->execute();
    }

}