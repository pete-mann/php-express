<?php

class CtrlUser {

    public function __construct() {}

    public function index(Request $req, Response $res) {
        $res->send(418, [
           'userId' => 1,
           'username' => 'Pete'
        ]);
    }

}