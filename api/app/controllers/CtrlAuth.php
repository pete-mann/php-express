<?php

namespace ExpressPHP\controllers;
use ExpressPHP\models\User;
use ExpressPHP\utility\AuthUtility;
use ExpressPHP\utility\TokenUtility;
use ExpressPHP\core\Request;
use ExpressPHP\core\Response;

/**
 * The CtrlAuth class is used handle logic associated with authentication
 * @author Pete Mann - peter.mann.design@gmail.com
 */
class CtrlAuth {

    /**
     * CtrlAuth constructor
     */
    public function __construct() {}

    public function auth(Request $req, Response $res) {
        $email = $req->getBody()['auth']['email'];
        $password = $req->getBody()['auth']['password'];
        $standardResponse = ['title' => 'Login failed', 'message' => 'Login failed, please try again'];

        if(AuthUtility::isEmailValid($email) == false) $res->send(400, $standardResponse);
        if(AuthUtility::isPasswordValid($password) == false) $res->send(400, $standardResponse);

        $user = User::findByEmail($email);

        if($user == false) $res->send(400, $standardResponse);

        if(AuthUtility::verifyPassword($password, $user->getPassword())) {
            $res->setHeader('x-auth-token', TokenUtility::encode($user, 7200,'ihgaDsd987619G*&(uy12nSkmj'));
            $res->json([
                'title' => 'Welcome',
                'message' => "Welcome {$user->getFirstname()} {$user->getSurname()}, have a great day!",
                'user' => $user
            ]);
        } else {
            $res->send(400, $standardResponse);
        }
    }

}