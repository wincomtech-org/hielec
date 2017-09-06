<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

require './source/class/class_core.php';
require './source/function/function_forum.php';

$modarray = array('ajax','announcement','attachment','forumdisplay',
	'group','image','index','medal','misc','modcp','notice','post','redirect',
	'relatekw','relatethread','rss','topicadmin','trade','viewthread','tag','collection','guide'
);
$modcachelist = array(
	'index'			=> array('announcements', 'onlinelist', 'forumlinks', 'heats', 'historyposts', 'onlinerecord', 'userstats', 'diytemplatenameforum'),
	'forumdisplay'	=> array('smilies', 'announcements_forum', 'globalstick', 'forums', 'onlinelist', 'forumstick', 'threadtable_info', 'threadtableids', 'stamps', 'diytemplatenameforum'),
	'viewthread'	=> array('smilies', 'smileytypes', 'forums', 'usergroups', 'stamps', 'bbcodes', 'smilies',	'custominfo', 'groupicon', 'stamps',
			'threadtableids', 'threadtable_info', 'posttable_info', 'diytemplatenameforum'),
	'redirect'		=> array('threadtableids', 'threadtable_info', 'posttable_info'),
	'post'			=> array('bbcodes_display', 'bbcodes', 'smileycodes', 'smilies', 'smileytypes', 'domainwhitelist', 'albumcategory'),
	'space'			=> array('fields_required', 'fields_optional', 'custominfo'),
	'group'			=> array('grouptype', 'diytemplatenamegroup'),
	// 'guide'			=> array('my'),
);

$mod = !in_array(C::app()->var['mod'], $modarray) ? 'index' : C::app()->var['mod'];
define('CURMODULE', $mod);

$cachelist = array();
if(isset($modcachelist[CURMODULE])) {
	$cachelist = $modcachelist[CURMODULE];
	$cachelist[] = 'plugin';
	$cachelist[] = 'pluginlanguage_system';
}

if(C::app()->var['mod'] == 'group') {
	$_G['basescript'] = 'group';
}
C::app()->cachelist = $cachelist;
C::app()->init();
require_once DISCUZ_ROOT .'public.php';

loadforum();
set_rssauth();
runhooks();



/*下面这一些是我加的*/
### 分类 group status=0隐藏1正常3群组
$forum_url = 'forum.php?gid=';
$thread_url = 'forum.php?mod=forumdisplay&fid=';
$post_url = 'forum.php?mod=viewthread&tid=';
$forum_hot = cache_data(sprintf('SELECT fid,name FROM %s WHERE status=1 and type=\'group\' ORDER BY posts DESC,type,displayorder LIMIT 9',DB::table('forum_forum')), 'forum_hot', 'fetch_all', 60);
### 本月达人 
$talent_show = cache_data(sprintf('SELECT a.uid,a.extcredits2,a.friends,a.posts,a.threads,a.digestposts,a.doings,a.views,b.username,c.interest FROM %s AS a LEFT JOIN %s AS b ON a.uid=b.uid LEFT JOIN %s AS c ON a.uid=c.uid ORDER BY a.posts DESC LIMIT 3',DB::table('common_member_count'),DB::table('common_member'),DB::table('common_member_profile')), 'talent_show', 'fetch_all', 60);
### 最新主题
// $grids['newthread'];

### 缓存似乎这里获取不到
// debug(memory('get', 'forum_index_page_'.$_G['member']['groupid']),1);
// debug(memory('get', 'forum_index_page_1'),1);
### SESSION 并不好使，而且从个人中心跳过来时，左侧栏是乱码的？
// $catlist	= $_SESSION['catlist'];
// $forumlist	= $_SESSION['forumlist'];
// $grids		= $_SESSION['grids'];
// session_unset();
// session_destroy();
### 直接从数据库获取
// 涉及的表名 pre_forum_forum allowpostspecial
// 字段 fup:上级fid  type:group/forum/sub  status:1 / 2
function get_catlist($list=array(), $pid=0, $cid='')
{
	foreach ($list as $k => $v) {
		if ($v['fup']==$pid) {
			$v['url'] = 'forum.php?mod=forumdisplay&fid='.$v['fid'];
			$v['cur'] = $v['fid']==$cid ? 'style="color:#F90;"' : '';
			foreach ($list as $child) {
				if ($child['fup']==$v['fid']) {
					$v['child'] = get_catlist($list, $v['fid'], $cid);break;
				}
			}
			$catlist[] = $v;
		}
	}
	return $catlist;
}
$sql = "SELECT fid,fup,type,name,threads,posts,todayposts,rank,lastpost,domain,forumcolumns from ".DB::table('forum_forum')." where status=1 order by fup,displayorder";
$catlistall = cache_data($sql,'catlistall');
$catlistall = get_catlist($catlistall, 0, $_G['fid']);
// debug($catlistall,1);
$navtitle = str_replace('{bbname}', $_G['setting']['bbname'], $_G['setting']['seotitle']['forum']);
$_G['setting']['threadhidethreshold'] = 1;
### 一定要 require_once 避免重复
// require_once DISCUZ_ROOT.'./source/module/forum/forum_index.php';



require_once DISCUZ_ROOT.'source/module/forum/forum_'.$mod.'.php';
?>