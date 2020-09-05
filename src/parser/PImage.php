<?php
# PImage.php

/**
 * User: Administrator
 * Date: 2020/9/5 0005
 * Time: 下午 14:51
 */
namespace jcom\spider\parser;

use jcom\spider\parser;

class PImage extends parser\PCommon
{
    var $savePath;
    var $webPath;

    function getInstance()
    {
        return new self();
    }

    /**
     * PImage constructor.
     * @param string $url
     * @param string $savePath
     * @param string $webPath
     */
    function __construct($url = '', $savePath = 'images/', $webPath = 'images/')
    {
        parent::__construct($url);
        if (empty($savePath)) {
            $savePath = 'images/';
        }
        $this->setSavePath($savePath, $webPath);
    }

    function setSavePath($path, $webPath)
    {
        $this->webPath = $webPath;
        $this->savePath = $path;
        if (!preg_match('/\/$/', $path)) {
            $this->savePath .= '/';
        }
        if (!file_exists($path)) {
            mkdir($path, 0777);
        }
    }

    function fetch()
    {
        $fileName = basename($this->url);
        $dir = date('Y') . '/';
        $dir .= substr(strtolower(md5($fileName)), 0, 2) . "/";
        $dirFull = $this->savePath . $dir;

        $file = $dirFull . $fileName;
        $webFile = $this->webPath . $dir . $fileName;
        if (!file_exists($file)) {
            $content = $this->read();
            if ($content) {
                if (!file_exists($dirFull)) {
                    mkdir($dirFull, 0777);
                }
                $fp = fopen($file, 'wb');
                fwrite($fp, $content);
                fclose($fp);

                unset($content);
                return $webFile;
            } else {
                return '';
            }
        }
        return $webFile;
    }
}
