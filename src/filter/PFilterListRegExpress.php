<?php
# PFilterListRegExpress.php

/**
 * User: Administrator
 * Date: 2020/9/5 0005
 * Time: 下午 14:46
 */

namespace jcom\spider\filter;

use jcom\spider\filter\PFilterBaseRegExpress;

class PFilterListRegExpress extends PFilterBaseRegExpress
{
    function apply($content, $url)
    {
        if (preg_match_all($this->pattern, $content, $list)) {
            return $list[1];
        }
        return array();
    }
}
