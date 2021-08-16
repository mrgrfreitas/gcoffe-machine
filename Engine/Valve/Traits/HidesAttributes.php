<?php


namespace app\Machine\Engine\Valve\Traits;


use app\Machine\Engine\Helpers\DateFormatter;
use app\Machine\Request;
use Illuminate\Support\Str;

trait HidesAttributes
{
    protected array $slug;
    protected array $timestamps;
    protected array $timestamp;
    protected array $created;
    protected array $updated;
    protected array $published;

    public function hidesAttr()
    {
        $this->data = [];
        $request = new Request;
        if (isset($this->slug)){

            if (array_key_exists($this->slug[1], $request->data())){
                $toSlug = $request->data()[$this->slug[1]];

                $slug = Str::slug($toSlug);
                $checkSlug = $this->uniqueChecker($this->slug[1], $toSlug);

                if ($checkSlug){
                    if ($this->getRowCount() == 0){
                        $this->data[$this->slug[0]] = $slug;
                    }else{
                        $this->data[$this->slug[0]] = $slug . '-' . $this->getRowCount();
                    }

                }else{
                    $this->data[$this->slug[0]] = $slug;
                }
            }
        }

        if (isset($this->timestamps)){
            if (!empty($this->timestamps)){
                foreach ($this->timestamps as $key => $value){
                    $this->data[$value] =  DateFormatter::timestamp();
                }

            }else{
                $this->data['created_at'] =  DateFormatter::timestamp();
                $this->data['updated_at'] =  DateFormatter::timestamp();
                $this->data['deleted_at'] =  DateFormatter::timestamp();
            }
        }

        if (isset($this->timestamp)){
            if (!empty($this->timestamp)){
                foreach ($this->timestamp as $key => $value){
                    $this->data[$value] =  DateFormatter::timestamp();
                }

            }else{
                $this->data['created_at'] =  DateFormatter::timestamp();
                $this->data['updated_at'] =  DateFormatter::timestamp();
            }
        }

        if (isset($this->created)){
            if (!empty($this->created)){
                foreach ($this->created as $key => $value){
                    $this->data[$value] =  DateFormatter::timestamp();
                }

            }else{
                $this->data['created_at'] = DateFormatter::timestamp();
            }
        }

        if (isset($this->updated)){
            if (!empty($this->updated)){
                foreach ($this->updated as $key => $value){
                    $this->data[$value] =  DateFormatter::timestamp();
                }

            }else{
                $this->data['updated_at'] =  DateFormatter::timestamp();
            }
        }

        if (isset($this->published)){
            if (!empty($this->published)){
                foreach ($this->published as $key => $value){
                    $this->data[$value] =  DateFormatter::timestamp();
                }

            }else{
                $this->data['published_at'] =  DateFormatter::timestamp();
            }
        }

        return $this->data;
    }
}