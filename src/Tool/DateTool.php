<?php                                      
                                                     
namespace App\Tool;

/**
 * Static methods to manipulate date.
 */
class DateTool
{
    /**
     * Get datetime for now according to timezone and format.
     *
     * @return \DateTime|false
     */
    public static function datetimeNow()
    {
        return
            \DateTime::createFromFormat('U.u', number_format(microtime(true), 6, '.', ''))
            ->setTimeZone(new \DateTimeZone(date_default_timezone_get()))
            ->setTime(0, 0);
    }

    public static function dateAndTimeNow()
    {
        return
            \DateTime::createFromFormat('U.u', number_format(microtime(true), 6, '.', ''))
                ->setTimeZone(new \DateTimeZone(date_default_timezone_get()));
    }
}
