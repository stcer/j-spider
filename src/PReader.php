<?php

namespace jcom\spider;

use jcom\spider\reader\PReaderCurl;

/**
 * Class PReader
 * @package jcom\spider
 */
class PReader
{
    private static $reader;

    /**
     * @param string $type
     * @return PReaderCurl
     */
    static function getInstance($type = 'default')
    {
        if (isset(self::$reader)) {
            return self::$reader;
        }

        self::$reader = new reader\PReaderCurl();
        return self::$reader;
    }
}

