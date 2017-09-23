<?php
define('APPTYPEID', 1);
define('CURSCRIPT', 'home');

if(!empty($_GET['mod']) && ($_GET['mod'] == 'misc' || $_GET['mod'] == 'invite')) {
	define('ALLOWGUEST', 1);
}

require_once './source/class/class_core.php';
require_once './source/function/function_home.php';
$discuz = C::app();// 原来是把discuz_application对象赋值给变量$discuz。
$cachelist = array('magic','userapp','usergroups', 'diytemplatenamehome');
$discuz->cachelist = $cachelist;
$discuz->init();// 哇靠，$discuz->init();这一行才是核心中的核心，具体功能是初始化整个discuz应用。discuz_application类是整个discuz的应用初始化类，相当于织梦的 /include/common.inc.php的功能

// 避免跳到 广播界面
$_GET['do'] = isset($_GET['do'])?$_GET['do']:'index';
require_once DISCUZ_ROOT .'public.php';

$space = array();
$mod = getgpc('mod');
// $do = getgpc('do');

// 需要一开始就验证用户的
$allow_plugin_route = array('project','trade','down','datasheet','activity');
if (isset($mod) && in_array($mod,$allow_plugin_route) && empty($gUid)) {
    dheader('location:'.$_G['siteurl'].'home.php?mod=space&do=profile');
}

if(!in_array($mod, array('space', 'spacecp', 'misc', 'magic', 'editor', 'invite', 'task', 'medal', 'rss', 'follow', 'project','trade','down','datasheet','activity','zone','uc'))) {
	$mod = 'space';
	$_GET['do'] = 'home';
}
if($mod == 'space' && ((empty($_GET['do']) || $_GET['do'] == 'index') && $_G['inajax'])) {
	$_GET['do'] = 'profile';
}
$curmod = !empty($_G['setting']['followstatus']) && (empty($_GET['diy']) && empty($_GET['do']) && $mod == 'space' || $_GET['do'] == 'follow') ? 'follow' : $mod;
define('CURMODULE', $curmod);
runhooks($_GET['do'] == 'profile' && $_G['inajax'] ? 'card' : $_GET['do']);



define('TEMPDIR', 'home/'.$mod.'/'.$mod.'_'.$_GET['do']);
require_once libfile('home/'.$mod, 'module');
?>