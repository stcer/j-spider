<?php
# PRuler.php

/**
 * User: Administrator
 * Date: 2020/9/5 0005
 * Time: 下午 14:47
 */

namespace jcom\spider;

/**
 * Ruler
 */
class PRuler
{
    protected $rules;
    protected $keyFields = 'f';
    protected $keyFilters = 'ft';

    function __construct($rules = array())
    {
        $this->setRules($rules);
    }

    function setRules($rules)
    {
        if (!is_array($rules)) {
            $rules = array($rules);
        }

        if (!isset($rules[$this->keyFields]) && !isset($rules[$this->keyFilters])) {
            $rules = array($this->keyFilters => $rules);
        }

        if (!isset($rules[$this->keyFields])) {
            $rules[$this->keyFields] = array();
        }

        if (!isset($rules[$this->keyFilters])) {
            $rules[$this->keyFilters] = array();
        }

        $this->rules = $rules;
    }

    /**
     * put your comment there...
     * @return PFilters
     */
    function getFilters()
    {
        $filters = new PFilters();
        if (isset($this->rules[$this->keyFilters])) {
            foreach ($this->rules[$this->keyFilters] as $express) {
                $filters->addFilterFromExp($express);
            }
        }
        return $filters;
    }

    function getFields()
    {
        return array_keys($this->rules[$this->keyFields]);
    }

    function getFiledRuler($filed)
    {
        if (isset($this->rules[$this->keyFields][$filed])) {
            return new self($this->rules[$this->keyFields][$filed]);
        } else {
            return null;
        }
    }
}
