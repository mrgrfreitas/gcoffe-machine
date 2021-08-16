<?php


namespace app\Machine\Engine\Valve\Traits;


use app\Machine\Request;

trait Fillable
{
    protected array $fillable;

    /**
     * @return array
     */
    public function fillable()
    {

        $request = new Request;
        if (isset($this->fillable)){

            //returns an array in flip order, i.e. keys from array become values and values from array become keys.
            $fillable_flipped = array_flip($this->fillable);

            //returns an array containing all the entries of array which have keys that are present in all the arguments.
            $fillable_intersect = array_intersect_key($request->data(), $fillable_flipped);

            $this->data = array_merge(
                $this->checkForHas($fillable_intersect),
                $this->checkForStatus(),
                $this->hidesAttr()
            );
        }else{
            $this->data = array_merge(
                $request->data(),
                $this->checkForStatus(),
                $this->hidesAttr()
            );
        }

        return $this->data;

    }

}