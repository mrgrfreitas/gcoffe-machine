<?php


namespace app\Machine\Engine\Auth\Events;


use app\Machine\Engine\Cylinders\Read;

trait Verified
{

    private function verifiedUserEmail($uniqueAttr, $value)
    {
        $query = new Read;
        $query->query("SELECT * FROM $this->table WHERE $uniqueAttr = :$uniqueAttr", "$uniqueAttr={$value}");
        if (!empty($query->getResult())){

            return $query->getResult()[0];

        }else{
            return false;
        }
    }

    public static function find($id)
    {
        $query = new Read;
        $query->query("SELECT * FROM users WHERE id = :$id", "$id={$id}");
        if (!empty($query->getResult())){

            return $query->getResult()[0];

        }else{
            return false;
        }
    }

}