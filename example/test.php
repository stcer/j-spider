<?php

use jcom\spider\PHtml;

$vendorPath = realpath(__DIR__ . "/../vendor/");
$loader = include($vendorPath . "/autoload.php");

function test_list(){
    $url = 'http://www.bmlink.com/news/list-26-1.html';
    $rules = array(
        '<div class="news_list2"> | <div class="page">',
        '#L__<ul>', // to array()
        '<a href=" | "',
        'url',
        );

    $phtml = new PHtml();
    $phtml->setUrl($url);
    $phtml->setRules($rules);

    return $phtml->fetch();
}

function test_info(){
    $url = 'http://www.bmlink.com/news/796741.html';
    $rules = array(
        'ft' => array(
            '<div class="newsinfo"> | <div class="keyword">'
            ),
        'f' => array(
            'content' => array(
                '<div class="newsinfo_cont"> | </div>',
                '<p> : </p>',
                'content' // filter
                ),
            'title' => array(
                '<h1 class="c_red"> | </h1>',
                ),
            'pdate' => '<span > | <',
            'cfrom' => 'À´Ô´£º | &nbsp;'
            )
        );
    $phtml = new PHtml();
    $phtml->setUrl($url);
    $phtml->setRules($rules);
    return $phtml->fetch();
}

function test_resource(){
    $url = 'http://www.jc001.cn';
    $phtml = new PHtml();
    $phtml->setUrl($url);
    return $phtml->fetch();
}

function test_filter(){
    $url = 'http://www.jc001.cn';
    $phtml = new PHtml();
    $phtml->setUrl($url);
    
    $rules = array(
        'ft' => array(
            'test_filter_diy'
            ),
        'f' => array()
        );
    $phtml->setRules($rules);
    
    return $phtml->fetch();
}

function test_filter_diy($content, $url){
    return "this is diy callback\n";
}

function test($type){
    switch ($type) {
        case 'list':
            $data = test_list();
            break;
        case 'info' :
            $data = test_info();
            break;
        case 'filter' :
            $data = test_filter(); 
            break;
        default :
            $data = test_resource(); 
    }
    echo "<pre>";
    print_r($data);
    echo "</pre>\n";
}

/**
* debug start
*/
test('list');
//test('info');
//test('filter');