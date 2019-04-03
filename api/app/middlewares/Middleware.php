<?php


namespace ExpressPHP\middlewares;

use ExpressPHP\core\Request;
use ExpressPHP\core\Response;

interface Middleware {

    public function handle(Request $req, Response $res);

}