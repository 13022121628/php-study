<?php
$url='https://credit.wsjd.gov.cn/portal/creditpublicity/0109000000';

$html= file_get_contents($url);

dump($html);

?>