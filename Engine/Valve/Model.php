<?php
declare(strict_types=1);


namespace app\Machine\Engine\Valve;

use app\Machine\Engine\Auth\Events\Verified;
use app\Machine\Engine\Cylinders\Create;
use app\Machine\Engine\Cylinders\Delete;
use app\Machine\Engine\Cylinders\Update;
use app\Machine\Engine\Support\FileAnalyzer;
use app\Machine\Engine\Support\Validator;
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
    use FileAnalyzer;
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
    protected $data;

    /** @var string */
    protected $statement;

    /** @var array  */
    protected array $Result;

    /** @var int  */
    protected int $RowCount;

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected int $perPage = 10;



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
    public function setKeyName($key): Model
    {
        $this->primaryKey = $key;

        return $this;
    }

    /**
     * Qualify the given column name by the model's table.
     *
     * @param string $column
     * @return string
     */
    public function qualifyColumn(string $column): string
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
    public function getQualifiedKeyName(): string
    {
        return $this->qualifyColumn($this->getKeyName());
    }


    /**
     * @param null $options
     * @return INT|void
     */
    public function save($options = null)
    {
        $this->extractData($options);

        $query = new Create();
        $query->insert($this->getTable(), $this->data);

        if ($query->getResult()){
            return $query->getResult();
        }else{
            echo $query->getError();
        }

    }

    public static function select($field)
    {
        return static::query()->select($field);
    }

    /**
     * @param $ids
     * @param null $options
     * @return bool
     */
    public function update($ids, $options = null): bool
    {
        $this->extractData($options);
        //exit(gDebug($this->data['temp_image']));
        if (isset($this->data['temp_image'])){

            $directories = array_diff( scandir(storage_path('files/public')), array('.', '..'));
            foreach ($directories as $dir){
                $file = storage_path('files\public\\').$dir.'\\'.$this->data['temp_image'];
                $file2 = storage_path('files/public/').$dir. DIRECTORY_SEPARATOR .$this->data['temp_image'];

                $fileDir = storage_path('files\public\\').$dir;

                if (file_exists($file) && !is_dir($file)):
                    unlink($file);
                elseif (file_exists($file2) && !is_dir($file2)):
                    unlink($file2);
                endif;

            }

            unset($_SESSION['temp_id']);
            unset($this->data['temp_image']);
        }

        $query = new Update();

        $query->ExeUpdate($this->getTable(), $this->data(), "WHERE $this->primaryKey = :field", "field=$ids");

        if ($query->getResult()){
            return $query->getResult();
        }else{
            return false;
        }
    }

    /**
     * @param $ids
     * @return mixed
     */
    public function delete($ids)
    {
        $query = new Delete();
        $query->ExeDelete($this->getTable(), "WHERE $this->primaryKey = :field", "field=$ids");
        return $query->getResult();
    }

    /**
     * Get all the records of models from the database.
     * @param string[] $columns
     * @return mixed
     */
    public static function all($columns = ['*'])
    {
        return static::query()->get(
            is_array($columns) ? $columns : func_get_args()
        );
    }

    /**
     *
     * Get the row count from the database.
     * @return int
     */
    public static function rowCount(): int
    {
        return count(self::all());
    }




    /**
     * @param $fields
     * @return Builder
     */
    public function isNull($fields): Builder
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

    /**
     * @param $fields
     * @return Builder
     */
    public function isNotNull($fields): Builder
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

    /**
     * @return Builder
     */
    public static function query(): Builder
    {
        return (new static())->newQuery();
    }

    /**
     * @return Builder
     */
    public function newQuery(): Builder
    {
        return new Builder($this);
    }


    /**
     * @param null $options
     * @return array
     */
    public function extractData($options = null): array
    {
        if (!empty($this->attrFileRequest($this->fillable()))) {
            $this->data = $this->attrFileRequest($this->fillable());
        } else {
            $this->data = $this->fillable();
        }

        if ($options !== null) {
            $this->data = array_merge($this->fillable(), $options);
        }

        return $this->data = array_filter($this->data);
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
     * @return $this
     */
    public function store(string $path = ''): Model
    {
        $this->upload($path);
        return $this;
    }

    /**
     * @return array
     */
    private function data(): array
    {

        if(!empty($this->attrFileRequest($this->fillable()))){
            $this->data = $this->attrFileRequest($this->fillable());
        }else{
            $this->data = $this->fillable();
        }
        $this->data = array_filter($this->data);

        return $this->data;

    }


    /**
     * Check if the value is unique on database table
     * @param $uniqueAttr
     * @param $value
     * @return bool
     */
    public function checkUniqueValue($uniqueAttr, $value): bool
    {
        $query = static::query()->where([[$uniqueAttr, '=', $value]])->get();

        if (!empty($query)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Get the number of models to return per page.
     *
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }


    /**
     * Handle dynamic method calls into the model.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        if (in_array($method, ['increment', 'decrement'])) {
            return $this->$method(...$parameters);
        }

        return $this->forwardCallTo( (new static())->newQuery(), $method, $parameters);
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
        return (new static())->$method(...$parameters);
    }




}