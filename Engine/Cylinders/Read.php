<?php


namespace app\Machine\Engine\Cylinders;

use app\Machine\Engine\Database\Connection\Conn;
use PDO;
use PDOException;
use PDOStatement;

/**
 * <b>Class Read</b>
 * Class responsible for genetic reads in the database!
 * Returns a result set of records from one or more tables.
 *
 * @package app\Machine\Engine\Cylinder
 * @copyright (c) 2021, Geraldo Freitas gWorks Tech
 */
class Read extends Conn {

    /**
     * @var
     */
    private $Select;

    /**
     * @var
     */
    private $Places;

    /**
     * @var
     */
    private $Result;

    /**
     * @var
     */
    private $Error;

    /**
     * @var PDOStatement
     */
    private PDOStatement $Read;

    /** @var
     * PDO
     */
    private $Conn;

    /**
     * <b>all:</b> Perform a simplified read with Prepared Statements.
     * Just inform the name of the table, the possible conditions of the selection query and a chain analysis to execute.
     *
     * @param string $Table  = table name
     * @param null $Conditions  = WHERE | ORDER | LIMIT :limit | OFFSET :offset
     * @param null $ParseString = var={$var}&var2={$var2}
     */
    public function all($Table, $Conditions = null, $ParseString = null) {
        if (!empty($ParseString)):
            parse_str($ParseString, $this->Places);
        endif;

        $this->Select = "SELECT * FROM {$Table} {$Conditions}";
        $this->Execute();
    }

    /**
     * <b>getResult:</b> Returns an array with all the results obtained.
     * @return mixed
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
     * <b>getRowCount: </b> Returns the number of records found by select!
     * @return int $var = Number of records found
     */
    public function getRowCount() {
        return $this->Read->rowCount();
    }


    /**
     * <b>query:</b> Performs data reading via query that must be manually assembled to enable
     * selection of multiple Tables in a single query!
     *
     * @param string $Query = Query Select Syntax
     * @param null $ParseString = var={$var}&var2={$var2}
     */
    public function query($Query, $ParseString = null) {
        $this->Select = (string) $Query;
        if (!empty($ParseString)):
            parse_str($ParseString, $this->Places);
        endif;
        $this->Execute();
    }

    /**
     * @param $Query
     * @param null $ParseString
     */
    public function getColummn($Query, $ParseString = null) {
        $this->Select = (string) $Query;
        if (!empty($ParseString)):
            parse_str($ParseString, $this->Places);
        endif;
        $this->Execute('column');
    }

    /**
     * @param $ParseString
     */
    public function setPlaces($ParseString) {
        parse_str($ParseString, $this->Places);
        $this->Execute();
    }

    /**
     * ****************************************
     * *********** PRIVATE METHODS ************
     * ****************************************
     */

    /**
     * Get the PDO and prepare the query
     */
    private function Connect() {
        $this->Conn = parent::getConn();
        $this->Read = $this->Conn->prepare($this->Select);
        $this->Read->setFetchMode(PDO::FETCH_ASSOC);
    }

    /**
     * Create the query syntax for Prepared Statements
     */
    private function getSyntax() {
        if ($this->Places):
            foreach ($this->Places as $key => $value):
                if ($key == 'limit' || $key == 'offset'):
                    $value = (int) $value;
                endif;
                $this->Read->bindValue(":{$key}", $value, ( is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR));
            endforeach;
        endif;
    }

    /**
     * Get the Connection and Syntax, run the query!
     */
    private function Execute($PDOStatement = null) {
        $this->Connect();
        try {
            $this->getSyntax();
            $this->Read->execute();

            if($PDOStatement == 'column'){
                $this->Result = $this->Read->fetchAll(PDO::FETCH_COLUMN);
            }else{
                $this->Result = $this->Read->fetchAll();
            }

        } catch (PDOException $e) {
            $this->Result = false;
            $this->Error = $e->getMessage() . ' [ please consult the log file error or the admin system to see the complete error...]';
            logger($e);
        }
    }

}