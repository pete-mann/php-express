<?php

namespace ExpressPHP\models;
use \ExpressPHP\core\DataBase;
use ExpressPHP\utility\AuthUtility;

/**
 * The User class is used handle logic associated with user
 * @author Pete Mann - peter.mann.design@gmail.com
 */
class User extends Model {

    public $userId;

    public $firstname;

    public $surname;

    public $email;

    public function __construct($userId = null, $firstname = null, $surname = null, $email = null) {
        $this->setUserId($userId);
        $this->setFirstname($firstname);
        $this->setSurname($surname);
        $this->setEmail($email);
    }

    public static function staticConstruct($userId = null, $firstname = null, $surname = null, $email = null) {
        return new User($userId, $firstname, $surname, $email);
    }

    /**
     * @return mixed
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId) {
        if(!is_int($userId)) throw new \InvalidArgumentException("User ID must be a number. Input was: {$userId}");
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname) {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getSurname() {
        return $this->surname;
    }

    /**
     * @param mixed $surname
     */
    public function setSurname($surname) {
        $this->surname = $surname;
    }

    /**
     * @return mixed
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email) {
        if(AuthUtility::isEmailValid($email) == false) throw new \InvalidArgumentException("Email address must be valid. Input was: {$email}");
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword() {
        $stmt = DataBase::getConnection()->query("SELECT password from user WHERE userId = {$this->getUserId()}");
        $password = $stmt->fetch(\PDO::FETCH_ASSOC);
        return ($password != false) ? $password['password'] : false;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password) {
//        if(AuthUtility::isPasswordValid($password) == false) throw new \InvalidArgumentException("Password does not meet complexity requirements");
        $this->password = $password;
    }

    public function save() {
        $stmt = DataBase::getConnection()->prepare("UPDATE user SET email = :email, firstname = :firstname, surname = :surname WHERE userId = :userId");
        $email = $this->getEmail();
        $firstname = $this->getFirstName();
        $surname = $this->getSurname();
        $userId = $this->getUserId();
        $stmt->bindParam('email', $email);
        $stmt->bindParam('firstname', $firstname);
        $stmt->bindParam('surname', $surname);
        $stmt->bindParam('userId', $userId);
        $stmt->execute();
    }

    public function findAll() {
        $stmt = DataBase::getConnection()->query("SELECT userId, firstname, surname, email from user");
        return $stmt->fetchAll(\PDO::FETCH_FUNC, ['ExpressPHP\models\User', 'staticConstruct']);
    }

    public function findOne($userId) {
        $stmt = DataBase::getConnection()->prepare("SELECT userId, firstname, surname, email from user WHERE userId = :userId LIMIT 1");
        $stmt->bindParam("userId", $userId);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_FUNC, ['ExpressPHP\models\User', 'staticConstruct'])[0];
    }

    public function updateOne(User $user) {
        $stmt = DataBase::getConnection()->prepare("UPDATE user SET email = :email, firstname = :firstname, surname = :surname WHERE userId = :userId");
        $email = $user->getEmail();
        $firstname = $user->getFirstName();
        $surname = $user->getSurname();
        $userId = $user->getUserId();
        $stmt->bindParam('email', $email);
        $stmt->bindParam('firstname', $firstname);
        $stmt->bindParam('surname', $surname);
        $stmt->bindParam('userId', $userId);
        $stmt->execute();
    }

    public static function findByEmail($email) {
        $stmt = DataBase::getConnection()->prepare("SELECT userId, firstname, surname, email, password from user WHERE email = :email LIMIT 1");
        $stmt->bindParam('email', $email);
        $stmt->execute();
        $users = $stmt->fetchAll(\PDO::FETCH_FUNC, ['ExpressPHP\models\User', 'staticConstruct']);
        return $users ? $users[0] : false;
    }

}