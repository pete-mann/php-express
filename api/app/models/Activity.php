<?php

namespace ExpressPHP\models;
use \ExpressPHP\core\DataBase;

/**
 * The Activity class is used handle logic associated with work codes
 * @author Pete Mann - peter.mann.design@gmail.com
 */
class Activity extends Model {

    public $activityId;

    public $name;

    public function __construct($activityId = null, $name = null) {
        $this->setActivityId($activityId);
        $this->setName($name);
    }

    public static function staticConstruct($activityId = null, $name = null) {
        return new Activity($activityId, $name);
    }

    /**
     * @return mixed
     */
    public function getActivityId() {
        return $this->activityId;
    }

    /**
     * @param $activityId
     */
    public function setActivityId($activityId) {
        if(!is_int($activityId)) throw new \InvalidArgumentException("Activity ID must be a number. Input was: {$activityId}");
        $this->activityId = $activityId;
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
        $stmt = DataBase::getConnection()->prepare("UPDATE activity SET name = :name WHERE activityId = :activityId");
        $activityId = $this->getActivityId();
        $name = $this->getName();
        $stmt->bindParam('activityId', $activityId);
        $stmt->bindParam('name', $name);
        $stmt->execute();
    }

    public function findAll() {
        $stmt = DataBase::getConnection()->query("SELECT * from activity");
        return $stmt->fetchAll(\PDO::FETCH_FUNC, ['ExpressPHP\models\Activity', 'staticConstruct']);
    }

    public function findOne($activityId) {
        $stmt = DataBase::getConnection()->prepare("SELECT * from activity where activityId = :activityId LIMIT 1");
        $stmt->bindParam("activityId", $activityId);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_FUNC, ['ExpressPHP\models\Activity', 'staticConstruct'])[0];
    }

    public function updateOne(Activity $activity) {
        $stmt = DataBase::getConnection()->prepare("UPDATE activity SET name = :name WHERE activityId = :activityId");
        $activityId = $activity->getActivityId();
        $name = $activity->getName();
        $stmt->bindParam('activityId', $activityId);
        $stmt->bindParam('name', $name);
        $stmt->execute();
    }

    public function findActivity($clientId = null, $projectId = null, $workCodeId = null) {
        $stmt = DataBase::getConnection()->prepare("SELECT * from activity a JOIN workCodeActivity wca ON wca.activityId = a.activityId WHERE wca.clientId = :clientId AND wca.projectId = :projectId AND wca.workCodeId = :workCodeId");
        $stmt->bindParam("clientId", $clientId);
        $stmt->bindParam("projectId", $projectId);
        $stmt->bindParam("workCodeId", $workCodeId);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_FUNC, ['ExpressPHP\models\Activity', 'staticConstruct']);
    }

}