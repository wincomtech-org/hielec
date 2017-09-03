<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
// 在访问PHP类中的成员变量或方法时，如果被引用的变量或者方法被声明成const（定义常量）或者static（声明静态）,那么就必须使用操作符::,
// 反之如果被引用的变量或者方法没有被声明成const或者static,那么就必须使用操作符->。
// 另外，如果从类的内部访问const或者static变量或者方法,那么就必须使用自引用的self，
// 反之如果从类的内部访问不为const或者static变量或者方法,那么就必须使用自引用的$this。
// 结论 :  self与$this的功能极其相似，但二者又不相同。$this不能引用静态成员和常量。self更像类本身，而$this更像是实例本身。

class plugin_lodatasheet {
	//全局钩子  
	function plugin_lodatasheet(){
		global $_G;
		if(!$_G['uid']) {
			return false;
		}
	}
	
	// function global_footer(){  
	// 	return '<script>alert("插件我来了")</script>';
	// }
}


/**
 * 只有运行member.php下注册页面时才运行的钩子 register_top 
*/ 
/*
class plugin_lodatasheet_member extends plugin_lodatasheet {
	function register_top(){
		header('location:http://zc.qq.com/chs/index.html'); //引导用户去注册QQ号
		// header('location:http://www.baidu.com'); //引导用户去百度
		exit;
	}
}
*/

?>