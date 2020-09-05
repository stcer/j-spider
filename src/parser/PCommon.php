<?php
# PCommon.php

/**
 * User: Administrator
 * Date: 2020/9/5 0005
 * Time: 下午 14:51
 */
namespace jcom\spider\parser;

use jcom\spider\PReader;

/**
 * Class PCommon
 * @package j\spider
 * @var PReader $reader
 */
class PCommon
{

    protected $url;

    function __construct($url = '')
    {
        if ($url) {
            $this->setUrl($url);
        }
    }

    function __get($key)
    {
        switch ($key) {
            case 'reader':
                $this->reader = PReader::getInstance('curl');
                return $this->reader;
        }
    }

    // 设置网页地址
    function setUrl($url)
    {
        $this->url = $url;
    }

    // 设置数据读取器
    function setReader($reader)
    {
        $this->reader = $reader;
    }

    function setPostParams($params)
    {
        $this->setOption('postParams', $params);
    }

    function setOption($k, $v)
    {
        if (method_exists($this->reader, 'setOption')) {
            $this->reader->setOption($k, $v);
        }
    }

    function read()
    {
        return $this->reader->read($this->url);
    }
}
