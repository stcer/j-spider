<?php
# PFilterBetween.php

/**
 * User: Administrator
 * Date: 2020/9/5 0005
 * Time: 下午 14:49
 */

namespace jcom\spider\filter;

use jcom\spider\filter;

class PFilterBetween extends filter\PFilterBaseRang
{
    function getStopPos($content)
    {
        return strrpos($content, $this->stopChar);
    }
}
