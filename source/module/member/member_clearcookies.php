<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

define('NOROBOT', TRUE);

foreach($_COOKIE as $key=>$value){
 setCookie($key,"2",time()-31536000);
}

?>