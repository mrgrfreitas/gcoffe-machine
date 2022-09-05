<?php

namespace app\Machine\Engine\Database\Connection;


use PDO;
use PDOException;

/**
 * Class Conn
 * @package app\Machine\Engine\Connection
 * @author Geraldo Freitas
 */
abstract class Conn {

    private static $Drive   = DB['mysql']['driver'];
    private static $Host    = DB['mysql']['host'];
    private static $User    = DB['mysql']['username'];
    private static $Pass    = DB['mysql']['password'];
    private static $Dbsa    = DB['mysql']['database'];
    private static $Options = DB['mysql']['options'];

    /**
     * @var PDO
     */
    private static $Connect = null;

    /**
     * Connects to database with singleton pattern.
     * Returns a PDO object!
     * @return PDO|null
     */
    private static function Connecting() {
        try {
            if (self::$Connect == null):
                $dsn = self::$Drive . ':host=' . self::$Host . ';dbname=' . self::$Dbsa;
                $options = self::$Options;
                self::$Connect = new PDO($dsn, self::$User, self::$Pass, $options);
            endif;
        } catch (PDOException $e) {
            PHPError($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            die;
        }

        self::$Connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return self::$Connect;
    }

    /**
     * Returns a PDO Singleton Pattern object.
     * @return PDO|null
     */
    protected static function getConn() {
        return self::Connecting();
    }

}




//
//
//namespace app\Machine\Engine\Database\Connection;
//
//
//use PDO;
//use PDOException;
//
///**
// * Class Conn
// * @package app\Machine\Engine\Connection
// * @author Geraldo Freitas
// */
//abstract class Conn {
//
//    private static $Drive;
//    private static $Host;
//    private static $User;
//    private static $Pass;
//    private static $Dbsa;
//    private static $Options;
//
//    /**
//     * @var PDO
//     */
//    private static $Connect = null;
//
//    /**
//     * Connects to database with singleton pattern.
//     * Returns a PDO object!
//     * @return PDO|null
//     */
//    private static function Connecting() {
//
//        self::$Drive   = config('database.connections')['mysql']['driver'];
//        self::$Host    = config('database.connections')['mysql']['host'];
//        self::$User    = config('database.connections')['mysql']['username'];
//        self::$Pass    = config('database.connections')['mysql']['password'];
//        self::$Dbsa    = config('database.connections')['mysql']['database'];
//        self::$Options = config('database.connections')['mysql']['options'];
//
//        try {
//            if (self::$Connect == null):
//                $dsn = self::$Drive . ':host=' . self::$Host . ';dbname=' . self::$Dbsa;
//                $options = self::$Options;
//                self::$Connect = new PDO($dsn, self::$User, self::$Pass, $options);
//            endif;
//        } catch (PDOException $e) {
//            PHPError($e->getCode(), $e->getMessage(), '', '19087');
//            die;
//        }
//
//        self::$Connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//        return self::$Connect;
//    }
//
//    /**
//     * Returns a PDO Singleton Pattern object.
//     * @return PDO|null
//     */
//    protected static function getConn() {
//        return self::Connecting();
//    }
//
//}
