<?php
// header('Content-Type:text/html;charset=gbk');
define('THISPLUG', 'homepage');
$pmod = $_GET['pmod']?$_GET['pmod']:'homepage';
session_start();



/*
 * 公共区域
*/
require_once '/source/plugin/_public/common.php';
// 初始化URL
define('LO_CURURL', LO_URL . '&pmod='.$pmod);// 前台或者 $_GET['id']
// 初始化路径
define('LO_PATH', '/source/plugin/'.THISPLUG.'/');// 插件位置
define('LO_ROOT', realpath(DISCUZ_ROOT).LO_PATH);// 当前目录位置 DISCUZ_ROOT物理路径 realpath()真实路径
define('LO_TPL', LO_PATH.'template/');// 模板位置



/*首页初始化*/
$tpre = 'homepage';$thead = '首页';
$no_pic_alt = '对不起,该图消失在二次元……';
$upload_common_path = dirname(LO_URL) . LO_UPLOAD;// realpath(LO_URL) 会除去 / ./ ../ 根路径

// require_once DISCUZ_ROOT.'./source/plugin/_public/plugin_common.config.php';
// require_once '/source/function/function_core.php';
require_once LO_PUB_PATH.'plugin_common.function.php';
require_once LO_PUB_PATH.'plugin_common.class.php';
// require_once dirname(__FILE__).'/'.THISPLUG.'.class.php';
// if (!$_G['uid']) plugin_common::jumpgo('请先登录','member.php?mod=logging&action=login');

// 主导航
$mnid = getcurrentnav();// 当前页标志
// 广告位
$adv_customs = lo_advs(1);

// 前台小导航 \template\default\_public\common_head.htm
// $head_nav = <<<NAV
// <div>
//  <a href="plugin.php?id=lo{$tpre}:{$tpre}" class="{if $module=='{$tpre}'}tab_hover{/if}">活动展示</a>
//  <a href="plugin.php?id=lo{$tpre}:{$tpre}op" class="{if $module=='{$tpre}op'}tab_hover{/if}">我的活动</a>
//  <a href="plugin.php?id=lo{$tpre}:{$tpre}op&pluginop=page" class="{if $module=='{$tpre}op'}tab_hover{/if}">发布活动</a>
//  <a href="plugin.php?id=lo{$tpre}:{$tpre}op&pluginop=record" class="{if $module=='{$tpre}op'}tab_hover{/if}">活动记录</a>
//  <a href="plugin.php?id=lo{$tpre}:{$tpre}&pluginop=gift" class="{if $module=='{$tpre}'}tab_hover{/if}">礼品兑换</a>
//  <a href="plugin.php?id=lo{$tpre}:{$tpre}op&pluginop=giftrecord" class="{if $module=='{$tpre}op'}tab_hover{/if}">兑换记录</a>
// </div>
// NAV;

// 菜单
// $menu = <<<MENU
// MENU;

?>