<?php


namespace app\Machine\Engine\Cylinders;

use app\Machine\Engine\Database\Connection\Conn;
use PDO;
use PDOException;
use PDOStatement;

/**
 * <b>Class Update</b>
 * Class responsible for genetic updates in the database
 *
 * @package app\Machine\Engine\Cylinder
 * @copyright (c) 2021, Geraldo Freitas gWorks Tech
 */
class Update extends Conn {

    private $Table;
    private $Data;
    private $Conditions;
    private $Places;
    private $Result;

    /** @var PDOStatement */
    private $Update;

    /** @var PDO */
    private $Conn;

    /**
     * <b>Exe Update:</b> Executa uma atualização simplificada com Prepared Statments. Basta informar o
     * nome da Table, os Data a serem atualizados em um Attay Atribuitivo, as condições e uma
     * analize em cadeia (ParseString) para executar.
     * @param STRING $Table = Nome da Table
     * @param array $Data = [ NomeDaColuna ] => Valor ( Atribuição )
     * @param STRING $Conditions = WHERE coluna = :link AND.. OR..
     * @param STRING $ParseString = link={$link}&link2={$link2}
     */
    public function ExeUpdate(string $Table, array $Data, $Conditions, $ParseString) {
        $this->Table = (string) $Table;
        $this->Data = $Data;

        $this->Conditions = (string) $Conditions;

        parse_str($ParseString, $this->Places);
        $this->getSyntax();
        $this->Execute();
    }

    /**
     * <b>Obter resultado:</b> Retorna TRUE se não ocorrer erros, ou FALSE. Mesmo não alterando os Data se uma query
     * for executada com sucesso o retorno será TRUE. Para verificar alterações execute o getRowCount();
     * @return BOOL $Var = True ou False
     */
    public function getResult() {
        return $this->Result;
    }


    /**
     * <b>Contar Registros: </b> Retorna o número de linhas alteradas no banco!
     * @return INT $Var = Quantidade de linhas alteradas
     */
    public function getRowCount() {
        return $this->Update->rowCount();
    }

    /**
     * <b>Modificar Links:</b> Método pode ser usado para atualizar com Stored Procedures. Modificando apenas os valores
     * da condição. Use este método para editar múltiplas linhas!
     * @param STRING $ParseString = id={$id}&..
     */
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
        $this->Update = $this->Conn->prepare($this->Update);
    }

    //Cria a sintaxe da query para Prepared Statements
    private function getSyntax() {
        foreach ($this->Data as $Key => $Value):
            $Places[] = $Key . ' = :' . $Key;
        endforeach;

        $Places = implode(', ', $Places);
        $this->Update = "UPDATE {$this->Table} SET {$Places} {$this->Conditions}";
    }

    //Obtém a Conexão e a Syntax, executa a query!
    private function Execute() {
        $this->Connect();
        try {
            $this->Update->execute(array_merge($this->Data, $this->Places));
            $this->Result = true;
        } catch (PDOException $e) {
            $this->Result = null;
            logger($e, gc_DANGER, ERROR_ON_UPDATE);
        }
    }

}