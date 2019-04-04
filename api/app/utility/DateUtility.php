<?php

namespace ExpressPHP\utility;

/**
 * The DateUtility class is used to provide formatting data required throughout the application
 * @author Pete Mann - peter.mann.design@gmail.com
 */
class DateUtility {

    private static $dateFormat1 = 'd/m/Y';

    private static $dateFormat2 = 'd-m-Y';

    private static $dateFormatStorage = 'Y-m-d';

    private static $dateFormatRegex = '/^\d{2}[\/-]\d{2}[\/-]\d{4}$/';

    private static $timeFormatRegex = '/^\d{1,2}\:\d{1,2}$/';

    /**
  	 * The __construct method is private because this is a static class
  	 */
    private function __construct() {}

    /**
     * The getDateFormat1 method is used to return a php date format string
     * @return string is a php date format string
     */
    public static function getDateFormat1() {
        return self::$dateFormat1;
    }

    /**
     * The getDateFormat2 method is used to return a php date format string
     * @return string is a php date format string
     */
    public static function getDateFormat2() {
        return self::$dateFormat2;
    }

    /**
     * The getDateFormatStorage method is used to return a date format required for
     * storing a date
     * @return string is a php date format string
     */
    public static function getDateFormatStorage() {
        return self::$dateFormatStorage;
    }

    /**
     * The getDateFormatRegex method is used for returning a regex string
     * @return string is a php regex string used for checking a date format
     */
    public static function getDateFormatRegex() {
        return self::$dateFormatRegex;
    }

    /**
     * The getTimeFormatRegex method is used for returning a regex string
     * @return string is a php regex string used for checking a time format
     */
    public static function getTimeFormatRegex() {
        return self::$timeFormatRegex;
    }

    /**
     * The isValidDateFormat method is used to check if a date string is a vaild and
     * supported format
     * @param $date
     * @return false|int
     */
    public static function isValidDateFormat($date) {
        return preg_match(self::getDateFormatRegex(), $date);
    }

    /**
     * The createDateTimeFromString method is used to create a DateTime object from
     * a date valid string
     * @param $dateString
     * @return bool|DateTime
     */
    public static function createDateTimeFromString($dateString) {
        $dateTime = DateTime::createFromFormat(self::getDateFormat1(), $dateString);
        return $dateTime == false ? DateTime::createFromFormat(self::getDateFormat2(), $dateString) : $dateTime;
    }

    /**
     * The formatDateStringForStorage method is used to format a string into a string
     * that can be stored in the database
     * @param $DateTime
     * @return mixed
     */
    public static function formatDateStringForStorage($dateString) {
        return self::createDateTimeFromString($dateString)->format(self::$dateFormatStorage);
    }

}

?>
