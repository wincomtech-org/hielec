<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class plugin_loproject {
	//全局钩子  
	function plugin_loproject(){
		global $_G;
		if(!$_G['uid']) {
			return false;
		}
	}
}
?>