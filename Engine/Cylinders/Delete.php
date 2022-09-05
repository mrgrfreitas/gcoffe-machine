<?php


namespace app\Machine\Engine\Cylinders;

use app\Machine\Engine\Database\Connection\Conn;
use PDO;
use PDOException;
use PDOStatement;

/**
 * <b>Class Update</b>
 * Class responsible for genetic deletes in the database
 *
 * @package app\Machine\Engine\Cylinder
 * @copyright (c) 2021, Geraldo Freitas gWorks Tech
 */
class Delete extends Conn {

    private $Table;
    private $Conditions;
    private $Places;
    private $Result;

    /** @var PDOStatement */
    private $Delete;

    /** @var PDO */
    private $Conn;

    public function ExeDelete($Table, $Conditions, $ParseString) {
        $this->Table = (string) $Table;
        $this->Conditions = (string) $Conditions;

        parse_str($ParseString, $this->Places);
        $this->getSyntax();
        $this->Execute();
    }

    public function getResult() {
        return $this->Result;
    }

    public function getRowCount() {
        return $this->Delete->rowCount();
    }

    public function setPlaces($ParseString) {
        parse_str($ParseString, $this->Places);
        $this->getSyntax();
        $this->Execute();
    }

    /**
     * ****************************************
     * *********** PRIVATE METHODS ************
     * ****************************************
     */
    //Obtém o PDO e Prepara a query
    private function Connect() {
        $this->Conn = parent::getConn();
        $this->Delete = $this->Conn->prepare($this->Delete);
    }

    //Cria a sintaxe da query para Prepared Statements
    private function getSyntax() {
        $this->Delete = "DELETE FROM {$this->Table} {$this->Conditions}";
    }

    //Obtém a Conexão e a Syntax, executa a query!
    private function Execute() {
        $this->Connect();
        try {
            $this->Delete->execute($this->Places);
            $this->Result = true;
        } catch (PDOException $e) {
            $this->Result = false;
            logger($e, gc_DANGER, ERROR_ON_DELETE);
        }
    }

}