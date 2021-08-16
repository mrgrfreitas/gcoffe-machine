<?php


namespace app\Machine\Engine\Helpers;


use Illuminate\Support\Facades\Date;

trait DateFormatter
{

    /**
     * @param $month
     * @return string
     */
    public static function monthToNumber($month)
    {
        if ($month == 'January'):
            $result = '01';
        elseif ($month == 'February'):
            $result = '02';
        elseif ($month == 'March'):
            $result = '03';
        elseif ($month == 'April'):
            $result = '04';
        elseif ($month == 'May'):
            $result = '05';
        elseif ($month == 'June'):
            $result = '06';
        elseif ($month == 'July'):
            $result = '07';
        elseif ($month == 'August'):
            $result = '08';
        elseif ($month == 'September'):
            $result = '09';
        elseif ($month == 'October'):
            $result = '10';
        elseif ($month == 'November'):
            $result = '11';
        elseif ($month == 'December'):
            $result = '12';
        endif;

        return $result;
    }

    /**
     * @param $date1
     * @param $date2
     * @return int
     */
    public static function diffDate($date1, $date2)
    {
        $dif = strtotime($date1) - strtotime($date2);
        return (int) floor($dif / (60 * 60 * 24));
    }

    public static function timestamp()
    {
        return Date::now()->toDateTimeString();
    }

}