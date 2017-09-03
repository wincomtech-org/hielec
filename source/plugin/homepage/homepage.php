<?php 
require_once dirname(__FILE__).'/common.php';
// echo "<pre>";
// print_r($adv_customs);
// echo $_G['setting']['navlogos'][$mnid];
// echo $_G['style']['boardlogo'];

/*
 * 论坛数据
 * 板块 forum_forum	(fid)	type类型 (group:分类 forum:普通论坛 sub:子论坛)
 		fup上级论坛id status显示状态(0:隐藏 1:正常 3:群组) displayorder显示顺序 threads主题数量 posts帖子数量 todayposts今日发帖数量 yesterdayposts昨日发帖数量 rank排名 oldrank昨日排名 lastpost最后发表 modnewposts是否审核发帖 jammer是否启用干扰码 favtimes收藏次数 sharetimes分享次数 view_index首页显示
 * 主题表 forum_thread (tid)	readperm阅读权限 price价格 dateline发表时间 lastpost最后发表时间 views浏览次数 replies回复次数 displayorder显示顺序 
 		highlight是否高亮 digest是否精华 rate是否评分 closed是否关闭 recommends推荐指数 recommend_add支持人数 recommend_sub反对人数 heats热度值 status状态 favtimes收藏次数 sharetimes分享次数 comments点评数
 * 帖子 forum_post (pid)  first是否是首贴 invisible是否通过审核
*/

/*版块 forum*/
$forum_url = 'forum.php?gid=';
$thread_url = 'forum.php?mod=forumdisplay&fid=';
$sqltable = 'forum_forum';
$sqlfield = 'fid,name';
// $sqlwhere = ' t WHERE t.status=\'1\' ';
// $sqlorder = 'ORDER BY t.type, t.displayorder';// 默认asc
// 热门版区
$forum_hot = cache_list_table($sqltable, $sqlfield, 'status=1 and type!=\'group\' and posts<>0', 'posts desc,type,displayorder', 7);
// 推荐版块
$fup = DB::fetch_first('select '. $sqlfield .' from '. DB::table($sqltable) .' where status=1 and type=\'group\'order by displayorder');
$forum_index = cache_list_table($sqltable, $sqlfield, 'status=1 and fup='.$fup['fid'], 'displayorder', 8);
// $forum_index = cache_list_table($sqltable, '*', 'status=1 and type=\'group\' and view_index=1','type,displayorder',8);
$threadlist = array();
foreach ($forum_index as $val) {
	$thread = DB::fetch_all('select tid,subject from '. DB::table('forum_thread') .' where closed=0 and fid='.$val['fid'].' order by heats desc limit 6');
	$threadlist[] = $thread;
}

/*主题 thread 技术贴*/
$post_url = 'forum.php?mod=viewthread&tid=';
// $post_url = 'forum.php?mod=viewthread&tid=2#lastpost';//定位到最后一条回复？
$sqltable = 'forum_thread';
$sqlfield = 'tid,subject';
// $sqlwhere = 'closed=0 and status=32';
$sqlwhere = 'closed=0';
$sqlorder = '';// 默认asc
// 新主题
$thread_news = cache_list_table($sqltable, $sqlfield, $sqlwhere, 'dateline desc');
// 热门主题
// $thread_hots = cache_list_table($sqltable, $sqlfield, $sqlwhere.' and heats<>0', 'heats desc');
$thread_hots = cache_list_table($sqltable, $sqlfield, $sqlwhere, 'heats desc');
// 精华主题
$thread_digest = cache_list_table($sqltable, $sqlfield, $sqlwhere.' and digest=1', 'digest desc');

/*帖子 post*/
// $post_news = cache_list_table('forum_post', $sqlfield, 'invisible<>0 and first=1', 'dateline desc');
// $post_hot = cache_list_table('forum_post', $sqlfield, 'invisible<>0 and first=1', 'dateline desc');

// print_r($thread_news);


// $forums = DB::fetch_all('SELECT '.$sqlfield.' FROM '. DB::table($sqltable) . $sqlwhere . $sqlorder);
// $forums = C::t('forum_forum')->fetch_all_by_status(1);
// $catlist = array();

// function get_forum_category($table='forum_forum', $fup=0, $fid = '') {
// 	$catlist = array();
// 	$data = C::t('forum_forum')->fetch_all_by_status(1);
// 	foreach ((array)$data as $value) {
// 		// $fup 将在嵌套函数中随之变化
// 		if ($value['fup'] == $fup) {
// 			if ($value['type'] != 'group') {
// 				$value['url'] = $this->rewrite_url($table, $value['fid']);
// 			} 
// 			foreach ($data as $child) {
// 				// 筛选下级导航
// 				if ($child['fup'] == $value['fid']) {
// 					// 嵌套函数获取子分类
// 					$value['child'] = get_forum_category($table, $value['fid'], $fid);
// 					break;
// 				}
// 			}
// 			$catlist[] = $value;
// 		}
// 	}
// 	return $catlist;
// }



/*foreach($forums as $forum) {
	if($forum['type'] != 'group') {
		$threads += $forum['threads'];// 主题数
		$posts += $forum['posts']; // 发帖数
		$todayposts += $forum['todayposts'];// 今日发帖数量

		if($forum['type'] == 'forum' && isset($catlist[$forum['fup']])) {
			if(forum($forum)) {
				$catlist[$forum['fup']]['forums'][] = $forum['fid'];
				$forum['orderid'] = $catlist[$forum['fup']]['forumscount']++;
				$forum['subforums'] = '';
				$forumlist[$forum['fid']] = $forum;
			}
		} elseif(isset($forumlist[$forum['fup']])) {
			$forumlist[$forum['fup']]['threads'] += $forum['threads'];
			$forumlist[$forum['fup']]['posts'] += $forum['posts'];
			$forumlist[$forum['fup']]['todayposts'] += $forum['todayposts'];
			if($_G['setting']['subforumsindex'] && $forumlist[$forum['fup']]['permission'] == 2 && !($forumlist[$forum['fup']]['simple'] & 16) || ($forumlist[$forum['fup']]['simple'] & 8)) {
				$forumurl = !empty($forum['domain']) && !empty($_G['setting']['domain']['root']['forum']) ? 'http://'.$forum['domain'].'.'.$_G['setting']['domain']['root']['forum'] : 'forum.php?mod=forumdisplay&fid='.$forum['fid'];
				$forumlist[$forum['fup']]['subforums'] .= (empty($forumlist[$forum['fup']]['subforums']) ? '' : ', ').'<a href="'.$forumurl.'" '.(!empty($forum['extra']['namecolor']) ? ' style="color: ' . $forum['extra']['namecolor'].';"' : '') . '>'.$forum['name'].'</a>';
			}
		}
	} else {
		if($forum['moderators']) {
		 	$forum['moderators'] = moddisplay($forum['moderators'], 'flat');
		}
		$forum['forumscount'] 	= 0;
		$catlist[$forum['fid']] = $forum;
	}
}*/






$IS_DB2 = DB::result_first("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA where SCHEMA_NAME='kppw26';");
$IS_DB3 = DB::result_first("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA where SCHEMA_NAME='21ic';");

/*下载数据*/
$down_url = 'plugin.php?id=lodown:down';
$downs = cache_list_table('down');
$down_ranks = array(
		array('月下载排行',$downs),
		array('周下载排行',$downs),
		array('日下载排行',$downs)
	);
rsort($down_ranks[1][1]);
// DT数据 需要用到21ic的数据
$sheet_url = 'plugin.php?id=lodatasheet:datasheet';
// $sheets = cache_list_table('datasheet');
$sql = sprintf('SELECT id,part as name,img_url as pic,package as brief,price from %s.ic_product limit 70;',$IS_DB3);
$sheets = $IS_DB3 ? cache_list_table($sql,'','','','',true) : cache_list_table('datasheet');
//合并项
$down_nav = array('下载排行榜','热门下载','datasheet下载');
// $downlist = array_merge($downs, $down_ranks, $sheets);
$downlist = array(1=>$down_ranks, 0=>$downs, 2=>$sheets);

// 项目外包
$pro_url = 'plugin.php?id=loproject:project';
// $pros = cache_list_table('project');
$pros =  $IS_DB2 ? cache_list_table(sprintf('SELECT task_id,task_title,task_cash,start_time from %s.keke_witkey_task limit 10',$IS_DB2),'','','','',true) : array();
// 优质服务商
$pro_serves = cache_list_table('project_task','tid,task_title,budget as task_cash,start_time','','',10);
//合并项
$pro_nav = array('项目外包','优质服务商');
$prolist = array(0=>$pros, 1=>$pro_serves);

/*二手交易*/
$trade_url = 'plugin.php?id=lotrade:trade';
// $trades = cache_list_table('trade','name,market_price,shop_price,weight','is_on_sale=1 and status=2');
// $trades = cache_list_table('trade','*','is_on_sale=1 and status=2');
$trade_pubnew = DB::fetch_all(sprintf("SELECT a.id,a.name,a.market_price,a.shop_price,a.pic,a.store_count,a.sales_sum,a.on_time,a.weight,b.username FROM %s as a LEFT JOIN %s as b ON a.uid=b.uid %s order by a.id desc LIMIT 6",DB::table('trade'),DB::table('common_member'),' where (a.status=2||a.status=6) and a.is_on_sale=1 '));
$trade_new = DB::fetch_all(sprintf("SELECT a.brief,b.username,c.name,c.id FROM %s as a LEFT JOIN `%s` as b ON a.uid=b.uid LEFT JOIN `%s` as c ON a.tradeid=c.id where a.type='buy' and (a.status=2||a.status=1) LIMIT 6",DB::table('trade_log'),DB::table('common_member'),DB::table('trade')));
// 合并项
$trade_nav = array('最新发布','最新交易');
$tradelist = array(0=>$trade_pubnew, 1=>$trade_new);
// print_r($trades);

/*活动*/
$activity_url = 'plugin.php?id=loactivity:activity';
$activitys = cache_list_table('activity');

/*公告*/
$announcements = cache_list_table('forum_announcement','id,author,subject,type,starttime,endtime,message,groups','','displayorder');

/*原厂专区*/
$sql = sprintf('SELECT id,part as name,img_url as pic,package as brief,price from %s.ic_product limit 16;',$IS_DB3);
$mfg_part = $IS_DB3 ? cache_list_table($sql,'','','','',true) : array();

/*帮助*/
$faqs = cache_list_table('forum_faq','id,fpid,identifier,keyword,title,message','fpid<>0','displayorder');

/*用户自拍*/
$usrinfos = DB::fetch_all("select a.uid,b.bio from ".DB::table('common_member')." a left join ".DB::table('common_member_profile')." b on a.uid=b.uid where a.avatarstatus=1 or b.bio!='' limit 5");

/**友情链接
 * type 基数8,4,2,1,0(0为全不选,15为全选)
*/
$friendlinks = cache_list_table('common_friendlink','*','','type');


// echo "</pre>";
// echo $_G['setting']['jspath'];
// print_r($_G);
// print_r($_G['setting']['creditnames']);
// print_r($_G['setting']['navs']);
// print_r($faqs);
// echo "<br>str\ting";
// die;
// include template(THISPLUG.':home');
include template('homepage:home');
?>