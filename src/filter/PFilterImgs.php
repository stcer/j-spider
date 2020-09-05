<?php
# PFilterImgs.php

/**
 * User: Administrator
 * Date: 2020/9/5 0005
 * Time: 下午 14:47
 */

namespace jcom\spider\filter;

use jcom\spider\filter;
use jcom\spider\PFilters;

class PFilterImgs extends filter\PFilterBaseImg
{
    private static $imgs;
    private $autoRest = true;

    /**
     * 替换内容图片
     * @param $content
     * @param $baseUrl
     * @return mixed
     */
    function apply($content, $baseUrl)
    {
        //preg_match_all('/src=["\']?((?:[^>"\']+?)\.(?:gif|jpg))/i', $content, $matches);
        preg_match_all('/src\s*=["\']?(.+?)[ \'">]/i', $content, $matches);
        $urls = $matches[1];
        $urls = array_unique($urls);

        $this->autoRest && $this->flush();

        $urlFilter = PFilters::getFilter('url');
        foreach ($urls as $key => $url) {
            // format img url
            $absUrl = $urlFilter->apply($url, $baseUrl);

            // save img
            $this->imager->setUrl($absUrl);
            $newUrl = $this->imager->fetch();

            // replace content to new img url
            if ($newUrl) {
                $content = str_ireplace($url, $newUrl, $content);
                self::$imgs[] = str_ireplace($this->imager->webPath, '', $newUrl);
            }
        }

        return $content;
    }

    function getImgs()
    {
        return self::$imgs;
    }

    function setAutoFlush($flag)
    {
        $this->autoRest = $flag;
    }

    function flush()
    {
        self::$imgs = array();
    }
}
