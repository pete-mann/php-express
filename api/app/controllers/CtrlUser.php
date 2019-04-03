<?php

namespace ExpressPHP\controllers;

/**
 * The CtrlUser class is used handle logic associated with users
 * @author Pete Mann - peter.mann.design@gmail.com
 */
class CtrlUser {

    /**
     * CtrlUser constructor
     */
    public function __construct() {}

    /**
     * The index method is used to return all users
     * @param Request $req
     * @param Response $res
     */
    public function index(Request $req, Response $res) {
        $stmt = DataBase::getConnection()->query("SELECT * from user");
        $res->json($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * The show method is used to return the user specified in the url using a named param: userId
     * @param Request $req
     * @param Response $res
     */
    public function show(Request $req, Response $res) {
        $stmt = DataBase::getConnection()->prepare("SELECT * from user where userId = :userId");
        $stmt->bindParam("userId", $req->getParams()["userId"]);
        $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $res->json($user);
    }

}