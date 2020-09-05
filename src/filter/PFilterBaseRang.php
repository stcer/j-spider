<?php
# PFilterBaseRang.php

/**
 * User: Administrator
 * Date: 2020/9/5 0005
 * Time: 下午 14:48
 */

namespace jcom\spider\filter;

use jcom\spider\filter\PFilterBase;

class PFilterBaseRang extends PFilterBase
{

    protected $startChar;
    protected $stopChar;
    protected $mustMatch;

    function __construct($start = '', $stop = '', $mustMatch = true)
    {
        if (!$start && !$stop) {
            throw(new \Exception('args is null'));
        }
        $this->startChar = $start;
        $this->stopChar = $stop;
        $this->mustMatch = $mustMatch;
    }

    function apply($content, $url)
    {
        $isMatch1 = $isMatch2 = false;
        if ($this->startChar) {
            $start = $this->getStartPos($content);
            if (is_numeric($start)) {
                $content = substr($content, $start + strlen($this->startChar));
                $isMatch1 = true;
            }
        } else {
            $isMatch1 = true;
        }

        if ($this->stopChar) {
            $start = $this->getStopPos($content);
            if (is_numeric($start)) {
                $content = substr($content, 0, $start);
                $isMatch2 = true;
            }
        } else {
            $isMatch2 = true;
        }

        if ($this->mustMatch && !($isMatch1 && $isMatch2)) { // 全匹配
            return '';
        }

        return $content;
    }

    function getStartPos($content)
    {
        return strpos($content, $this->startChar);
    }

    function getStopPos($content)
    {
        return strpos($content, $this->stopChar);
    }
}
