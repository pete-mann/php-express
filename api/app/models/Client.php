<?php

namespace ExpressPHP\models;
use \ExpressPHP\core\DataBase;

/**
 * The Client class is used handle logic associated with clients
 * @author Pete Mann - peter.mann.design@gmail.com
 */
class Client extends Model {

    public $clientId;

    public $name;

    public function __construct($clientId = null, $name = null) {
        $this->setClientId($clientId);
        $this->setName($name);
    }

    public static function staticConstruct($clientId = null, $name = null) {
        return new Client($clientId, $name);
    }

    /**
     * @return mixed
     */
    public function getClientId() {
        return $this->clientId;
    }

    /**
     * @param $clientId
     */
    public function setClientId($clientId) {
        if(!is_int($clientId) || $clientId < 1) throw new \InvalidArgumentException("Client ID must be a number. Input was: {$clientId}");
        $this->clientId = $clientId;
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

    public function save() {
        $stmt = DataBase::getConnection()->prepare("UPDATE client SET name = :name WHERE clientId = :clientId");
        $clientId = $this->getClientId();
        $name = $this->getName();
        $stmt->bindParam('clientId', $clientId);
        $stmt->bindParam('name', $name);
        $stmt->execute();
    }

    public function findAll() {
        $stmt = DataBase::getConnection()->query("SELECT * from client");
        return $stmt->fetchAll(\PDO::FETCH_FUNC, ['ExpressPHP\models\Client', 'staticConstruct']);
    }

    public function findOne($clientId) {
        $stmt = DataBase::getConnection()->prepare("SELECT * from client where clientId = :clientId LIMIT 1");
        $stmt->bindParam("clientId", $clientId);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_FUNC, ['ExpressPHP\models\Client', 'staticConstruct'])[0];
    }

    public function updateOne(Client $client) {
        $stmt = DataBase::getConnection()->prepare("UPDATE client SET name = :name WHERE clientId = :clientId");
        $clientId = $client->getClientId();
        $name = $client->getName();
        $stmt->bindParam('clientId', $clientId);
        $stmt->bindParam('name', $name);
        $stmt->execute();
    }

}