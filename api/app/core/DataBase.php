<?php

namespace ExpressPHP\core;

/**
 * Class DataBase
 * The DataBase class is used to connect to a mysql data base.
 * This is a static singleton class that holds a copy of it's connection.
 * @author Pete Mann - peter.mann.design@gmail.com
 * @package ExpressPHP\core
 */
class DataBase {

    private static $env;

    private static $mode;

    private static $connection;

    /**
     * The __construct method is private because this is a static class
     */
    private function __construct() { }

    /**
     * The __destruct should destroy the database connection
     */
    public function __destruct() {
        self::destroyConnection();
    }

    /**
     * The destroyConnection method is used to terminate the connection to the database
     */
    public static function destroyConnection() {
        self::$connection = null;
    }

    /**
     * The setEnv method is used to set the environment array which is later used to connect to the database
     * @param array $env [The env param contains the connection information used to connect to the database]
     * @param $mode [The mode param is used to determine the type of connection, toggling exceptions on/off]
     */
    public static function setEnv(array $env, $mode) {
        self::$env = $env;
        self::$mode = $mode;
    }

    /**
     * The connect method is used to create a new connection to the database
     * @param  array  $env Accepts an array that contains the details required to login to the database
     */
    private static function connect(array $env) {
        if(self::$connection) {
            return;
        } else {
            try {
                # MySQL with PDO_MYSQL
                self::$connection = new \PDO(
                    "mysql:host=" . $env['host'] . ";dbname=" . $env['database'] . ";charset=utf8",
                    $env['username'],
                    $env['password'],
                    [\PDO::ATTR_EMULATE_PREPARES => false]
                );

                # PDO::ATTR_EMULATE_PREPARES => false
                # Add this to the above array to prevent mysql from
                # putting int in a string.

                if(self::$mode == 'prd') {
                    # production setting
                    self::$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
                } else {
                    # development setting
                    self::$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                }

            } catch(\PDOException $e) {
                echo $e;
            }
        }
    }

    /**
     * The getConnection method is used to get a connection to the database
     * @return \PDO Returns a PDO database connection
     */
    public static function getConnection() {
        if(!self::$connection) self::connect(self::$env);
        return self::$connection;
    }

}