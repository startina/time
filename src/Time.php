<?php
namespace startina\time;

class Time
{
    /**
     * 时间戳获取指定时区的时间
     * @param $format string 时间戳格式 例 Y-m-d H:i:s
     * @param null|int|string $timestamp 时间戳/秒 10位整型
     * @param null|string $utc 时区
     * @return false|string
     */
    public function date(string $format, ?int $timestamp = null, $timezone = null)
    {
        $timestamp = $timestamp ?? time();
        $timeTrans = $timestamp - $this->getTimezoneOffset($timezone);
        return date($format, $timeTrans);
    }

    /**
     * 获取现有时区 - 指定时区的时间差（s）
     * @param null|int|string $timezone 时区
     * @return int
     */
    public function getTimezoneOffset($timezone = null)
    {
        if (!$timezone) {
            $timezone = date_default_timezone_get();
        }
        return date('Z') - date_offset_get(date_create(date('Y-m-d H:i:s'), timezone_open($timezone)));
    }

    /**
     * strtotime 拓展。支持时区
     * @param string $time 被解析的字符串，格式根据 GNU » 日期输入格式 的语法
     * @param int|null $now  用来计算返回值的时间戳
     * @param null|int|null $timezone 指定时区
     * @return false|int
     */
    public function strtotime(string $time, ?int $now = null, $timezone = null)
    {
        $now = $now ?? time();
        return strtotime($time, $now) + $this->getTimezoneOffset($timezone);
    }

    /**************************************************************************************************/
    /******************************************** DAY ***********************************************/
    /**************************************************************************************************/

    /**
     * 获取指定时区X日开始和结束的时间戳
     * @param int|null $days  0:今日 +1:明天 -1：昨天 其它类推
     * @param string|int|null $timezone
     * @return array
     */
    public function day(?int $days = 0, $timezone = null)
    {
        $timestamp = $this->strtotime("+{$days} day");
        $start = $this->strtotime($this->date('Y-m-d', $timestamp, $timezone), $timestamp, $timezone);
        $end = $start + 3600 * 24 - 1;
        return [$start, $end];
    }

    /**
     * 获取指定时区今日开始和结束的时间戳
     * @param null|int|string $timezone 时区
     * @return array [开始，结束]
     */
    public function today($timezone = null)
    {
        return  $this->day(0, $timezone);
    }

    /**
     * 获取指定时区昨日开始和结束的时间戳
     * @param null|int|string $timezone 时区
     * @return array [开始，结束]
     */
    public function yesterday($timezone = null)
    {
        return $this->day(-1, $timezone);
    }

    /**
     * 获取指定时区昨日开始和结束的时间戳
     * @param null|int|string $timezone 时区
     * @return array [开始，结束]
     */
    public function tomorrow($timezone = null)
    {
        return $this->day(1, $timezone);
    }

    /**************************************************************************************************/
    /*******************************************  WEEK  ***********************************************/
    /**************************************************************************************************/

    /**
     * 获取指定时区X周开始和结束的时间戳
     * @param int|null $days  0:本周 +1:下周 -1：上周 其它类推
     * @param null|int|string $timezone
     * @return array
     */
    public function week(?int $weeks = 0, $timezone = null)
    {
        $timestamp = $this->strtotime("{$weeks} week");
        $w = $this->date('w', $timestamp, $timezone);
        $start = $this->strtotime("Monday", $timestamp, $timezone) - ($w > 1 ? 3600 * 24 * 7 : 0);
        $end = $start + 3600 * 24 * 7 - 1;
        return [$start, $end];
    }

    /**
     * 获取指定时区今周开始和结束的时间戳
     * @param null|int|string $timezone 时区
     * @return array [开始，结束]
     */
    public function thisWeek($timezone = null)
    {
        return $this->week(0, $timezone);
    }

    /**
     * 获取指定时区上周开始和结束的时间戳
     * @param null|int|string $timezone 时区
     * @return array [开始，结束]
     */
    public function lastWeek($timezone = null)
    {
        return $this->week(-1, $timezone);
    }

    /**
     * 获取指定时区上周开始和结束的时间戳
     * @param null|int|string $timezone 时区
     * @return array [开始，结束]
     */
    public function nextWeek($timezone = null)
    {
        return $this->week(1, $timezone);
    }

    /**************************************************************************************************/
    /*******************************************  WEEK  ***********************************************/
    /**************************************************************************************************/

    /**
     * 获取指定时区X周开始和结束的时间戳
     * @param int|null $days  0:本周 +1:下周 -1：上周 其它类推
     * @param null|int|string $timezone
     * @return array
     */
    public function month(?int $months = 0, $timezone = null)
    {
        $timestamp = $months? $this->strtotime("{$months} month"): time();
        $start = $this->strtotime($this->date('Y-m', $timestamp, $timezone), $timestamp, $timezone);
        $end = $this->strtotime($this->date('Y-m', $this->strtotime('next month', $timestamp, $timezone), $timezone), $timestamp, $timezone) - 1;
        return [$start, $end];
    }
    /**
     * 获取指定时区这个月开始和结束的时间戳
     * @param null|int|string $timezone 时区
     * @return array [开始，结束]
     */
    public function thisMonth($timezone = null)
    {
        return $this->month(0, $timezone);
    }

    /**
     * 获取指定时区上个月开始和结束的时间戳
     * @param null|int|string $timezone 时区
     * @return array [开始，结束]
     */
    public function lastMonth($timezone = null)
    {
        return $this->month(-1, $timezone);
    }

    /**
     * 获取指定时区下个月开始和结束的时间戳
     * @param null|int|string $timezone 时区
     * @return array [开始，结束]
     */
    public function nextMonth($timezone = null)
    {
        return $this->month(1, $timezone);
    }

    /**
     * 获取指定时区今年开始和结束的时间戳
     * @param null|int|string $timezone 时区
     * @return array [开始，结束]
     */
    public function year($timezone = null)
    {
        $start = $this->strtotime($this->date('Y-1-1', time(), $timezone), time(), $timezone);
        $end = $this->strtotime($this->date('Y-1-1', $this->strtotime('next year', time(), $timezone), $timezone), time(), $timezone) - 1;
        return [$start, $end];
    }

    /**
     * 获取指定时区去年开始和结束的时间戳
     * @param null|int|string $timezone 时区
     * @return array [开始，结束]
     */
    public function lastYear($timezone = null)
    {
        $start = $this->strtotime($this->date('Y-1-1', $this->strtotime('last year', time(), $timezone), $timezone), time(), $timezone);
        $end = $this->strtotime($this->date('Y-1-1', $this->strtotime('this year', time(), $timezone), $timezone), time(), $timezone) - 1;
        return [$start, $end];
    }


}
