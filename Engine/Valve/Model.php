<?php


namespace app\Machine\Engine\Valve;

use app\Machine\Engine\Auth\Events\Verified;
use app\Machine\Engine\Cylinders\Create;
use app\Machine\Engine\Cylinders\Delete;
use app\Machine\Engine\Cylinders\Update;
use app\Machine\Engine\Helpers\File;
use app\Machine\Engine\Helpers\Validator;
use app\Machine\Engine\Valve\Traits\Fillable;
use app\Machine\Engine\Valve\Traits\Hash;
use app\Machine\Engine\Valve\Traits\HidesAttributes;
use app\Machine\Engine\Valve\Traits\Status;
use app\Machine\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;


/**
 * Class Model
 * @package app\Machine\Engine\Valve
 */
abstract class Model
{
    use Validator;
    use Verified;
    use Fillable;
    use HidesAttributes;
    use Hash;
    use Status;
    use File;
    use ForwardsCalls;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected string $table;

    /** @var string name of pk column */
    protected string $primaryKey = 'id';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /** @var array  */
    protected array $data;

    /** @var string */
    protected $statement;

    /** @var array  */
    protected array $Result;

     /** @var int  */
    protected int $RowCount;



    public function __construct()
    {
//        $this->table = $this->getTable();
//        $this->primaryKey = $this->getTablePk();
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table ?? Str::snake(Str::pluralStudly(class_basename($this)));
    }

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName(): string
    {
        return $this->primaryKey;
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        return $this->Result;
    }

    /**
     * @return int
     */
    public function getRowCount(): int
    {
        return $this->RowCount;
    }

    /**
     * Set the primary key for the model.
     *
     * @param  string  $key
     * @return $this
     */
    public function setKeyName($key)
    {
        $this->primaryKey = $key;

        return $this;
    }

    /**
     * Qualify the given column name by the model's table.
     *
     * @param  string  $column
     * @return string
     */
    public function qualifyColumn($column)
    {
        if (Str::contains($column, '.')) {
            return $column;
        }

        return $this->getTable().'.'.$column;
    }

    /**
     * Get the table qualified key name.
     *
     * @return string
     */
    public function getQualifiedKeyName()
    {
        return $this->qualifyColumn($this->getKeyName());
    }


    /**
     * @param null $options
     * @return false|INT
     */
    public function save($options = null)
    {
        $this->extractData($options);

        $query = new Create;
        $query->insert($this->getTable(), $this->data);

        if ($query->getResult() !== null){
            return $query->getResult();
        }else{
            return false;
        }

    }

    /**
     * @param $ids
     * @param null $options
     * @return bool
     */
    public function update($ids, $options = null): bool
    {
        $this->extractData($options);

        exit(var_dump($this->data()));
        $query = new Update();
        $query->ExeUpdate($this->getTable(), $this->data(), "WHERE $this->primaryKey = :field", "field=$ids");

        if ($query->getResult()){
            return $query->getResult();
        }else{
            return false;
        }
    }

    public function delete($ids)
    {
        $query = new Delete();
        $query->ExeDelete($this->getTable(), "WHERE $this->primaryKey = :field", "field=$ids");
        return $query->getResult();
    }

    /**
     * Get all of the models from the database.
     * @param string[] $columns
     * @return mixed
     */
    public static function all($columns = ['*'])
    {
        return static::query()->get(
            is_array($columns) ? $columns : func_get_args()
        )->Result;
    }


    /**
     * @param $fields
     * @return Builder
     */
    public function isNull($fields)
    {
        if (!is_array($fields)){
            $fields = [...func_get_args()];
        }

        foreach ($fields as $key => $value):
            $cond[] = $value . ' IS NULL ';
        endforeach;
        $cond = implode(' AND ', $cond);

        return static::query()->where($cond);
    }

    public function isNotNull($fields)
    {
        if (!is_array($fields)){
            $fields = [...func_get_args()];
        }

        foreach ($fields as $key => $value):
            $cond[] = $value . ' IS NOT NULL ';
        endforeach;
        $cond = implode(' AND ', $cond);

        return static::query()->where($cond);
    }

    public static function query()
    {
        return (new static)->newQuery();
    }

    public function newQuery()
    {
        return new Builder($this);
    }


    /**
     * @param $options
     */
    public function extractData($options = null): void
    {
        if (!empty($this->attrFileRequest($this->fillable()))) {
            $this->data = $this->attrFileRequest($this->fillable());
        } else {
            $this->data = $this->fillable();
        }

        if ($options !== null) {
            $this->data = array_merge($this->fillable(), $options);
        }


        $this->data = array_filter($this->data);
    }


    /**
     * @param null $width
     * @param null $height
     */
    public function size($width = null, $height = null)
    {
        (new Request())->size($width, $height);
        return $this;
    }

    public function thumbnail($w, $h, $path = '')
    {
        (new Request())->thumbnail($w, $h, $path);
        return $this;
    }

    /**
     * @param string $path
     */
    public function store($path = ''): Model
    {
        $this->upload($path);
        return $this;
    }

    /**
     * @return array|mixed
     */
    private function data()
    {

        if(!empty($this->attrFileRequest($this->fillable()))){
            $this->data = $this->attrFileRequest($this->fillable());
        }else{
            $this->data = $this->fillable();
        }
        $this->data = array_filter($this->data);

        return $this->data;

    }


    public function uniqueChecker($uniqueAttr, $value): bool
    {
        $query = static::query()->where([[$uniqueAttr, '=', $value]])->get();

        if (!empty($query)){
            $this->Result       = $query->Result;
            $this->RowCount     = $query->RowCount;
            return true;
        }else{
            return false;
        }
    }


    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, ['increment', 'decrement'])) {
            return $this->$method(...$parameters);
        }

        return $this->forwardCallTo( (new static)->newQuery(), $method, $parameters);
    }


    /**
     * Handle dynamic static method calls into the model.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public static function __callStatic(string $method, array $parameters)
    {
        return (new static)->$method(...$parameters);
    }




}