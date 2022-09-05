<?php


namespace app\Machine\Engine\Valve;

use app\Machine\Engine\Pagination\Paginator;
use Illuminate\Support\Traits\ForwardsCalls;
use app\Machine\Engine\Cylinders\Read;

class Builder
{
    use ForwardsCalls;

    /**
     * The base query builder instance.
     * @var
     */
    protected $query;

    /**
     * All of the globally registered builder macros.
     *
     * @var array
     */
    protected static array $macros = [];

    /**
     * The model being queried.
     *
     * @var Model
     */
    protected Model $model;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected string $table;

    /** @var string */
    protected string $statement;

    /** @var string */
    protected string $counter;

    /** @var  */
    protected $relationship;

    /** @var  */
    protected $conditions;

    /**
     * @var array
     */
    public array $whereClause;

    /** @var  */
    protected $bindParams;

    /**
     * The orderings for the query.
     *
     * @var array
     */
    public $orders;

    /**
     * The maximum number of records to return.
     *
     * @var
     */
    public $limit;

    /**
     * The number of records to skip.
     *
     * @var
     */
    public $offset;

    /** @var  */
    protected $group;

    /**
     * All of the available clause operators.
     *
     * @var array
     */
    public array $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=', '<=>',
        'like', 'LIKE', 'like binary', 'not like', 'ilike',
        '&', '|', '^', '<<', '>>',
        'rlike', 'not rlike', 'regexp', 'not regexp',
        '~', '~*', '!~', '!~*', 'similar to', 'AND', 'and', 'OR', 'or', 'NOT', 'not',
        'not similar to', 'not ilike', '~~*', '!~~*',
    ];


    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->table =  $this->model->getTable();
    }

    /**
     * Add a where clause on the primary key to the query.
     *
     * @param mixed $id
     * @return $this
     */
    public function whereKey($id)
    {
        return $this->where([[$this->model->getQualifiedKeyName(), '=', $id]]);
    }

    /**
     * @param $column
     * @return string
     */
    public function qualifiedKeyName($column)
    {
        return $this->model->qualifyColumn($column);
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     * @param string $boolean
     * @return Builder
     */
    public function where($column, string $boolean = 'AND', $operator = null, $value = null)
    {

        if(is_string($column)){
            $this->conditions = "WHERE $column";
        }else{
            $data = $this->nested(...func_get_args());
            $count = count($data);
            $i = 0;
            $conditions = '';
            $param = '';

            $checkOperator  = $this->arraySearch($data[0][1], $this->operators);
            $checkBoolean   = $this->arraySearch($boolean, $this->operators);

            if ($checkBoolean){$condBoolean = $boolean;}

            if ($checkOperator) {

                while ($i < $count){

                    $bind = 'param_'.$i;
                    $conditions .= $data[$i][0]. ' ' . $data[$i][1]. ' :'. $bind . " $condBoolean ";
                    if ($data[$i][1] === 'like' || $data[$i][1] === 'LIKE') {
                        $keyToSearch = '%' . $data[$i][2] . '%';
                        $param .= $bind. '=' . $keyToSearch . '&';
                    }else{
                        $param .= $bind. '=' . $data[$i][2]. '&';
                    }
                    $i++;
                }

                if (strlen($condBoolean) == 2){
                    $conditions = substr($conditions, 0, -4);
                }else{
                    $conditions = substr($conditions, 0, -5);
                }

                $this->conditions = "WHERE $conditions";
                $this->bindParams = substr($param, 0, -1);

            } else {

                $value = $operator;
                $this->conditions .= "WHERE $column = :column";
                $this->bindParams = "column={$value}";
            }
        }

        $this->whereClause = [$this->conditions, $this->bindParams];
        return $this;
    }


    /**
     * Paginate the given query.
     *
     * @param null $perPage
     * @return $this
     */
    public function paginate($perPage = null)
    {

        $page = filter_input(INPUT_GET, VAR_NAME ?? '', FILTER_VALIDATE_INT);

        $perPage = $perPage ?: $this->model->getPerPage();

        $Pager = new Paginator();
        $Pager->exePager($page, $perPage);
        $Pager->exePaginator($this->table);

        $this->limit($Pager->getLimit());
        $this->offset($Pager->getOffset());

        return $this->get();
    }

    public function simplePaginate($perPage = null)
    {
        $perPage = $perPage ?: $this->model->getPerPage();

        $Pager = new Paginator(null, PREVIOUS_LINK, NEXT_LINK);
        $Pager->simplePager($perPage);
        $Pager->simplePaginator($this->table);

        $this->limit($Pager->getLimit());
        $this->offset($Pager->getOffset());

        return $this->get();
    }

    /**
     * @param $counter
     * @param string $column
     * @return $this
     */
    public function count($counter, string $column = '*'): Builder
    {
        $this->counter = "$column, COUNT($counter) as counter";
        return $this;
    }

    /**
     * @param $id
     * @param string[] $columns
     * @return mixed
     */
    public function findOne($id, array $columns = ['*'])
    {
        if (is_array($id)) {
            return $this->findMany($id, $columns);
        }
        return $this->whereKey($id)->get()[0];
    }

    /**
     * @param $relationship
     * @return $this
     */
    public function innerJoin($relationship)
    {
        $join = 'INNER JOIN';
        if (is_array($relationship)){
            $relationship = $this->checkRelationship($relationship);
        }else{
            $relationship = [func_get_args()];
        }

        $count = count($relationship);
        $i = 0;
        $relations = '';

        while ($i < $count){

            $field = $this->table. '.' .$relationship[$i][0];
            $referencedTable = $relationship[$i][1];
            $referencedField = $relationship[$i][1] . '.' . $relationship[$i][2];

            $relations .= $join. ' ' . $referencedTable. ' ON ' .$referencedField. ' = '. $field . ' ';
            $i++;
        }

        $this->relationship = $relations;
        return $this;
    }

    public function nested($function)
    {
        return $function;
    }

    /**
     * Execute the query as a "select" statement.
     * @param array|string  $columns
     * @return mixed
     */
    public function get($columns = ['*'])
    {
        if (!empty($this->counter)){
            $this->statement = $this->counter;
        }else{
            $this->statement = implode(', ', $columns);
        }

        $this->statement = "SELECT $this->statement FROM $this->table ";
        $this->query = (

            $this->statement.
            $this->relationship.
            $this->conditions.
            $this->group.
            $this->orders.
            $this->limit.
            $this->offset
        );

        $select = new Read();
        $select->query($this->query, $this->bindParams);

        if (!$select->getResult()){
            if (!empty($select->getError())){
                //echo view('errors/');
                echo $select->getError();
                exit();
            }
        }

        return $select->getResult();
    }


    /**
     * @return int
     */
    public function getRowCount(): int
    {
        return count($this->get());
    }


    /**
     * Return the order of query. e.g. ORDER BY COLUMN ASC OR DESC
     *
     * @param $column
     * @param string $direction
     * @return $this
     */
    public function orderBy($column, string $direction = 'asc'): Builder
    {

        $direction = strtolower($direction);

        if (! in_array($direction, ['asc', 'desc'], true)) {
            GWError('Order direction must be "asc" or "desc".', GW_DANGER);
            die();
        }

        $this->orders = " ORDER BY {$this->qualifiedKeyName($column)} {$direction}";
        return $this;
    }

    /**
     * Add a descending "order by" clause to the query.
     *
     * @param  string  $column
     * @return $this
     */
    public function orderByDesc($column)
    {
        return $this->orderBy($this->qualifiedKeyName($column), 'desc');
    }

    /**
     * Add an "order by" clause for a timestamp to the query.
     *
     * @param string $column
     * @return $this
     */
    public function latest(string $column = 'created_at')
    {
        return $this->orderBy($this->qualifiedKeyName($column), 'desc');
    }

    /**
     * Add an "order by" clause for a timestamp to the query.
     *
     * @param string $column
     * @return $this
     */
    public function oldest(string $column = 'created_at')
    {
        return $this->orderBy($this->qualifiedKeyName($column), 'asc');
    }

    /**
     * Set the "limit" value of the query.
     *
     * @param $value
     * @return $this
     */
    public function limit($value): Builder
    {
        $this->limit = " LIMIT $value";
        return $this;
    }

    /**
     * Set the "offset" value of the query.
     *
     * @param $value
     * @return $this
     */
    public function offset($value): Builder
    {
        $this->offset = " OFFSET $value";
        return $this;
    }

    /**
     * @param $column
     * @return $this
     */
    public function groupBy($column): Builder
    {
        $this->group = " GROUP BY $column";
        return $this;
    }

    /**
     * @param $value
     * @param $array
     * @return bool
     */
    public function arraySearch($value, $array): bool
    {
        return(in_array($value, $array));
    }

    private function checkRelationship($relationship)
    {
        $relationship = $this->nested(...func_get_args());
        foreach ($relationship as $rel);
        if(!is_array($rel)){
            $relationship = [$relationship];
        }

        return $relationship;
    }

    public function select($field)
    {
        return "SELECT $field FROM $this->table";
    }

    /**
     * Dynamically handle calls into the query instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $this->forwardCallTo($this->query, $method, $parameters);

        return $this;
    }

    /**
     * Dynamically handle calls into the query instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public static function __callStatic($method, $parameters)
    {
        $macros = [];
        return call_user_func_array(static::$macros[$method], $parameters);
    }




}