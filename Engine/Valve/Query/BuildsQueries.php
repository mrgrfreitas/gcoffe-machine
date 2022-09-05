<?php

namespace app\Machine\Engine\Valve\Query;


use app\Machine\Engine\Cylinders\Read;

class BuildsQueries
{

    public static function one($model, $column)
    {
        $read = new Read();
        $read->query("SELECT $column FROM $model ORDER BY $column DESC");
        return $read->getResult()[0][$column];
    }

    /**
     * @param $table
     * @param $column
     * @param $id
     * @return mixed
     */
    public static function previous($table, $column, $id)
    {
        $read = new Read();
        $read->query("SELECT * FROM $table WHERE $column < :id ORDER BY $column DESC LIMIT 1", "id={$id}");
        if (!empty($read->getResult())){
            return $read->getResult()[0];
        }else{
            return  null;
        }
    }

    /**
     * @param $table
     * @param $column
     * @param $id
     * @return mixed
     */
    public static function nextTo($table, $column, $id)
    {
        $read = new Read();
        $read->query("SELECT * FROM $table WHERE $column > :id ORDER BY $column ASC LIMIT 1", "id={$id}");
        if (!empty($read->getResult())){
            return $read->getResult()[0];
        }else{
            return  null;
        }
    }

    public static function where($model, $reference, $value, $column)
    {
        $column = $column ? $column : $reference;
        $read = new Read();
        $read->query("SELECT $column FROM $model WHERE $reference = :id", "id={$value}");
        return $read->getResult()[0][$column];
    }

    public static function gallery($id)
    {
        $read = new Read();
        $read->query("SELECT * FROM `galleries` WHERE album_id = :id", "id={$id}");
        return $read->getResult();
    }

}