<?php
#AreaQuery.php created by stcer@jz at 2020/9/5 0005
namespace jcom\spider\Demo;

use jcom\spider\PHtml;
use jcom\spider\reader\PReaderCurl;

/**
 * Class AreaQuery
 * @package jcom\spider\Demo
 */
class AreaQuery
{
    public function find($phone, $cacheFile = '')
    {
        $cacheData = [];
        if (!$cacheFile) {
            $cacheFile = __DIR__ . '/phone_cache.txt';
        }

        if (!$cacheData && file_exists($cacheFile)) {
            $cache = file_get_contents($cacheFile);
            if ($cache) {
                $cacheData = unserialize($cache);
                if (!$cacheData) {
                    $cacheData = [];
                }
            }
        }

        if (isset($cacheData[$phone])) {
            return $cacheData[$phone];
        }

        $cacheData[$phone] = $this->fetch($phone);
        file_put_contents($cacheFile, serialize($cacheData));
        return $cacheData[$phone];
    }

    protected function fetch($phone)
    {
        $reader = new PReaderCurl();
        $reader->setOption('header', []);
        $url = 'https://shouji.51240.com/%s__shouji/';
        $url = sprintf($url, $phone);

        $phtml = new PHtml();
        $phtml->setReader($reader);
        $phtml->setUrl($url);
        $phtml->setRules([
            'ft' => array(
                '归属地</td> | </td>',
                '"> | '
            ),
        ]);
        return trim(preg_replace('/\d+/', '', $phtml->fetch()));
    }
}
