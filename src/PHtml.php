<?php

namespace jcom\spider;

use Exception;

/**
 * Class PCommon
 * @package j\spider
 * @var PReader $reader
 */
class PCommon {
    
    protected $url;
    
    function __construct($url = ''){
        if($url)
            $this->setUrl($url);
    }
    
    function __get($key){
        switch ($key) {
            case 'reader':
                $this->reader = PReader::getInstance('curl');
                return $this->reader;
        }
    }
    
    // 设置网页地址
    function setUrl($url){
        $this->url = $url;
    }
    
    // 设置数据读取器
    function setReader($reader){
        $this->reader = $reader;
    }
    
    function setPostParams($params){
        $this->setOption('postParams', $params);
    }
    
    function setOption($k, $v) {
        if(method_exists($this->reader, 'setOption')){
            $this->reader->setOption($k, $v);
        }
    }
    
    function read(){
        return $this->reader->read($this->url);
    }
}


/**
 * Class PHtml
 * @package j\spider
 * @property PRuler $ruler
 */
class PHtml extends PCommon{
    function __get($key){
        switch ($key) {
            case 'ruler':
                $this->ruler = new PRuler();
                return $this->ruler;
        }
        return parent::__get($key);
    }
    
    function setRules($rules){
        $this->ruler->setRules($rules);
    }
    
    /**
    * put your comment there...
    * 
    */
    function fetch(){
        if(!isset($this->url) || !$this->url){
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
    function filter($content, $ruler){
        // apply filter
        $filters = $ruler->getFilters();
        $content = $filters->apply($content, $this->url);
        $content = $this->fix($content);
        
        // get fields
        $fields = $ruler->getFields();
        if($fields){
            $data = array();
            foreach ($fields as $field) {
                $fruler = $ruler->getFiledRuler($field);
                if($fruler){
                    $data[$field]  = $this->filter($content, $fruler);
                    $data[$field]  = $this->fix($data[$field]);
                }
            }
            return $data;
        }
        
        return $content;
    }
    
    function fix($data){
        if(is_string($data)){
            return trim($data);
        }
        
        if(!is_array($data)) {
            return $data;
        }
        
        foreach($data as $key => $val){
            if(empty($val)){
                unset($data[$key]);
            }elseif(is_array($val)){
                if($var = $this->fix($val)){
                    $data[$key] = $var;
                }else{
                    unset($data[$key]);
                }
            }else{
                $data[$key] = trim($val);
            }
        }
        return $data;
    }
}

class PImage extends PCommon{
    var $savePath;
    var $webPath;
    
    function getInstance(){
        return new self();
    }

    /**
     * PImage constructor.
     * @param string $url
     * @param string $savePath
     * @param string $webPath
     */
    function __construct($url = '', $savePath = 'images/', $webPath = 'images/'){
        parent::__construct($url);
        if(empty($savePath)){
            $savePath = 'images/';
        }
        $this->setSavePath($savePath, $webPath);
    }
    
    function setSavePath($path, $webPath){
        $this->webPath = $webPath;
        $this->savePath = $path;
        if(!preg_match('/\/$/', $path)){
            $this->savePath .= '/';
        }
        if(!file_exists($path)){
            mkdir($path, 0777);
        }
    }
    
    function fetch(){
        $fileName =  basename($this->url);
        $dir = date('Y') . '/';
        $dir .= substr(strtolower(md5($fileName)), 0, 2) . "/";
        $dirFull = $this->savePath . $dir;
        
        $file = $dirFull . $fileName;
        $webFile = $this->webPath . $dir . $fileName;
        if(!file_exists($file)){
            $content = $this->read();
            if($content){
                if(!file_exists($dirFull)){
                    mkdir($dirFull, 0777);
                }
                $fp = fopen($file, 'wb'); 
                fwrite($fp, $content); 
                fclose($fp);
                
                unset($content);
                return $webFile;
            }else{
                return '';
            }
        }
        return $webFile;
    }
}
