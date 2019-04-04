<?php

namespace ExpressPHP\middlewares;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use ExpressPHP\core\Request;
use ExpressPHP\core\Response;
use ExpressPHP\utility\TokenUtility;

/**
 * Class JWT
 * The JWT class is used to manage the token IO, encoding and decoding. Tokens are used for authorisation of connections.
 * @author Pete Mann - peter.mann.design@gmail.com
 * @package ExpressPHP\middlewares
 */
class JWT implements Middleware {

    public function __construct() {}

    public function handle(Request $req, Response $res) {
        $isAuthenticated = false;
        $message = '';
        if(array_key_exists('x-auth-token', $req->getHeaders())) {
            try {
                $token = TokenUtility::decode($req->getHeaders()['x-auth-token'], 'ihgaDsd987619G*&(uy12nSkmj');
                $isAuthenticated = time() < $token['exp'];
            } catch(SignatureInvalidException $e) {
                $isAuthenticated = false;
                $message = $e->getMessage();
            } catch(BeforeValidException $e) {
                $isAuthenticated = false;
                $message = $e->getMessage();
            } catch(ExpiredException $e) {
                $isAuthenticated = false;
                $message = $e->getMessage();
            } catch(\UnexpectedValueException $e) {
                $isAuthenticated = false;
                $message = $e->getMessage();
            } catch(\DomainException $e) {
                $isAuthenticated = false;
                $message = $e->getMessage();
            }
        }
        if($isAuthenticated == false) $res->failedAuthentication($req->getPath(), $message);
    }

}