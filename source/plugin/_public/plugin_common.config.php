<?php 
/** 我的插件
前台 project.inc.php
用户 projectop.inc.php
后台 projectadmin.inc.php
*/
// header('Content-Type:text/html;charset=gbk');
if ($_GET['identifier']) { $identifier = $_GET['identifier']; }
define('THISPLUG', $identifier);
if ($_GET['pmod']) { $module = $_GET['pmod']; }
define('MODULE', $module);
// echo MODULE;die;
// if (!$_G['uid']) showmessage(THISPLUG.':请先登录','member.php?mod=logging&action=login');

// ob_start();
// ob_end_flush();

/*
 * 公共区域
*/
require_once '/source/plugin/_public/common.php';
// 初始化URL
// define('LO_CURURL', 'plugin.php?id='.THISPLUG.':'.MODULE);// 前台或者 $_GET['id']
define('LO_CURURL', 'plugin.php?pl='.MODULE);// 设置了路由，使用路由 pl=%s 相当于 id=lo%s:%s
// 初始化路径
define('LO_PATH', '/source/plugin/'.THISPLUG.'/');// 插件位置
define('LO_ROOT', realpath(DISCUZ_ROOT).LO_PATH);// 当前目录位置 DISCUZ_ROOT物理路径 realpath()真实路径
define('LO_CTRL', LO_PATH.'control/');// 控制器位置
define('LO_TPL', LO_PATH.'template/');// 模板位置

/*插件数据*/
loadcache('plugin');
$pluginvar = $_G['cache']['plugin'][THISPLUG];
// 每页记录数
if ($pluginvar['pagesize']) { $pagesize = intval($pluginvar['pagesize']); }
// 积分与货币转换比率
$loper = explode(':', $pluginvar['loper']);
$loper = ($loper[1]/$loper[0]);
$curcredits_per = $curcredits*$loper;
// 审核开关
define('AUDIT', $pluginvar['audit']);



?>