<?php

namespace app\Machine\Engine\Cylinders;

use app\Machine\Engine\Database\Connection\Conn;
use PDO;
use PDOStatement;

class Show extends Conn
{

    /** @var PDOStatement */
    private $Show;

    /** @var PDO */
    private $Conn;


    public function table($query)
    {
        return str_replace('SELECT * FROM ',"", $query);
    }


    /**
     * @param $query
     * @return array|false
     */
    public function columns($query)
    {
        $table = $this->table($query);

        $this->Conn = parent::getConn();
        $this->Show = $this->Conn->prepare("DESCRIBE $table");
        $this->Show->execute();
        return $this->Show->fetchAll(PDO::FETCH_COLUMN);
    }


    /**
     * @param $query
     * @return false|PDOStatement
     */
    public function pk($query)
    {
        return $this->columns($query)[0];
    }
}