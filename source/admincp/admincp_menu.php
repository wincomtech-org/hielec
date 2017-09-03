<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

global $_G;
$isfounder = isset($isfounder) ? $isfounder : isfounder();
$topmenu = $menu = array();

$topmenu = array(
	'index' => '',//首页
	'global' => '',//全局
	'style' => '',//界面
	'user' => '',//用户
	'topic' => '',//内容
	// 'portal' => '',//注释门户
	'forum' => '',//论坛
	// 'group' => '',//注释群组
	'safe' => '',//防灌水
	'trade' => '',//添加二手交易
	'activity'=>'',//添加活动
	'datasheet'=>'',//添加Datasheet
	'down'=>'',//添加下载
	'project'=>'',//添加外包
	'extended' => '',//运营
	'plugin' => $isfounder ? 'plugins' : '',//应用=>插件
	'tools' => '',//工具
);

$menu['index'] = array(
	array('menu_home', 'index'),
	array('menu_custommenu_manage', 'misc_custommenu'),
);

$custommenu = get_custommenu();
$menu['index'] = array_merge($menu['index'], $custommenu);

$menu['global'] = array(
	array('menu_setting_basic', 'setting_basic'),
	array('menu_setting_access', 'setting_access'),
	array('menu_setting_functions', 'setting_functions'),
	array('menu_setting_optimize', 'setting_cachethread'),
	array('menu_setting_seo', 'setting_seo'),
	//注释全局->域名设置
	// array('menu_setting_domain', 'domain'),
	//注释全局->广播设置
	// array('menu_setting_follow', 'setting_follow'),
	// 注释全局->空间设置
	// array('menu_setting_home', 'setting_home'),
	array('menu_setting_user', 'setting_permissions'),
	array('menu_setting_credits', 'setting_credits'),
	// 注释全局->时间设置
	// array('menu_setting_datetime', 'setting_datetime'),
	// 注释全局->上传设置
	// array('menu_setting_attachments', 'setting_attach'),
	// 注释全局->水印设置
	// array('menu_setting_imgwater', 'setting_imgwater'),
	//注释全局->附件类型尺寸
	// array('menu_posting_attachtypes', 'misc_attachtype'),
	array('menu_setting_search', 'setting_search'),
	array('menu_setting_district', 'district'),
	//注释全局->排序榜设置
	// array('menu_setting_ranklist', 'setting_ranklist'),
	//注释全局->手机版访问设置
	// array('menu_setting_mobile', 'setting_mobile'),
	// 注释全局->防采集设置
	// array('menu_setting_antitheft', 'setting_antitheft'),
);

$menu['style'] = array(
	array('menu_setting_customnav', 'nav'),
	array('menu_setting_styles', 'setting_styles'),
	array('menu_styles', 'styles'),
	//注释界面->模板管理
	// $isfounder ? array('menu_styles_templates', 'templates') : null,
	array('menu_posting_smilies', 'smilies'),
	array('menu_click', 'click'),
	array('menu_thread_stamp', 'misc_stamp'),
	array('menu_posting_editor', 'setting_editor'),
	array('menu_misc_onlinelist', 'misc_onlinelist'),
);

$menu['topic'] = array(
	array('menu_moderate_posts', 'moderate'),
	array('menu_posting_censors', 'misc_censor'),
	array('menu_maint_report', 'report'),
	array('menu_setting_tag', 'tag'),
	//注释内容->淘贴管理
	// array('menu_setting_collection', 'collection'),
	array(cplang('nav_forum'), '', 1),
		array('menu_maint_threads', 'threads'),
		array('menu_maint_prune', 'prune'),
		array('menu_maint_attaches', 'attach'),
	array(cplang('nav_forum'), '', 2),

	//注释内容->组群
	// array(cplang('nav_group'), '', 1),
	// 	array('menu_maint_threads_group', 'threads_group'),
	// 	array('menu_maint_prune_group', 'prune_group'),
	// 	array('menu_maint_attaches_group', 'attach_group'),
	// array(cplang('nav_group'), '', 2),

	array(cplang('thread'), '', 1),
    		array('menu_moderate_recyclebin', 'recyclebin'),
		array('menu_moderate_recyclebinpost', 'recyclebinpost'),
		array('menu_threads_forumstick', 'threads_forumstick'),
		array('menu_postcomment', 'postcomment'),
	array(cplang('thread'), '', 2),
	array(cplang('nav_home'), '', 1),
		array('menu_maint_doing', 'doing'),
		array('menu_maint_blog', 'blog'),
		array('menu_maint_blog_recycle_bin', 'blogrecyclebin'),
		array('menu_maint_feed', 'feed'),
		array('menu_maint_album', 'album'),
		array('menu_maint_pic', 'pic'),
		array('menu_maint_comment', 'comment'),
		array('menu_maint_share', 'share'),
	array(cplang('nav_home'), '', 2),
);

$menu['user'] = array(
	array('menu_members_edit', 'members_search'),
	array('menu_members_add', 'members_add'),
	array('menu_members_profile', 'members_profile'),
	array('menu_members_stat', 'members_stat'),
	array('menu_members_newsletter', 'members_newsletter'),
	array('menu_members_mobile', 'members_newsletter_mobile'),
	array('menu_usertag', 'usertag'),
	array('menu_members_edit_ban_user', 'members_ban'),
	array('menu_members_ipban', 'members_ipban'),
	array('menu_members_credits', 'members_reward'),
	array('menu_moderate_modmembers', 'moderate_members'),
	array('menu_admingroups', 'admingroup'),
	array('menu_usergroups', 'usergroups'),
	array('menu_follow', 'specialuser_follow'),
	array('menu_defaultuser', 'specialuser_defaultuser'),
	// array('members_verify_profile', 'verify_verify'),资料审核
	array('menu_members_verify_setting', 'verify'),
);

if(is_array($_G['setting']['verify'])) {
	foreach($_G['setting']['verify'] as $vid => $verify) {
		if($vid != 7 && $verify['available']) {
			$menu['user'][] = array($verify['title'], "verify_verify_$vid");
		}
	}
}

$menu['portal'] = array(
	array('menu_portalcategory', 'portalcategory'),
	array('menu_article', 'article'),
	array('menu_topic', 'topic'),
	array('menu_html', 'makehtml'),
	array('menu_diytemplate', 'diytemplate'),
	array('menu_block', 'block'),
	array('menu_blockstyle', 'blockstyle'),
	//注释门户->第三方模块
	// array('menu_blockxml', 'blockxml'),
	array('menu_portalpermission', 'portalpermission'),
	array('menu_blogcategory', 'blogcategory'),
	array('menu_albumcategory', 'albumcategory'),
);

$menu['forum'] = array(
	array('menu_forums', 'forums'),
	array('menu_forums_merge', 'forums_merge'),
	array('menu_forums_infotypes', 'threadtypes'),
	array('menu_grid', 'grid'),
);

$menu['group'] = array(
	array('menu_group_setting', 'group_setting'),
	array('menu_group_type', 'group_type'),
	array('menu_group_manage', 'group_manage'),
	array('menu_group_userperm', 'group_userperm'),
	array('menu_group_level', 'group_level'),
	array('menu_group_mod', 'group_mod'),
);

$menu['safe'] = array(
	array('menu_safe_setting', 'setting_sec'),
	array('menu_safe_security', 'cloud_security'),
	array('menu_safe_seccheck', 'setting_seccheck'),
	array('menu_security', 'optimizer_security'),
	array('menu_safe_accountguard', 'setting_accountguard'),
);

$menu['extended'] = array(
	array('menu_misc_announce', 'announce'),
	array('menu_adv_custom', 'adv'),
	array('menu_tasks', 'tasks'),
	array('menu_magics', 'magics'),
	array('menu_medals', 'medals'),
	array('menu_misc_help', 'faq'),
	array('menu_ec', 'setting_ec'),
	array('menu_withdraw', 'withdraw'),
	array('menu_misc_link', 'misc_link'),
	array('memu_focus_topic', 'misc_focus'),
	array('menu_misc_relatedlink', 'misc_relatedlink'),
	array('menu_card', 'card')
);

//添加二手交易
$menuurl_pre = 'plugins&operation=config&do=17&identifier=lotrade&pmod=tradeadmin&pluginop=';
$menu['trade'] = array(
	array('menu_trade_1', 'plugins&operation=config&do=17'),
	array('menu_trade_2', $menuurl_pre),
	array('menu_trade_3', $menuurl_pre.'page'),
	array('menu_trade_4', $menuurl_pre.'cg'),
	array('menu_trade_5', $menuurl_pre.'cgpage'),
	array('menu_trade_6', $menuurl_pre.'brand'),
	array('menu_trade_7', $menuurl_pre.'brandpage'),
	array('menu_trade_8', $menuurl_pre.'article'),
	// array('menu_trade_9', '?action=plugins&operation=config&do=17&identifier=lotrade&pmod=tradeadmin&pluginop=articlepage'),	
);

//添加活动
$menuurl_pre = 'plugins&operation=config&do=16&identifier=loactivity&pmod=activityadmin&pluginop=';
$menu['activity'] = array(
	array('menu_activity_1', 'plugins&operation=config&do=16'),
	array('menu_activity_2', $menuurl_pre),
	array('menu_activity_3', $menuurl_pre.'page'),
	array('menu_activity_4', $menuurl_pre.'commonlist&sign=cg'),
	array('menu_activity_5', $menuurl_pre.'commonpage&sign=cg'),
	array('menu_activity_6', $menuurl_pre.'gift'),
	array('menu_activity_7', $menuurl_pre.'giftpage'),
	array('menu_activity_8', $menuurl_pre.'joinlist'),
);

//添加Datasheet
$menuurl_pre = 'plugins&operation=config&do=15&identifier=lodatasheet&pmod=datasadmin&pluginop=';
$menu['datasheet'] = array(
	array('menu_datasheet_1', 'plugins&operation=config&do=15'),
	array('menu_datasheet_2', $menuurl_pre),
	array('menu_datasheet_3', $menuurl_pre.'page'),
	array('menu_datasheet_4', $menuurl_pre.'commonlist&sign=cg'),
	array('menu_datasheet_5', $menuurl_pre.'commonpage&sign=cg'),
);

//添加下载
$menuurl_pre = 'plugins&operation=config&do=14&identifier=lodown&pmod=downadmin&pluginop=';
$menu['down'] = array(
	array('menu_down_1', 'plugins&operation=config&do=14'),
	array('menu_down_2', $menuurl_pre),
	array('menu_down_3', $menuurl_pre.'page'),
	array('menu_down_4', $menuurl_pre.'commonlist&sign=cg'),
	array('menu_down_5', $menuurl_pre.'commonpage&sign=cg'),
);

//添加外包
$menuurl_pre = 'plugins&operation=config&do=13&identifier=loproject&pmod=projectadmin&pluginop=';
$menu['project'] = array(
	array('menu_project_set', 'plugins&operation=config&do=13'),
	array('menu_project_list', $menuurl_pre.'&c=list'),
	array('menu_project_edit', $menuurl_pre.'pro&c=page'),
	array('menu_project_cate', $menuurl_pre.'cate&c=list'),
	array('menu_project_cate_edit', $menuurl_pre.'cate&c=page'),
	// array('menu_project_talent', $menuurl_pre.'talent&c=list'),
	// array('menu_project_message', $menuurl_pre.'message&c=list'),
);



if(file_exists($menudir = DISCUZ_ROOT.'./source/admincp/menu')) {
	$adminextend = $adminextendnew = array();
	if(file_exists($adminextendfile = DISCUZ_ROOT.'./data/sysdata/cache_adminextend.php')) {
		@include $adminextendfile;
	}
	$menudirhandle = dir($menudir);
	while($entry = $menudirhandle->read()) {
		if(!in_array($entry, array('.', '..')) && preg_match("/^menu\_([\w\.]+)$/", $entry, $entryr) && substr($entry, -4) == '.php' && strlen($entry) < 30 && is_file($menudir.'/'.$entry)) {
			@include_once $menudir.'/'.$entry;
			$adminextendnew[] = $entryr[1];
		}
	}
	if($adminextend != $adminextendnew) {
		@unlink($adminextendfile);
		if($adminextendnew) {
			require_once libfile('function/cache');
			writetocache('adminextend', getcachevars(array('adminextend' => $adminextendnew)));
		}
		unset($_G['lang']['admincp']);
	}
}

/*应用=>插件*/
if($isfounder) {
	$menu['plugin'] = array(
		//注释应用->应用中心
		// array('menu_addons', 'cloudaddons'),
		array('menu_plugins', 'plugins'),
	);
}
loadcache('adminmenu');
if(is_array($_G['cache']['adminmenu'])) {
	foreach($_G['cache']['adminmenu'] as $row) {
		if($row['name'] == 'plugins_system') {
			$row['name'] = cplang('plugins_system');
		}
		//注释应用->下载，活动，Datasheet，二手交易，外包
		// $menu['plugin'][] = array($row['name'], $row['action'], $row['sub']);
	}
}
if(!$menu['plugin']) {
	unset($topmenu['plugin']);
}

/*工具*/
$menu['tools'] = array(
	array('menu_tools_updatecaches', 'tools_updatecache'),
	array('menu_tools_updatecounters', 'counter'),
	array('menu_logs', 'logs'),
	array('menu_misc_cron', 'misc_cron'),
	$isfounder ? array('menu_tools_fileperms', 'tools_fileperms') : null,
	$isfounder ? array('menu_tools_filecheck', 'checktools_filecheck') : null,
	$isfounder ? array('menu_tools_hookcheck', 'checktools_hookcheck') : null,
	//注释门户->云平台诊断工具
	// $isfounder ? array('menu_cloud_doctor', 'cloud_doctor') : null,
);
if($isfounder) {
	$topmenu['founder'] = '';
	$menu['founder'] = array(
		//注释站长->后台管理团队
		// array('menu_founder_perm', 'founder_perm'),
		array('menu_setting_mail', 'setting_mail'),
		//注释站长->安全中心
		// array('menu_patch', 'patch'),
		array('menu_setting_uc', 'setting_uc'),
		array('menu_db', 'db_export'),
		array('menu_membersplit', 'membersplit_check'),
		array('menu_postsplit', 'postsplit_manage'),
		array('menu_threadsplit', 'threadsplit_manage'),
		//注释站长->在线升级
		// array('menu_upgrade', 'upgrade'),
		array('menu_optimizer', 'optimizer_performance'),
	);
	$menu['uc'] = array();
}

/**/
if(!isfounder() && !isset($GLOBALS['admincp']->perms['all'])) {
	$menunew = $menu;
	foreach($menu as $topkey => $datas) {
		if($topkey == 'index') {
			continue;
		}
		$itemexists = 0;
		foreach($datas as $key => $data) {
			if(array_key_exists($data[1], $GLOBALS['admincp']->perms)) {
				$itemexists = 1;
			} else {
				unset($menunew[$topkey][$key]);
			}
		}
		if(!$itemexists) {
			unset($topmenu[$topkey]);
			unset($menunew[$topkey]);
		}
	}
	$menu = $menunew;
}
?>