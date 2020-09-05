<?php
# PFilterBaseUrl.php

/**
 * User: Administrator
 * Date: 2020/9/5 0005
 * Time: 下午 14:46
 */

namespace jcom\spider\filter;

use jcom\spider\filter\PFilterBase;

/**
 * static instance
 */
class PFilterBaseUrl extends PFilterBase
{
    function apply($url, $baseUrl)
    {
        if (!$url) {
            return $url;
        }
        if (strpos($url, 'http://') !== 0) {
            if (substr($url, 0, 1) === '/') {
                if (preg_match('#^((?:https?://)?[^/]+)#', $baseUrl, $r)) {
                    $url = $r[1] . $url;
                }
            } elseif (substr($url, 0, 3) == '../') {
                $urlInfo = parse_url($baseUrl);
                $path = dirname(dirname($urlInfo['path']));
                $path == '\\' && $path = '/';
                $url = $urlInfo['scheme'] . '://' . $urlInfo['host'] . $path . substr($url, 3);
            } else {
                $url = preg_replace('/[^\/]+$/', '', $baseUrl) . $url;
            }
        }
        return $url;
    }
}
