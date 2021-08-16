<?php


namespace app\Machine\Engine\Valve\Traits;


trait Hash
{
    protected array $hashing;


    /**
     * @param $data
     * @return mixed
     */
    public function checkForHas($data)
    {
        if (isset($this->hashing)){

            //returns an array in flip order, i.e. keys from array become values and values from array become keys.
            $fillable_flipped = array_flip($this->hashing);

            //returns an array containing all the entries of array which have keys that are present in all the arguments.
            $fillable_intersect = array_intersect_key($data, $fillable_flipped);
            foreach ($fillable_intersect as $key => $value){
                $data[$key] = password_hash($value, PASSWORD_DEFAULT);
            }
        }
        return $data;
    }

}