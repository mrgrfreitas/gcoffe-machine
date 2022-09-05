<?php


namespace app\Machine\Engine\Database\Migrations;


use app\Machine\Engine\Cylinders\Create;
use app\Machine\Engine\Cylinders\Read;
use app\Machine\Engine\Database\Connection\Conn;
use PDO;
use PDOException;


/**
 * Class MigrationCreator
 * @package app\Machine\Engine\database\migrations
 */
class Schema extends Conn
{

    private static $SQL;
    private static $Table;

    /** @var PDO */
    private $Conn;

    public static function create($SQL)
    {
        self::$SQL = $SQL;
        (new static)->Execute();
    }

    public static function dropIfExists($table)
    {
        self::$Table = $table;
        (new static)->DropTable();
    }

    public static function allMigration()
    {
        $migrations = new Read;
        $migrations->getColummn("SELECT migration FROM migrations");
        return $migrations->getResult();
    }

    public static function query($table, $columns, $values)
    {
        $query = new Create;
        return $query->queryInsert($table, $columns, $values);
    }

    public static function insert($table, $data)
    {
        $query = new Create;
        return $query->insert($table, $data);
    }

    public static function truncate($table)
    {
        self::$Table = $table;
        (new static)->truncateTable();
    }

    /**
     * ****************************************
     * *********** PRIVATE METHODS ************
     * ****************************************
     */


    //Get the Connection and Syntax, execute the query!
    private function Execute() {
        $this->Conn = parent::getConn();
        try {
            $this->Conn->exec(self::$SQL);
            return true;

        } catch (PDOException $e) {
            return false;
        }
    }

    private function truncateTable() {
        $this->Conn = parent::getConn();
        $sql = "TRUNCATE TABLE ".self::$Table;
        try {
            $this->Conn->exec($sql);
            return true;

        } catch (PDOException $e) {
            return false;
        }
    }

    private function DropTable() {
        $this->Conn = parent::getConn();
        $sql = "DROP TABLE ".self::$Table;
        try {
            $this->Conn->exec($sql);
            return true;

        } catch (PDOException $e) {
            return false;
        }
    }

    
}