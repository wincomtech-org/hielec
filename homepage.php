<?php
/**
 *      [] (C)2001-2099 Comsenz Inc.
 *      $Id: homepage.php 33828 2016-03-07 12:29:32 lothar $
*/
define('APPTYPEID', 20);// 应用标志
define('CURSCRIPT', 'homepage');// 后来又赋给了$_G['basescript']

/*完整初始化*/
require_once './source/class/class_core.php'; //引入核心类文件，作用为：自动引入类规则，错误和异常处理，单例创建discuz_application类实例，引入默认函数库function.core.php
// require_once './source/function/function_home.php';//项目函数库

$discuz = C::app();//实例化desiuz_application类
// $cachelist = array('magic','userapp','usergroups', 'diytemplatenamehome');
// $discuz->cachelist = $cachelist;//设置缓存列表
$discuz->init();//初始化整个应用
// require_once DISCUZ_ROOT .'public.php';



$navtitle = str_replace('{bbname}', $_G['setting']['bbname'], $_G['setting']['seotitle']['forum']);

// 引入文件
require DISCUZ_ROOT.'./source/plugin/homepage/homepage.php';
// require_once '/source/plugin/homepage/homepage.php';
// echo DISCUZ_ROOT.'./source/plugin/homepage/homepage.php';

?>