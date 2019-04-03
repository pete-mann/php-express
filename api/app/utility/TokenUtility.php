<?php

namespace ExpressPHP\utility;
use \Firebase\JWT\JWT;

/**
 * The AuthUtility class is used to provide formatting authentication data required throughout the application
 * This is a static singleton class
 * @author Pete Mann - peter.mann.design@gmail.com
 */
class TokenUtility {

    /**
  	 * The __construct method is private because this is a static class
  	 */
    private function __construct() {}

    public static function encode($user, $validitySeconds, $key) {
        $token = [
            'iss' => 'melbourne.sma.com.au',
            'aud' => 'melbourne.sma.com.au',
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + $validitySeconds,
            'user' => $user
        ];
        return JWT::encode($token, $key);
    }

    public static function decode($token, $key) {
        return (array) JWT::decode($token, $key, array('HS256'));
    }

}