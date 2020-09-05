<?php
# PReaderFOpen.php

/**
 * User: Administrator
 * Date: 2020/9/5 0005
 * Time: 下午 14:53
 */

namespace jcom\spider\reader;

class PReaderFOpen
{
    function read($url)
    {
        $handle = @fopen($url, 'r');
        $content = '';
        if ($handle) {
            while (!feof($handle)) {
                $content .= fread($handle, 8192);
            }
            fclose($handle);
        }

        return $content;
    }
}
