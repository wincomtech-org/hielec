<?php
//当前公共
require_once DISCUZ_ROOT.'source/plugin/_public/plugin_common.config.php';

$tpre = 'down';$lang['thead'] = '下载';
$upload_common_path = dirname(LO_URL) . LO_UPLOAD . $tpre.'/';// realpath(LO_URL) 会除去 / ./ ../ 根路径
$upload_common_path_op = realpath(DISCUZ_ROOT) . LO_UPLOAD . $tpre.'/';// 物理路径

$arrDownCredit = array('0分','1-3分','4-6分','7-9分','10-15分','15分以上');

require_once LO_PUB_PATH.'plugin_common.function.php';
require_once LO_PUB_PATH.'plugin_common.class.php';
require_once dirname(__FILE__).'/'.THISPLUG.'.class.php';

// 广告位
$adv_customs = lo_advs(4);
$lastvisit = dgmdate($_G['member']['lastvisit']);
if (isset($_REQUEST['do'])) {
	define('DOO', $_REQUEST['do']);
	define('AURL', 'admin.php?action=plugins&operation=config&do='.DOO.'&identifier='.THISPLUG.'&pmod='.MODULE);
	define('AURLJUMP', 'action=plugins&operation=config&do='.DOO.'&identifier='.THISPLUG.'&pmod='.MODULE);
	// 后台小导航 \template\default\_public\admin_common_head.htm
	$admin_head_nav_bool = array('category'=>true,'firm'=>false,'article'=>false,'gift'=>false);
	$admin_head_nav = '';
} else {
	// 前台小导航 \template\default\_public\common_head.htm
	$head_nav = <<<NAV
	<div>
		<a href="plugin.php?id=lo{$tpre}:{$tpre}" class="{if $module=='{$tpre}'}tab_hover{/if}">下载展示</a>
		<a href="plugin.php?id=lo{$tpre}:{$tpre}op" class="{if $module=='{$tpre}op'}tab_hover{/if}">我的下载</a>
		<a href="plugin.php?id=lo{$tpre}:{$tpre}op&pluginop=page" class="{if $module=='{$tpre}op'}tab_hover{/if}">上传资源</a>
		<a href="plugin.php?id=lo{$tpre}:{$tpre}op&pluginop=sell" class="{if $module=='{$tpre}op'}tab_hover{/if}">出售资源</a>
	</div>
NAV;
}

// 菜单
$menu = <<<MENU
MENU;

?>