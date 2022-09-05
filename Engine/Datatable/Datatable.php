<?php

namespace app\Machine\Engine\Datatable;

use app\Machine\Engine\Cylinders\Read;
use app\Machine\Engine\Cylinders\Show;
use app\Machine\Engine\Valve\Model;
use app\Machine\Request;

class Datatable extends Model
{

    protected string $select;
    protected $search;
    protected $order;
    protected $limit;


    /**
     * @param $select
     * @return $this
     */
    public function of($select): static
    {
        $this->select = $select;
        return $this;
    }

    /**
     * @return $this
     */
    public function getSearch(): static
    {
        $request = new Request();

        if (isset($request->input("search")['value']) && $request->input("search")['value'] !== ''){

            $searchValue = $request->input("search")['value'];
            $columnsTable = (new Show())->columns($this->select);

            $assign_column = [];
            foreach ($columnsTable as $column){
                $assign_column[] = [$column, 'LIKE', $searchValue];
            }

            $this->search = (new static())->newQuery()->where($assign_column, 'OR')->whereClause;
        }else{
            $this->search = false;
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function getOrder(): static
    {
        $tableField = (new Show())->table($this->select) . '.' . (new Show())->pk($this->select);

        $request = new Request();
        if (array_key_exists("order", $request->getData()) && $request->input("order") !== null){
            $this->order = (new static())->newQuery()->orderBy($tableField, $request->input("order")['0']['dir'])->orders;
        }else{
            $this->order = (new static())->newQuery()->orderBy($tableField, 'DESC')->orders;
        }

        return $this;

    }


    /**
     * @return $this
     */
    public function getLimit(): static
    {
        $request = new Request();
        if ($request->input("length") != -1){
            $this->limit = (new static())->newQuery()->limit($request->input("start"). ',' .$request->input("length"))->limit;
        }else{
            $this->limit = false;
        }
        return $this;

    }


    /**
     * @param $true
     * @return array
     */
    public function make($true)
    {

        $query = $this->select;
        $param = '';

        if ($this->getSearch()->search){

            $query .= ' '. $this->getSearch()->search[0];
            $param = $this->getSearch()->search[1];
        }

        if ($this->getOrder()->order){

            $query .= ' '. $this->getOrder()->order;
        }

        if ($this->getLimit()->limit){

            $query .= ' '. $this->getLimit()->limit;
        }


        $data = array();

        if ($true){

            $select = new Read();

            // Total number of records without filtering
            $select->query($this->select);
            $totalRecords = $select->getRowCount();

            // Get filtering record data
            $select->query($query, $param);
            $records = $select->getResult();

            foreach ($records as $record){
                $output = array();
                $output[] = $record["roomTypeName"];
                $output[] = $record["roomTypeDesc"];
                $output[] = '<button type="button" name="update" id="'.$record["roomTypeId"].'" class="btn btn-primary btn-sm update">Edit</button>';
                $output[] = '<button type="button" name="delete" id="'.$record["roomTypeId"].'" class="btn btn-danger btn-sm delete">Delete</button>';

                $data[] = $output;
            }

            // Total number of records with filtering
            $totalRecordwithFilter = count($data);
        }

        //Draw
        $request = new Request();
        $draw = $request->input("draw");

        // Response
        return [
            "draw"              =>  intval($draw),
            "recordsTotal"      =>  $totalRecords,
            "recordsFiltered"   =>  $totalRecordwithFilter,
            "data"              =>  $data
        ];
    }


    public function rules(): array
    {
        // TODO: Implement rules() method.
    }
}