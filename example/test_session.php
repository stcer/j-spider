<?php

use j\spider\PHtml;

$url = 'http://php5.9z.cn/go.php?action=regist';

$phtml = new PHtml();
$phtml->setOption('cookieFile', __DIR__ . '/cookie.txt');
$phtml->setUrl($url);

echo $phtml->fetch();
