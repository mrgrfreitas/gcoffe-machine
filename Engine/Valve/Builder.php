<?php


namespace app\Machine\Engine\Valve;

use Illuminate\Support\Str;
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
    protected $model;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected string $table;

    /** @var string */
    protected string $statement;

    /** @var  */
    protected $relationship;

    /** @var  */
    protected $conditions;

    /** @var  */
    protected $bindParams;

    /**
     * The orderings for the query.
     *
     * @var array
     */
    public $orders;

    /** @var  */
    protected $limit;

    /**
     * All of the available clause operators.
     *
     * @var array
     */
    public $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=', '<=>',
        'like', 'like binary', 'not like', 'ilike',
        '&', '|', '^', '<<', '>>',
        'rlike', 'not rlike', 'regexp', 'not regexp',
        '~', '~*', '!~', '!~*', 'similar to',
        'not similar to', 'not ilike', '~~*', '!~~*',
    ];

    public array $Result;
    public int $RowCount;


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
    public function where($column, $operator = null, $value = null, string $boolean = 'AND')
    {
        if(is_string($column)){
            $this->conditions = "WHERE $column";
        }else{
            $data = $this->nested(...func_get_args());
            $count = count($data);
            $i = 0;
            $conditions = '';
            $param = '';

            $checkOperator = $this->arraySearch($data[0][1], $this->operators);

            if ($checkOperator) {

                while ($i < $count){

                    $bind = 'param_'.$i;
                    $conditions .= $data[$i][0]. ' ' . $data[$i][1]. ' :'. $bind . " $boolean ";
                    $param .= $bind. '=' . $data[$i][2]. '&';
                    $i++;
                }
                $conditions = substr($conditions, 0, -5);
                $this->conditions = "WHERE $conditions";
                $this->bindParams = substr($param, 0, -1);

            } else {
                $value = $operator;
                $this->conditions .= "WHERE $column = :column";
                $this->bindParams = "column={$value}";
            }
        }

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
        return $this->whereKey($id)->get();
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
        $this->statement = implode(', ', $columns);
        $this->statement = "SELECT $this->statement FROM $this->table ";
        $this->query = (

            $this->statement.
            $this->relationship.
            $this->conditions.
            $this->orders.
            $this->limit
        );
        
        $select = new Read();
        $select->query($this->query, $this->bindParams);

        $this->RowCount = $select->getRowCount();
        $this->Result = $select->getResult();
        return $this;
    }


    /**
     * @param $column
     * @param string $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'asc'): Builder
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
     * @param $value
     * @return $this
     */
    public function limit($value): Builder
    {
        $this->limit = " LIMIT $value";
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