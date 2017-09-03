<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class plugin_lotrade {
	//全局钩子  
	function plugin_lotrade(){
		global $_G;
		if(!$_G['uid']) {
			return false;
		}
	}
}
?>