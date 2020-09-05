<?php
# PFilterBase.php

/**
 * User: Administrator
 * Date: 2020/9/5 0005
 * Time: 下午 14:49
 */
namespace jcom\spider\filter;

/**
 * 滤镜接口
 */
abstract class PFilterBase
{
    public function filter($content, $url)
    {
        if (is_array($content)) {
            foreach ($content as $key => $val) {
                if ($val) {
                    $content[$key] = $this->filter($val, $url);
                }
            }
        } else {
            $content = $this->apply($content, $url);
        }
        return $content;
    }

    public abstract function apply($content, $url);
}
