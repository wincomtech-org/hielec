<?php
define('APPTYPEID', 127);
define('CURSCRIPT', 'plugin');

require './source/class/class_core.php';

$discuz = C::app();

$cachelist = array('plugin', 'diytemplatename');

$discuz->cachelist = $cachelist;
$discuz->init();
// require_once DISCUZ_ROOT.'./public.php'; // require_once避免重复加载

/*路由设置*/
$allow_plugin_route = array('project','trade','down','datasheet','activity');
if (isset($_GET['pl']) && in_array($_GET['pl'],$allow_plugin_route)) {
    $_GET['id'] = 'lo'.$_GET['pl'].':'.$_GET['pl'];
}

// 插件里的 id 特殊用途，不可他用。
if(!empty($_GET['id'])) {
	list($identifier, $module) = explode(':', $_GET['id']);
	$module = $module !== NULL ? $module : $identifier;
} else {
	showmessage('plugin_nonexistence');
}

$mnid = 'plugin_'.$identifier.'_'.$module;
$pluginmodule = isset($_G['setting']['pluginlinks'][$identifier][$module]) ? $_G['setting']['pluginlinks'][$identifier][$module] : (isset($_G['setting']['plugins']['script'][$identifier][$module]) ? $_G['setting']['plugins']['script'][$identifier][$module] : array('adminid' => 0, 'directory' => preg_match("/^[a-z]+[a-z0-9_]*$/i", $identifier) ? $identifier.'/' : ''));
if(!preg_match('/^[\w\_]+$/', $identifier)) {
	showmessage('plugin_nonexistence');
}

if(empty($identifier) || !preg_match("/^[a-z0-9_\-]+$/i", $module) || !in_array($identifier, $_G['setting']['plugins']['available'])) {
	showmessage('plugin_nonexistence');
} elseif($pluginmodule['adminid'] && ($_G['adminid'] < 1 || ($_G['adminid'] > 0 && $pluginmodule['adminid'] < $_G['adminid']))) {
	showmessage('plugin_nopermission');
} elseif(@!file_exists(DISCUZ_ROOT.($modfile = './source/plugin/'.$pluginmodule['directory'].$module.'.inc.php'))) {
	showmessage('plugin_module_nonexistence', '', array('mod' => $modfile));
}

define('CURMODULE', $identifier);
runhooks();

include DISCUZ_ROOT.$modfile;
?>