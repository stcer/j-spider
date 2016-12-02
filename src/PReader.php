<?php

namespace jcom\spider;

/**
 * Class PReader
 * @package jcom\spider
 */
class PReader{
    private static $reader;

    /**
     * @param string $type
     * @return PReaderCurl
     */
    static function getInstance($type = 'default'){
        if(isset(self::$reader)){
            return self::$reader;
        }
        
        self::$reader = new PReaderCurl();
        return self::$reader;
    }
}

/**
* readers
*/
class PReaderCurl{
    var $_options = array();
    var $header = array();
    
    function setPostParams($params){
        $this->setOption('postParams', $params);
    }
    
    function setOption($k, $v) {
        $this->_options[$k] = $v;
    }
    
    function read($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate");
        curl_setopt($ch, CURLOPT_USERAGENT, "Baiduspider+(+http://www.baidu.com/search/spider.htm)");
        
        if(isset($this->_options['postParams']) 
            && ($postParams = $this->_options['postParams'])
            ){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
        }
        if(isset($this->_options['cookieFile']) 
            && ($cookie_jar = $this->_options['cookieFile'])
            ){
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_jar);
            curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookie_jar);
        }
        
        if(isset($this->_options['referer']) 
            && ($referer = $this->_options['referer'])){
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        }

        if(isset($this->_options['agent']) 
            && ($agent = $this->_options['agent'])){
            curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        }
        
        if(isset($this->_options['fllow']) 
            && ($fllow = $this->_options['fllow'])){
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        }
        
        $rs = curl_exec($ch);
        
        if(isset($this->_options['header']) 
            && $this->_options['header'] && !curl_errno($ch)
            ){
            $this->header = curl_getinfo($ch);
        }
        
        curl_close($ch);
        return $rs;
    }
}

class PReaderFOpen{
    function read($url){
        $handle = @fopen($url, 'r');
        $content = '';
        if($handle){
            while (!feof($handle)) {
                $content .= fread($handle, 8192);
            }
            fclose($handle);
        }
        
        return $content;
    }
}

