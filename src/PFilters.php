<?php

namespace jcom\spider;

use jcom\spider\filter\PFilterBase;

/**
 * Class PFilters
 * @package j\spider
 */
class PFilters
{
    static private $cbMap = array();
    static private $cbMapIsLoad = false;
    protected $filters = array();

    function __construct($filters = array())
    {
        if ($filters) {
            $this->filters = $filters;
        }

        self::loadDefFilterMap();
    }

    public function __destruct()
    {
        foreach ($this->filters as $filter) {
            unset($filter);
        }
    }

    /**
     * @param $content
     * @param $url
     * @return array|mixed
     */
    function apply(&$content, $url)
    {
        foreach ($this->filters as $callback) {
            if (is_object($callback) && ($callback instanceof filter\PFilterBase)) {
                $content = $callback->filter($content, $url);
            } elseif (is_array($callback) || is_string($callback)) {
                $content = call_user_func($callback, $content, $url);
            }
        }
        return $content;
    }

    function addFilter($callback)
    {
        $this->filters[] = $callback;
    }

    function addFilterFromExp($express)
    {
        $filter = $express;
        if (is_string($express)) {
            if (strpos($express, '#L__') === 0) {
                // list from chars
                $tag = str_replace('#L__', '', $express);
                $filter = new filter\PFilterBaseArray($tag);

            } elseif (strpos($express, '#LE__') === 0) {
                // list from regExpress
                $tag = str_replace('#LE__', '', $express);
                $filter = new filter\PFilterListRegExpress($tag);

            } elseif (strpos($express, '#C__') === 0) {
                // callback function
                $filter = str_replace('#C__', '', $express);

            } elseif (strpos($express, '#E__') === 0) {
                // express
                $tag = str_replace('#E__', '', $express);
                $filter = new filter\PFilterBaseRegExpress($tag);

            } elseif (is_numeric(strpos($express, ' | '))) {
                // 顺序
                // start char -- stop char
                $tag = explode(' | ', $express, 2);
                $filter = new filter\PFilterBaseRang($tag[0], $tag[1]);

            } elseif (is_numeric(strpos($express, ' : '))) {
                // 两边
                // between start and stop char
                $tag = explode(' : ', $express, 2);
                $filter = new filter\PFilterBetween($tag[0], $tag[1]);

            } elseif (is_numeric(strpos($express, ' :: '))) {
                // 反序
                // between start and stop char
                $tag = explode(' :: ', $express, 2);
                $filter = new filter\PFilterRRang($tag[0], $tag[1]);

            } elseif (is_numeric(strpos($express, ' !| '))) {
                // start char -- stop char, not need match
                $tag = explode(' !| ', $express, 2);
                $filter = new filter\PFilterBaseRang($tag[0], $tag[1], false);

            } elseif (is_numeric(strpos($express, ' !: '))) {
                // between start and stop char, not need match
                $tag = explode(' !: ', $express, 2);
                $filter = new filter\PFilterBetween($tag[0], $tag[1], false);

            } elseif (is_numeric(strpos($express, ' !:: '))) {
                // between start and stop char, not need match
                $tag = explode(' !:: ', $express, 2);
                $filter = new filter\PFilterRRang($tag[0], $tag[1], false);

            } elseif (isset(self::$cbMap[$express])) {
                // 注册
                $filter = self::$cbMap[$express];
            }
        }
        $this->filters[] = $filter;
    }

    /**
     * put your comment there...
     *
     */
    protected static function loadDefFilterMap()
    {
        if (self::$cbMapIsLoad) {
            return;
        }
        self::$cbMapIsLoad = true;
        self::$cbMap['url'] = new filter\PFilterBaseUrl();
        self::$cbMap['img'] = new filter\PFilterBaseImg();
        self::$cbMap['imgs'] = new filter\PFilterImgs();
        self::$cbMap['content'] = new filter\PFilterBaseContent();
    }

    static function regFilter($key, $callback)
    {
        self::$cbMap[$key] = $callback;
    }

    static function unFilter($key)
    {
        unset(self::$cbMap[$key]);
    }

    /**
     * @param $key
     * @return PFilterBase
     */
    static function getFilter($key)
    {
        if (!self::$cbMap) {
            self::loadDefFilterMap();
        }

        if (isset(self::$cbMap[$key])) {
            return self::$cbMap[$key];
        }

        return null;
    }
}
