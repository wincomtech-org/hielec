<?php 
require_once '/source/plugin/_public/plugin_common.config.php';

$tpre = 'project';$lang['thead'] = '外包';
$upload_common_path = dirname(LO_URL) . LO_UPLOAD . $tpre.'/';// realpath(LO_URL) 会除去 / ./ ../ 根路径
$upload_common_path_op = realpath(DISCUZ_ROOT) . LO_UPLOAD . $tpre.'/';// 物理路径

require_once LO_PUB_PATH.'plugin_common.function.php';
require_once LO_PUB_PATH.'plugin_common.class.php';
require_once dirname(__FILE__).'/'.THISPLUG.'.class.php';

// 广告位
$adv_customs = lo_advs(2);

if (isset($_REQUEST['do'])) {
	define('DOO', $_REQUEST['do']);
	define('AURL', 'admin.php?action=plugins&operation=config&do='.DOO.'&identifier='.THISPLUG.'&pmod='.MODULE);
	define('AURLJUMP', 'action=plugins&operation=config&do='.DOO.'&identifier='.THISPLUG.'&pmod='.MODULE);
	// 后台小导航 \template\default\_public\admin_common_head.htm 这里弃用
	// $admin_head_nav_bool = array('category'=>true,'firm'=>false,'article'=>false,'gift'=>false);
	// $admin_head_nav = '';
} else {
	// 前台小导航 \template\default\_public\common_head.htm
	// 已弃用
	$head_nav = <<<NAV
	<div>
		<a href="plugin.php?id=lo{$tpre}:{$tpre}" class="{if $module=='{$tpre}'}tab_hover{/if}">任务大厅</a>
		<a href="plugin.php?id=lo{$tpre}:{$tpre}&pluginop=talent" class="{if $module=='{$tpre}'}tab_hover{/if}">人才市场</a>
		<a href="plugin.php?id=lo{$tpre}:{$tpre}op" class="{if $module=='{$tpre}op'}tab_hover{/if}">我的任务</a>
		<a href="plugin.php?id=lo{$tpre}:{$tpre}op&pluginop=page" class="{if $module=='{$tpre}op'}tab_hover{/if}">发布任务</a>
		<a href="plugin.php?id=lo{$tpre}:{$tpre}op&pluginop=sell" class="{if $module=='{$tpre}op'}tab_hover{/if}">出售服务</a>
	</div>
NAV;
}

// 菜单
$menu = <<<MENU
MENU;

// 面包屑
$ur_here = '
	<div class="nav_find">
        当前位置：首页/找项目/项目名/竞标中
    </div>';



function project_del($table, $lokey, $upfile='task_file')
{
    $ids = $_GET[$lokey] ? (array)$_GET[$lokey] : ($_POST['loids']?$_POST['loids']:plugin_common::jumpgo('非法操作！'));
    $ids = plugin_common::create_sql_in($ids);
    $files = DB::fetch_all(sprintf("SELECT %s from %s where %s %s;",$upfile,$table,$lokey,$ids));
    $res = DB::query(sprintf("DELETE from %s WHERE %s %s",$table,$lokey,$ids));
    // DB::delete($table,array($lokey=>$_GET[$lokey]));
    if ($res) {
        // 文件处理
        if ($files) {
            foreach ($files as $fs) {
                $files_c[] = $fs[$upfile];
            }
            plugin_common::del_file($files_c);
        }
        plugin_common::jumpgo('删除成功！','','','success');
    } else {
        plugin_common::jumpgo('删除失败！');
    }
}
?>