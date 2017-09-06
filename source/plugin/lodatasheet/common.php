<?php 
// require_once $_G['siteurl'].'source/plugin/_public/plugin_common_config.php';
require_once DISCUZ_ROOT.'source/plugin/_public/plugin_common.config.php';

$tpre = 'datasheet';$lang['thead'] = 'DT';
$upload_common_path = dirname(LO_URL) . LO_UPLOAD . $tpre.'/';// realpath(LO_URL) 会除去 / ./ ../ 根路径
// $upload_common_path_op = LO_ROOT . LO_UPLOAD . $tpre.'/';// 物理路径
$upload_common_path_op = realpath(DISCUZ_ROOT) . LO_UPLOAD . $tpre.'/';// 物理路径

// $IS_DB2 = DB::result_first("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA where SCHEMA_NAME='uckk';");
$IS_DB3 = DB::result_first("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA where SCHEMA_NAME='21ic';");

require_once LO_PUB_PATH.'plugin_common.function.php';
require_once LO_PUB_PATH.'plugin_common.class.php';
require_once dirname(__FILE__).'/'.THISPLUG.'.class.php';

// 广告位
$adv_customs = lo_advs(5);
// echo $_G['uid'];
// echo $_G['adminid'];

if (isset($_REQUEST['do'])) {
	define('DOO', $_REQUEST['do']);
	define('AURL', 'admin.php?action=plugins&operation=config&do='.DOO.'&identifier='.THISPLUG.'&pmod='.MODULE);
	define('AURLJUMP', 'action=plugins&operation=config&do='.DOO.'&identifier='.THISPLUG.'&pmod='.MODULE);
	// define('REQUEST_URI', $_SERVER['REQUEST_URI']);
	// define('QUERY_STRING', $_SERVER['QUERY_STRING']);
	
	// 后台小导航 \template\default\_public\admin_common_head.htm
	$admin_head_nav_bool = array('category'=>true,'firm'=>true,'article'=>false,'gift'=>false);
	$admin_head_nav = '';
	// $admin_head_nav .= '<a href="'.AURL.'">商品列表页</a>'."\t";
	// $admin_head_nav .= "<a href=\"".AURL."&pluginop=page\">发布商品</a>\t";
	// $admin_head_nav .= "<a href=\"".AURL."&pluginop=cg\">分类列表页</a>\t";
	// $admin_head_nav .= "<a href=\"".AURL."&pluginop=cgpage\">新增分类</a>\t";
	// $admin_head_nav .= "<a href=\"".AURL."&pluginop=article\">文章列表页</a>\t";
	// $admin_head_nav .= "<a href=\"".AURL."&pluginop=articlepage\">新增文章</a>\t";
} else {
	// 前台小导航 \template\default\_public\common_head.htm
	$head_nav = <<<NAV
	<div>
		<a href="plugin.php?id=lo{$tpre}:{$tpre}" class="{if $module=='{$tpre}'}tab_hover{/if}">DT展示</a>
		<a href="plugin.php?id=lo{$tpre}:{$tpre}op" class="{if $module=='{$tpre}op'}tab_hover{/if}">我的DT</a>
		<a href="plugin.php?id=lo{$tpre}:{$tpre}op&pluginop=col" class="{if $module=='{$tpre}op'}tab_hover{/if}">我的收藏</a>
	</div>
NAV;
}

// 菜单
$menu = <<<MENU
MENU;

?>