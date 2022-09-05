<?php


namespace app\Machine\Engine\Support;


use Illuminate\Support\Facades\Date;

class DateFormatter
{

    /**
     * @param $month
     * @return string
     */
    public static function monthToNumber($month): string
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
    public static function diffDate($date1, $date2): int
    {
        $dif = strtotime($date1) - strtotime($date2);
        return (int) floor($dif / (60 * 60 * 24));
    }

    public static function timestamp()
    {
        return Date::now()->toDateTimeString();
    }

    /**
     * @param $date1
     * @param $date2
     * @return array
     */
    public static function all_days_period($date1, $date2=null): array
    {

        // Declare two dates
        $Date1 = $date1;
        $Date2 = $date2;

        // Declare an empty array
        $array = array();

        // Use strtotime function
        $strDate1 = strtotime($Date1);
        $strDate2 = strtotime($Date2);

        // Use for loop to store dates into array
        // 86400 sec = 24 hrs = 60*60*24 = 1 day
        for ($currentDate = $strDate1; $currentDate <= $strDate2; $currentDate += (86400)):
            $Store = date('Y-m-d', $currentDate);
            $array[] = $Store;
        endfor;

        return $array;
    }

    /**
     * Increase days from the current date or a user-defined date
     * @param $days
     * @param $date
     * @return string
     */
    public static function IncreaseDaysToDate($days, $date = null): string
    {
        $date = $date ?? date('Y-m-d');
        $date = strtotime($date);
        $date = strtotime("+{$days} day", $date);

        return date('Y-m-d', $date);
    }

    public static function DatetimeAgo($time) {
        $time = strtotime($time);
        $cur_time = time();
        $time_elapsed = $cur_time - $time;
        $seconds = $time_elapsed;
        $minutes = round($time_elapsed / 60 );
        $hours = round($time_elapsed / 3600);
        $days = round($time_elapsed / 86400 );
        $weeks = round($time_elapsed / 604800);
        $months = round($time_elapsed / 2600640 );
        $years = round($time_elapsed / 31207680 );

        // Seconds
        if($seconds <= 60){
            echo "$seconds segundos atrás";
        }

        //Minutes
        else if($minutes <= 60){

            if($minutes == 1){
                echo "um minuto atrás";
            }
            else{
                echo "$minutes minutos atrás";
            }
        }

        //Hours
        else if($hours <= 24){

            if($hours == 1){
                echo "uma hora atrás";
            }else{
                echo "$hours horas atrás";
            }
        }

        //Days
        else if($days <= 7){

            if($days == 1){
                echo "ontem";
            }else{
                echo "$days dias atrás";
            }
        }

        //Weeks
        else if($weeks <= 4.3){

            if($weeks == 1){
                echo "uma semana atrás";
            }else{
                echo "$weeks semanas atrás";
            }
        }

        //Months
        else if($months <= 12){

            if($months == 1){
                echo "um mês atrás";
            }else{
                echo "$months meses atrás";
            }
        }

        //Years
        else{
            if($years == 1){
                echo "um ano atrás";
            }else{
                echo "$years anos atrás";
            }
        }
    }

    public static function dayMonthYear($DATE) {

        $days = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

        $months = ['January' => 'Janeiro',
            'February' => 'Fevereiro',
            'March' => 'Março',
            'April' => 'Abril',
            'May' => 'Maio',
            'June' => 'Junho',
            'July' => 'Julho',
            'August' => 'Agosto',
            'September' => 'Setembro',
            'October' => 'Outubro',
            'November' => 'Novembro',
            'December' => 'Dezembro'];

        $day = date('w', strtotime($DATE));

        $month = date('F', strtotime($DATE));

        $DMY = $days[$day] .', ' .date('d', strtotime($DATE)) .' de '.$months[$month].' de ' .date('Y', strtotime($DATE));

        return $DMY;
    }
    public static function monthYearFull($DATE) {

        //$days = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

        $months = ['January' => 'Janeiro',
            'February' => 'Fevereiro',
            'March' => 'Março',
            'April' => 'Abril',
            'May' => 'Maio',
            'June' => 'Junho',
            'July' => 'Julho',
            'August' => 'Agosto',
            'September' => 'Setembro',
            'October' => 'Outubro',
            'November' => 'Novembro',
            'December' => 'Dezembro'];

        //$day = date('w', strtotime($DATE));

        $month = date('F', strtotime($DATE));

        //$MonthYear = $days[$day] .', ' .date('d', strtotime($DATE)) .' de '.$months[$month].' de ' .date('Y', strtotime($DATE));
        $MonthYear = $months[$month].' de ' .date('Y', strtotime($DATE));

        return $MonthYear;
    }
    public static function monthYearShort($DATE) {

        $months = ['January' => 'Jan',
            'February' => 'Fev',
            'March' => 'Mar',
            'April' => 'Abr',
            'May' => 'Mai',
            'June' => 'Jun',
            'July' => 'Jul',
            'August' => 'Ago',
            'September' => 'Set',
            'October' => 'Out',
            'November' => 'Nov',
            'December' => 'Dez'];

        $month = date('F', strtotime($DATE));
        $MonthYear = $months[$month].'/' .date('Y', strtotime($DATE));

        return $MonthYear;
    }

    public static function age($DATE): int
    {
        $date = explode('-', $DATE);
        $bdy = (int) $date[0];
        $actualYear = (int) date('Y');

        return $actualYear - $bdy;
    }

}