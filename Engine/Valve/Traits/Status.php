<?php


namespace app\Machine\Engine\Valve\Traits;


trait Status
{

    protected array $status;


    /**
     * @return array
     */
    public function checkForStatus(): array
    {
        if (isset($this->status)){
            foreach ($this->status as $key => $value){
                $this->data[$key] = $value;
            }
            return $this->data;
        }else{
            return [];
        }
    }

}