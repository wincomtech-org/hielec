<?php
if($_GET['mod'] == 'register') {
	$_GET['mod'] = 'connect';
	$_GET['action'] = 'register';
	require_once 'member.php';
	exit;
}

define('APPTYPEID', 126);
define('CURSCRIPT', 'connect');

require_once './source/class/class_core.php';
require_once './source/function/function_home.php';

$discuz = C::app();

$mod = $discuz->var['mod'];
$discuz->init();
require_once DISCUZ_ROOT .'public.php';

if(!in_array($mod, array('config', 'login', 'feed', 'check', 'user'))) {
	showmessage('undefined_action');
}

if(!$_G['setting']['connect']['allow']) {
	showmessage('qqconnect:qqconnect_closed');
}

define('CURMODULE', $mod);
runhooks();

$connectService = Cloud::loadClass('Service_Connect');
require_once libfile('connect/'.$mod, 'plugin/qqconnect');
?>