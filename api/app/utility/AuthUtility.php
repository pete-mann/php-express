<?php

namespace ExpressPHP\utility;

/**
 * The AuthUtility class is used to provide formatting authentication data required throughout the application
 * This is a static singleton class
 * @author Pete Mann - peter.mann.design@gmail.com
 */
class AuthUtility {

    private static $passwordRegex = '/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).{8,}/';

    /**
  	 * The __construct method is private because this is a static class
  	 */
    private function __construct() {}

    public static function isPasswordValid($password) {
        return preg_match(self::$passwordRegex, $password);
    }

    public static function isEmailValid($email) {
        return filter_var((string) $email, FILTER_VALIDATE_EMAIL);
    }

    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

}

?>
