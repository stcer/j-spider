<?php
# PFilterBaseArray.php

/**
 * User: Administrator
 * Date: 2020/9/5 0005
 * Time: 下午 14:49
 */

namespace jcom\spider\filter;

use jcom\spider\filter;

class PFilterBaseArray extends filter\PFilterBase
{
    private $spChars;

    function __construct($spChars)
    {
        $this->spChars = $spChars;
    }

    function apply($content, $url)
    {
        return explode($this->spChars, $content);
    }
}
