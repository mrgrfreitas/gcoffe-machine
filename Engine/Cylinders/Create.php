<?php


namespace app\Machine\Engine\Cylinders;

use app\Machine\Engine\Database\Connection\Conn;
use PDO;
use PDOException;
use PDOStatement;

/**
 * <b>Class Create</b>
 * Class responsible for genetic records in the database
 *
 * @package app\Machine\Engine\Cylinder
 * @copyright (c) 2021, Geraldo Freitas gWorks Tech
 */
class Create extends Conn
{
    /**
     * @var string
     */
    private string $Table;

    /**
     * @var array
     */
    private array $Data;

    /**
     * @var
     */
    private $Result;

    /**
     * @var
     */
    private $Error;

    /** @var PDOStatement */
    private $Create;

    /** @var PDO */
    private $Conn;

    /**
     * <b>Insert:</b> Executes a simplified registration in the database using prepared statements.
     * Just inform the table name and an attributive array with column name and value!
     *
     * @param string $Table = Enter the name of the Table in question!
     * @param array $Data = Enter an attributable array. ( column name => value ).
     */
    public function insert(string $Table, array $Data) {
        $this->Table = (string) $Table;
        $this->Data = $Data;

        $this->getSyntax();
        $this->Execute();
    }

    public function queryInsert($table, $columns, $values)
    {
        $this->Conn = parent::getConn();
        $this->Create = $this->Conn->prepare("INSERT INTO {$table} ({$columns}) VALUES {$values}");
        $this->Create->execute();
    }

    /**
     * <b>get result:</b> Returns the ID of the entered record or FALSE if no record is entered!
     * @return INT $Var = lastInsertId OR FALSE
     */
    public function getResult() {
        return $this->Result;
    }

    /**
     * <b>getError:</b> Returns the error.
     * @return mixed
     */
    public function getError() {
        return $this->Error;
    }

    /**
     * ****************************************
     * *********** PRIVATE METHODS ************
     * ****************************************
     */


    // Get the PDO and prepare the query
    private function Connect() {
        $this->Conn = parent::getConn();
        $this->Create = $this->Conn->prepare($this->Create);
    }

    //Create the query syntax for Prepared Statements
    private function getSyntax() {
        $Fields = implode(', ', array_keys($this->Data));
        $Places = ':' . implode(', :', array_keys($this->Data));
        $this->Create = "INSERT INTO {$this->Table} ({$Fields}) VALUES ({$Places})";
    }

    //Get the Connection and Syntax, execute the query!
    private function Execute() {
        $this->Connect();
        try {
            $this->Create->execute($this->Data);
            $this->Result = $this->Conn->lastInsertId();

        } catch (PDOException $e) {
            $this->Result = false;
            $this->Error = $e->getMessage();
            logger($e);
            //logger($e, gc_DANGER, ERROR_ON_CREATE);
        }
    }
}