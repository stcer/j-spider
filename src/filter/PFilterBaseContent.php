<?php
# PFilterBaseContent.php

/**
 * User: Administrator
 * Date: 2020/9/5 0005
 * Time: 下午 14:47
 */

namespace jcom\spider\filter;

use jcom\spider\filter\PFilterBase;

class PFilterBaseContent extends PFilterBase
{
    function apply($string, $url)
    {
        $string = preg_replace('/<a.+?>(.+?)<\/a>/is', '$1', $string);
        $string = preg_replace('/<script.+?<\/script\>/is', '', $string);
        $string = preg_replace('/<iframe.+?<\/iframe\>/is', '', $string);

        $string = preg_replace('/<a.+?>/is', '', $string);
        $string = preg_replace('/<p[^>]+?><\/p>/is', '', $string);
        $string = preg_replace('/<font[^>]+?><\/font>/is', '', $string);
        $string = preg_replace('/<object.+?<\/object>/is', '', $string);
        $string = preg_replace('/<FORM.+?<\/FORM>/is', '', $string);
        $string = preg_replace('/<span style="display\: none">.+?span>/is', '', $string);
        return $string;
    }
}
