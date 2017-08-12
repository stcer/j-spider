<?php

namespace jcom\spider;

/**
 * Class PFilters
 * @package j\spider
 */
class PFilters{
    static private $cbMap = array();
    static private $cbMapIsLoad = false;
    protected $filters = array();
    
    function __construct($filters = array()){
        if($filters)
            $this->filters = $filters;
            
        self::loadDefFilterMap();
    }
    
    public function __destruct(){
        foreach ($this->filters as $filter) {
            unset($filter);
        }
    }

    /**
     * @param $content
     * @param $url
     * @return array|mixed
     */
    function apply(& $content, $url){
        foreach($this->filters as $callback){
            if(is_object($callback) && ($callback instanceof PFilter)){
                $content = $callback->filter($content, $url);
            }elseif(is_array($callback) || is_string($callback)){
                $content = call_user_func($callback, $content, $url);
            }
        }
        return $content;
    }
    
    function addFilter($callback){
        $this->filters[] = $callback;
    }
    
    function addFilterFromExp($express){
        $filter = $express;
        if(is_string($express)){
            if(strpos($express, '#L__') === 0){
            // list from chars
                $tag = str_replace('#L__', '', $express);
                $filter = new PFilterArray($tag);
                
            }elseif(strpos($express, '#LE__') === 0){
            // list from regExpress
                $tag = str_replace('#LE__', '', $express);
                $filter = new PFilterListRegExpress($tag);
                
            }elseif(strpos($express, '#C__') === 0){
            // callback function
                $filter = str_replace('#C__', '', $express); 
                
            }elseif(strpos($express, '#E__') === 0){
            // express
                $tag = str_replace('#E__', '', $express);
                $filter = new PFilterRegExpress($tag);
                
            }elseif(is_numeric(strpos($express, ' | '))){ 
            // 顺序
            // start char -- stop char
                $tag = explode(' | ', $express, 2); 
                $filter = new PFilterRang($tag[0], $tag[1]);
                
            }elseif(is_numeric(strpos($express, ' : '))){
            // 两边
            // between start and stop char
                $tag = explode(' : ', $express, 2);
                $filter = new PFilterBetween($tag[0], $tag[1]);
                
            }elseif(is_numeric(strpos($express, ' :: '))){
            // 反序
            // between start and stop char
                $tag = explode(' :: ', $express, 2);
                $filter = new PFilterRRang($tag[0], $tag[1]);
                
            }elseif(is_numeric(strpos($express, ' !| '))){
            // start char -- stop char, not need match
                $tag = explode(' !| ', $express, 2);
                $filter = new PFilterRang($tag[0], $tag[1], false);
                
            }elseif(is_numeric(strpos($express, ' !: '))){
            // between start and stop char, not need match
                $tag = explode(' !: ', $express, 2);
                $filter = new PFilterBetween($tag[0], $tag[1], false);
                
            }elseif(is_numeric(strpos($express, ' !:: '))){
            // between start and stop char, not need match
                $tag = explode(' !:: ', $express, 2);
                $filter = new PFilterRRang($tag[0], $tag[1], false);
                
            }elseif(isset(self::$cbMap[$express])){ 
                // 注册
                $filter = self::$cbMap[$express];
            }
        }
        $this->filters[] = $filter;
    }
    
    /**
    * put your comment there...
    * 
    */
    protected static function loadDefFilterMap(){
        if(self::$cbMapIsLoad){
            return;
        }
        self::$cbMapIsLoad = true;
        self::$cbMap['url'] = new PFilterUrl();
        self::$cbMap['img'] = new PFilterImg();
        self::$cbMap['imgs'] = new PFilterImgs();
        self::$cbMap['content'] = new PFilterContent();
    }
    
    static function regFilter($key, $callback){
        self::$cbMap[$key] = $callback;
    }
    
    static function unFilter($key){
        unset(self::$cbMap[$key]);
    }

    /**
     * @param $key
     * @return PFilter
     */
    static function getFilter($key){
        if(!self::$cbMap){
            self::loadDefFilterMap();
        }
        
        if(isset(self::$cbMap[$key])){
            return self::$cbMap[$key];
        }
        
        return null;
    }
}

/**
* 滤镜接口
*/
abstract class PFilter {
    public function filter($content, $url){
        if(is_array($content)){
            foreach($content as $key => $val){
                if($val){
                    $content[$key] = $this->filter($val, $url);
                }
            }
        }else{
            $content = $this->apply($content, $url);
        }
        return $content;
    }
    
    public abstract function apply($content, $url);
}

class PFilterRang extends PFilter {
    
    protected $startChar;
    protected $stopChar;
    protected $mustMatch;
    
    function __construct($start = '', $stop = '', $mustMatch = true){
        if(!$start && !$stop){
            throw(new \Exception('args is null'));
        }
        $this->startChar = $start;
        $this->stopChar = $stop;
        $this->mustMatch = $mustMatch;
    }
    
    function apply($content, $url) {
        $isMatch1 = $isMatch2 = false;
        if($this->startChar){
            $start = $this->getStartPos($content);
            if(is_numeric($start)){
                $content = substr($content, $start + strlen($this->startChar));
                $isMatch1 = true;
            }
        }else{
            $isMatch1 = true;
        }
        
        if($this->stopChar){
            $start = $this->getStopPos($content);
            if(is_numeric($start)){
                $content = substr($content, 0, $start);
                $isMatch2 = true;
            }
        }else{
            $isMatch2 = true;
        }
        
        if($this->mustMatch && !($isMatch1 && $isMatch2)){ // 全匹配
            return '';
        }
        
        return $content;
    }
    
    function getStartPos($content){
        return strpos($content, $this->startChar);
    }
    
    function getStopPos($content){
        return strpos($content, $this->stopChar);
    }
}

class PFilterBetween extends PFilterRang{
    function getStopPos($content){
        return strrpos($content, $this->stopChar);
    }
}

class PFilterRRang extends PFilterRang{
    function getStartPos($content){
        return strrpos($content, $this->startChar);
    }
    
    function getStopPos($content){
        return strrpos($content, $this->stopChar);
    }
}

class PFilterArray extends PFilter {
    private $spChars;
    function __construct($spChars){
        $this->spChars = $spChars;
    }
    
    function apply($content, $url) {
        return explode($this->spChars, $content);
    }
}

class PFilterRegExpress extends PFilter   {
    protected $pattern;
    function __construct($pattern){
        $this->pattern = $pattern;
    }
    
    function apply($content, $url) {
        if(preg_match($this->pattern, $content, $r)){
            return $r[1];
        }
        return '';
    }
}

class PFilterListRegExpress extends PFilterRegExpress {
    function apply($content, $url) {
        if(preg_match_all($this->pattern, $content, $list)){
            return $list[1];
        }
        return array();
    }
}

/**
 * static instance
 */
class PFilterUrl extends PFilter{
    function apply($url, $baseUrl) {
        if(!$url){
            return $url;
        }
        if(strpos($url, 'http://') !== 0){
            if(substr($url, 0, 1) === '/'){
                if(preg_match('#^((?:https?://)?[^/]+)#', $baseUrl, $r)){
                    $url = $r[1] . $url;
                }
            }elseif(substr($url, 0, 3) == '../'){
                $urlInfo = parse_url($baseUrl);
                $path = dirname(dirname($urlInfo['path']));
                $path == '\\' && $path = '/';
                $url = $urlInfo['scheme'] . '://' . $urlInfo['host'] . $path . substr($url, 3);
            }else{
                $url = preg_replace('/[^\/]+$/', '', $baseUrl) . $url;
            }
        }
        return $url;
    }
}

class PFilterContent extends PFilter{
    function apply($string, $url){
        $string = preg_replace('/<a.+?>(.+?)<\/a>/is', '$1', $string);
        $string =  preg_replace('/<script.+?<\/script\>/is', '', $string);
        $string =  preg_replace('/<iframe.+?<\/iframe\>/is', '', $string);
        
        $string = preg_replace('/<a.+?>/is', '', $string);
        $string = preg_replace('/<p[^>]+?><\/p>/is', '', $string);
        $string = preg_replace('/<font[^>]+?><\/font>/is', '', $string);
        $string = preg_replace('/<object.+?<\/object>/is', '', $string);
        $string = preg_replace('/<FORM.+?<\/FORM>/is', '', $string);
        $string = preg_replace('/<span style="display\: none">.+?span>/is', '', $string);
        return $string;
    }
}

/**
 * Class PFilterImg
 * @package j\spider
 * @property PImage $imager
 */
class PFilterImg extends PFilter{
    function setImager($imger){
        $this->imager = $imger;
    }
    
    /**
    * put your comment there...
    * 
    * @return PImage
    */
    function getImager(){
        return $this->imager;
    }
    
    function __get($key){
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
    function apply($url, $baseUrl){
        $urlFilter = PFilters::getFilter('url');
        $url = $urlFilter->apply($url, $baseUrl);
        $this->imager->setUrl($url);
        return $this->imager->fetch();
    }
}

class PFilterImgs extends PFilterImg{
    private static $imgs;
    private $autoRest = true;

    /**
     * 替换内容图片
     * @param $content
     * @param $baseUrl
     * @return mixed
     */
    function apply($content, $baseUrl){
        //preg_match_all('/src=["\']?((?:[^>"\']+?)\.(?:gif|jpg))/i', $content, $matches);
        preg_match_all('/src\s*=["\']?(.+?)[ \'">]/i', $content, $matches);
        $urls = $matches[1];
        $urls = array_unique($urls);

        $this->autoRest && $this->flush();
        
        $urlFilter = PFilters::getFilter('url');
        foreach ($urls as $key => $url ){
            // format img url
            $absUrl = $urlFilter->apply($url, $baseUrl);
            
            // save img
            $this->imager->setUrl($absUrl);
            $newUrl = $this->imager->fetch();

            // replace content to new img url
            if($newUrl){
                $content = str_ireplace($url, $newUrl, $content);
                self::$imgs[] = str_ireplace($this->imager->webPath, '', $newUrl);
            }
        }
        
        return $content;
    }
    
    function getImgs(){
        return self::$imgs;
    }
    
    function setAutoFlush($flag){
        $this->autoRest = $flag;
    }
    
    function flush(){
        self::$imgs = array();
    }
}

/**
* Ruler
*/
class PRuler{
    protected $rules;
    protected $keyFields = 'f';
    protected $keyFilters = 'ft';
    
    function __construct($rules = array()){
        $this->setRules($rules);
    }
    
    function setRules($rules){
        if(!is_array($rules)){
            $rules = array($rules);
        }
        
        if(!isset($rules[$this->keyFields]) && !isset($rules[$this->keyFilters])){
            $rules = array($this->keyFilters => $rules);
        }
        
        if(!isset($rules[$this->keyFields])){
            $rules[$this->keyFields] = array();
        }
        
        if(!isset($rules[$this->keyFilters])){
            $rules[$this->keyFilters] = array();
        }
        
        $this->rules = $rules;
    }
    
    /**
    * put your comment there...
    * @return PFilters
    */
    function getFilters(){
        $filters = new PFilters();
        if(isset($this->rules[$this->keyFilters])){
            foreach ($this->rules[$this->keyFilters] as $express) {
                $filters->addFilterFromExp($express);
            }
        }
        return $filters;
    }
    
    function getFields(){
        return array_keys($this->rules[$this->keyFields]);
    }
    
    function getFiledRuler($filed){
        if(isset($this->rules[$this->keyFields][$filed])){
            return new self($this->rules[$this->keyFields][$filed]);
        }else{
            return null;
        }
    }
}
