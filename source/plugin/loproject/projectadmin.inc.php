<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
require_once dirname(__FILE__).'/common.php';

/*
 * 分支
 * ?m=moudle&c=control&a=action&var=value
 * ?m=pluginop&c=pro&a=list&var=value
*/
$pluginop = $_REQUEST['pluginop'] ? $_REQUEST['pluginop'] : 'pro';
$oproute = 'admin';// 标识
$mainurl = AURL;
$jumpurl = AURLJUMP;
switch ($pluginop) {
case 'talent':
	// 预处理数据
    $ahr['cur'] = 'talent';
    $ahr['lm'] = '';
    $SEO['title'] = '人才大厅';
	break;

case 'pro':
    // 项目市场初始化数据
    $ahr['cur'] = 'pro';
    $ahr['lm'] = '';
    $SEO['title'] = '项目市场';
    break;

case 'cate':
    // 外包分类
    $ahr['cur'] = 'cate';
    $ahr['lm'] = 'category';
    $SEO['title'] = '外包分类';
    break;

case 'message':
    // 消息管理
    $ahr['cur'] = 'msg';
    $ahr['lm'] = '';
    $SEO['title'] = '外包分类';
	break;
}
require_once LO_CTRL . $oproute .'_'. ($ahr['lm']?$ahr['lm']:$pluginop) .'.php';
?>