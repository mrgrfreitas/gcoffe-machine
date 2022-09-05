<?php


namespace app\Machine\Engine\Valve\Traits;


use app\Machine\Engine\Support\DateFormatter;
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
    protected array $time;

    public function hidesAttr()
    {
        $this->data = [];
        $request = new Request;

        /** SLUG */
        if (isset($this->slug)){

            if (array_key_exists($this->slug[1], $request->data())){
                $toSlug = $request->data()[$this->slug[1]];

                $slug = Str::slug($toSlug);
                $checkSlug = $this->checkUniqueValue($this->slug[1], $toSlug);

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

        /** TIMESTAMPS [ created - updated - deleted ] */
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

        /** TIMESTAMP [ created - updated ] */
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

        /** CREATED */
        if (isset($this->created)){
            if (!empty($this->created)){
                foreach ($this->created as $key => $value){
                    $this->data[$value] =  DateFormatter::timestamp();
                }

            }else{
                $this->data['created_at'] = DateFormatter::timestamp();
            }
        }

        /** UPDATED */
        if (isset($this->updated)){
            if (!empty($this->updated)){
                foreach ($this->updated as $key => $value){
                    $this->data[$value] =  DateFormatter::timestamp();
                }

            }else{
                $this->data['updated_at'] =  DateFormatter::timestamp();
            }
        }

        /** PUBLISHED */
        if (isset($this->published)){
            if (!empty($this->published)){
                foreach ($this->published as $key => $value){
                    $this->data[$value] =  DateFormatter::timestamp();
                }

            }else{
                $this->data['published_at'] =  DateFormatter::timestamp();
            }
        }

        /** TIME */
        if (isset($this->time)){
            if (!empty($this->time)){
                foreach ($this->time as $key => $value){
                    $this->data[$value] =  time();
                }

            }else{
                $this->data['time_stamp'] =  time();
            }
        }

        return $this->data;
    }
}