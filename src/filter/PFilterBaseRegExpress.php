<?php
# PFilterBaseRegExpress.php

/**
 * User: Administrator
 * Date: 2020/9/5 0005
 * Time: 下午 14:47
 */

namespace jcom\spider\filter;

use jcom\spider\filter\PFilterBase;

class PFilterBaseRegExpress extends PFilterBase
{
    protected $pattern;

    function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    function apply($content, $url)
    {
        if (preg_match($this->pattern, $content, $r)) {
            return $r[1];
        }
        return '';
    }
}
