<?php

namespace jcom\spider;

use Exception;

/**
 * Class PHtml
 * @package j\spider
 * @property PRuler $ruler
 */
class PHtml extends parser\PCommon
{
    function __get($key)
    {
        switch ($key) {
            case 'ruler':
                $this->ruler = new PRuler();
                return $this->ruler;
        }
        return parent::__get($key);
    }

    function setRules($rules)
    {
        $this->ruler->setRules($rules);
    }

    /**
     * put your comment there...
     *
     */
    function fetch()
    {
        if (!isset($this->url) || !$this->url) {
            throw(new Exception('UrlGenerator is null'));
        }

        $html = $this->read();
        return $this->filter($html, $this->ruler);
    }

    /**
     * @param $content
     * @param PRuler $ruler
     * @return array
     */
    function filter($content, $ruler)
    {
        // apply filter
        $filters = $ruler->getFilters();
        $content = $filters->apply($content, $this->url);
        $content = $this->fix($content);

        // get fields
        $fields = $ruler->getFields();
        if ($fields) {
            $data = array();
            foreach ($fields as $field) {
                $fruler = $ruler->getFiledRuler($field);
                if ($fruler) {
                    $data[$field] = $this->filter($content, $fruler);
                    $data[$field] = $this->fix($data[$field]);
                }
            }
            return $data;
        }

        return $content;
    }

    function fix($data)
    {
        if (is_string($data)) {
            return trim($data);
        }

        if (!is_array($data)) {
            return $data;
        }

        foreach ($data as $key => $val) {
            if (empty($val)) {
                unset($data[$key]);
            } elseif (is_array($val)) {
                if ($var = $this->fix($val)) {
                    $data[$key] = $var;
                } else {
                    unset($data[$key]);
                }
            } else {
                $data[$key] = trim($val);
            }
        }
        return $data;
    }
}
