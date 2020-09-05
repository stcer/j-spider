<?php
# PFilterBaseImg.php

/**
 * User: Administrator
 * Date: 2020/9/5 0005
 * Time: 下午 14:47
 */

namespace jcom\spider\filter;

use jcom\spider\filter\PFilterBase;
use jcom\spider\PFilters;
use jcom\spider\parser\PImage;

/**
 * Class PFilterBaseImg
 * @package j\spider
 * @property PImage $imager
 */
class PFilterBaseImg extends PFilterBase
{
    function setImager($imger)
    {
        $this->imager = $imger;
    }

    /**
     * put your comment there...
     *
     * @return PImage
     */
    function getImager()
    {
        return $this->imager;
    }

    function __get($key)
    {
        switch ($key) {
            case 'imager':
                $this->imager = new PImage();
                return $this->imager;
        }
        return null;
    }

    /**
     * 保存图片
     * @param $url
     * @param $baseUrl
     * @return string
     */
    function apply($url, $baseUrl)
    {
        $urlFilter = PFilters::getFilter('url');
        $url = $urlFilter->apply($url, $baseUrl);
        $this->imager->setUrl($url);
        return $this->imager->fetch();
    }
}
