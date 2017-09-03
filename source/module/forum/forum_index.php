<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');
require_once libfile('function/forumlist');

$gid = intval(getgpc('gid'));
// $_G['fid'] = intval(getgpc('fid'));// 为什么存 $_G[]
$showoldetails = get_index_online_details();

if(!$_G['uid'] && !$gid && $_G['setting']['cacheindexlife'] && !defined('IN_ARCHIVER') && !defined('IN_MOBILE')) {
	get_index_page_guest_cache();
}

$newthreads = round((TIMESTAMP - $_G['member']['lastvisit'] + 600) / 1000) * 1000;

$catlist = $forumlist = $sublist = $forumname = $collapse = $favforumlist = array();
$threads = $posts = $todayposts = $announcepm = 0;
$postdata = $_G['cache']['historyposts'] ? explode("\t", $_G['cache']['historyposts']) : array(0,0);
$postdata[0] = intval($postdata[0]);
$postdata[1] = intval($postdata[1]);

list($navtitle, $metadescription, $metakeywords) = get_seosetting('forum');
if(!$navtitle) {
	$navtitle = $_G['setting']['navs'][2]['navname'];
	$nobbname = false;
} else {
	$nobbname = true;
}
if(!$metadescription) {
	$metadescription = $navtitle;
}
if(!$metakeywords) {
	$metakeywords = $navtitle;
}

if($_G['setting']['indexhot']['status'] && $_G['cache']['heats']['expiration'] < TIMESTAMP) {
	require_once libfile('function/cache');
	updatecache('heats');
}

if($_G['uid'] && empty($_G['cookie']['nofavfid'])) {
	$favfids = array();
	$forum_favlist = C::t('home_favorite')->fetch_all_by_uid_idtype($_G['uid'], 'fid');
	if(!$forum_favlist) {
		dsetcookie('nofavfid', 1, 31536000);
	}
	foreach($forum_favlist as $key => $favorite) {
		if(defined('IN_MOBILE')) {
			$forum_favlist[$key]['title'] = strip_tags($favorite['title']);
		}
		$favfids[] = $favorite['id'];
	}
	if($favfids) {
		$favforumlist = C::t('forum_forum')->fetch_all($favfids);
		$favforumlist_fields = C::t('forum_forumfield')->fetch_all($favfids);
		foreach($favforumlist as $id => $forum) {
			if($favforumlist_fields[$forum['fid']]['fid']) {
				$favforumlist[$id] = array_merge($forum, $favforumlist_fields[$forum['fid']]);
			}
			forum($favforumlist[$id]);
		}

	}
}

if(!$gid && $_G['setting']['collectionstatus'] && ($_G['setting']['collectionrecommendnum'] || !$_G['setting']['hidefollowcollection'])) {
	require_once libfile('function/cache');
	loadcache('collection_index');
	$collectionrecommend = dunserialize($_G['setting']['collectionrecommend']);
	if(TIMESTAMP - $_G['cache']['collection_index']['dateline'] > 3600 * 4) {
		$collectiondata = $followdata = array();
		if($_G['setting']['collectionrecommendnum']) {
			if($collectionrecommend['ctids']) {
				$collectionrecommend['ctidsKey'] = array_keys($collectionrecommend['ctids']);
				$tmpcollection = C::t('forum_collection')->fetch_all($collectionrecommend['ctidsKey']);
				foreach($collectionrecommend['ctids'] as $ctid=>$setcollection) {
					if($tmpcollection[$ctid]) {
						$collectiondata[$ctid] = $tmpcollection[$ctid];
					}
				}
				unset($tmpcollection, $ctid, $setcollection);
			}
			if($collectionrecommend['autorecommend']) {
				require_once libfile('function/collection');
				$autorecommenddata = getHotCollection(500);
			}
		}

		savecache('collection_index', array('dateline' => TIMESTAMP, 'data' => $collectiondata, 'auto' => $autorecommenddata));
		$collectiondata = array('data' => $collectiondata, 'auto' => $autorecommenddata);
	} else {
		$collectiondata = &$_G['cache']['collection_index'];
	}

	if($_G['setting']['showfollowcollection']) {
		$followcollections = $_G['uid'] ? C::t('forum_collectionfollow')->fetch_all_by_uid($_G['uid']) : array();;
		if($followcollections) {
			$collectiondata['follows'] = C::t('forum_collection')->fetch_all(array_keys($followcollections), 'dateline', 'DESC', 0, $_G['setting']['showfollowcollection']);
		}
	}
	if($collectionrecommend['autorecommend'] && $collectiondata['auto']) {
		$randrecommend = array_rand($collectiondata['auto'], min($collectionrecommend['autorecommend'], count($collectiondata['auto'])));
		if($randrecommend && !is_array($randrecommend)) {
			$collectiondata['data'][$randrecommend] = $collectiondata['auto'][$randrecommend];
		} else {
			foreach($randrecommend as $ctid) {
				$collectiondata['data'][$ctid] = $collectiondata['auto'][$ctid];
			}
		}
	}
	if($collectiondata['data']) {
		$collectiondata['data'] = array_slice($collectiondata['data'], 0, $collectionrecommend['autorecommend'], true);
	}
}

if(empty($gid) && empty($_G['member']['accessmasks']) && empty($showoldetails)) {
	extract(get_index_memory_by_groupid($_G['member']['groupid']));
	if(defined('FORUM_INDEX_PAGE_MEMORY') && FORUM_INDEX_PAGE_MEMORY) {
		categorycollapse();
		if(!defined('IN_ARCHIVER')) {
			include template('diy:forum/discuz');
		} else {
			include loadarchiver('forum/discuz');
		}
		dexit();
	}
}

$grids = array();
if($_G['setting']['grid']['showgrid']) {
	loadcache('grids');
	$cachelife = $_G['setting']['grid']['cachelife'] ? $_G['setting']['grid']['cachelife'] : 600;
	$now = dgmdate(TIMESTAMP, lang('form/misc', 'y_m_d')).' '.lang('forum/misc', 'week_'.dgmdate(TIMESTAMP, 'w'));
	if(TIMESTAMP - $_G['cache']['grids']['cachetime'] < $cachelife) {
		$grids = $_G['cache']['grids'];
	} else {
		$images = array();
		$_G['setting']['grid']['fids'] = in_array(0, $_G['setting']['grid']['fids']) ? 0 : $_G['setting']['grid']['fids'];

		if($_G['setting']['grid']['gridtype']) {
			$grids['digest'] = C::t('forum_thread')->fetch_all_for_guide('digest', 0, array(), 3, 0, 0, 10, $_G['setting']['grid']['fids']);
		} else {
			$images = C::t('forum_threadimage')->fetch_all_order_by_tid(10);
			foreach($images as $key => $value) {
				$tids[$value['tid']] = $value['tid'];
			}
			$grids['image'] = C::t('forum_thread')->fetch_all_by_tid($tids);
		}
		$grids['newthread'] = C::t('forum_thread')->fetch_all_for_guide('newthread', 0, array(), 0, 0, 0, 6, $_G['setting']['grid']['fids']);

		$grids['newreply'] = C::t('forum_thread')->fetch_all_for_guide('reply', 0, array(), 0, 0, 0, 10, $_G['setting']['grid']['fids']);
		$grids['hot'] = C::t('forum_thread')->fetch_all_for_guide('hot', 0, array(), 3, 0, 0, 10, $_G['setting']['grid']['fids']);

		$_G['forum_colorarray'] = array('', '#EE1B2E', '#EE5023', '#996600', '#3C9D40', '#2897C5', '#2B65B7', '#8F2A90', '#EC1282');
		foreach($grids as $type => $gridthreads) {
			foreach($gridthreads as $key => $gridthread) {
				$gridthread['dateline'] = str_replace('"', '\'', dgmdate($gridthread['dateline'], 'u', '9999', getglobal('setting/dateformat')));
				$gridthread['lastpost'] = str_replace('"', '\'', dgmdate($gridthread['lastpost'], 'u', '9999', getglobal('setting/dateformat')));
				if($gridthread['highlight'] && $_G['setting']['grid']['highlight']) {
					$string = sprintf('%02d', $gridthread['highlight']);
					$stylestr = sprintf('%03b', $string[0]);

					$gridthread['highlight'] = ' style="';
					$gridthread['highlight'] .= $stylestr[0] ? 'font-weight: bold;' : '';
					$gridthread['highlight'] .= $stylestr[1] ? 'font-style: italic;' : '';
					$gridthread['highlight'] .= $stylestr[2] ? 'text-decoration: underline;' : '';
					$gridthread['highlight'] .= $string[1] ? 'color: '.$_G['forum_colorarray'][$string[1]] : '';
					$gridthread['highlight'] .= '"';
				} else {
					$gridthread['highlight'] = '';
				}
				if($_G['setting']['grid']['textleng']) {
					$gridthread['oldsubject'] = dhtmlspecialchars($gridthread['subject']);
					$gridthread['subject'] = cutstr($gridthread['subject'], $_G['setting']['grid']['textleng']);
				}

				$grids[$type][$key] = $gridthread;
			}
		}
		if(!$_G['setting']['grid']['gridtype']) {
			$focuspic = $focusurl = $focustext = array();
			$grids['focus'] = 'config=5|0xffffff|0x0099ff|50|0xffffff|0x0099ff|0x000000';
			foreach($grids['image'] as $ithread) {
				if($ithread['displayorder'] < 0) {
					continue;
				}
				if($images[$ithread['tid']]['remote']) {
					$imageurl = $_G['setting']['ftp']['attachurl'].'forum/'.$images[$ithread['tid']]['attachment'];
				} else {
					$imageurl = $_G['setting']['attachurl'].'forum/'.$images[$ithread['tid']]['attachment'];
				}
				$grids['slide'][$ithread['tid']] = array(
						'image' => $imageurl,
						'url' => 'forum.php?mod=viewthread&tid='.$ithread['tid'],
						'subject' => $ithread['subject']
					);
			}
		}
		$grids['cachetime'] = TIMESTAMP;
		savecache('grids', $grids);
	}
}



// 放出来，始终获取所有？
// if (empty($fid)) {
// 	require_once DISCUZ_ROOT.'./source/include/misc/misc_forum_actlist.php';
// }
if(!$gid && (!defined('FORUM_INDEX_PAGE_MEMORY') || !FORUM_INDEX_PAGE_MEMORY)) {
	// 什么都没有时
	# 原先是上面放出去的那部分
	require_once DISCUZ_ROOT.'./source/include/misc/misc_forum_actlist.php';
} else {
	// 当 $gid 不为空 和 缓存被定义 时 
	// 当 $gid 不为空 或 缓存未被定义 时 
	// 当 $gid 为空 或 缓存被定义 时 
	// $catlist = '';
	require_once DISCUZ_ROOT.'./source/include/misc/misc_category.php';
}



if(defined('IN_ARCHIVER')) {
	include loadarchiver('forum/discuz');
	exit();
}
categorycollapse();

if($gid && !empty($catlist)) {
	$_G['category'] = $catlist[$gid];
	$forumseoset = array(
		'seotitle' => $catlist[$gid]['seotitle'],
		'seokeywords' => $catlist[$gid]['keywords'],
		'seodescription' => $catlist[$gid]['seodescription']
	);
	$seodata = array('fgroup' => $catlist[$gid]['name']);
	list($navtitle, $metadescription, $metakeywords) = get_seosetting('threadlist', $seodata, $forumseoset);
	if(empty($navtitle)) {
		$navtitle = $navtitle_g;
		$nobbname = false;
	} else {
		$nobbname = true;
	}
	$_G['fid'] = $gid;
}



/*下面这一些是我加的*/
### $gid 和 $_G['fid']
// debug($_G['category']);
### 缓存似乎这里获取不到
// $key = !IS_ROBOT ? $_G['member']['groupid'] : 'for_robot';
// debug($key,1);
// debug(memory('get', 'forum_index_page_'.$key),1);
### SESSION 并不好使，而且从个人中心跳过来时，左侧栏是乱码的？
// $_SESSION['catlist']	= $catlist;
// $_SESSION['forumlist']	= $forumlist;
// $_SESSION['grids']		= $grids;
// debug($catlist);
### 模板占用
// if ($mod=='index') {
	$mnid = getcurrentnav();// 当前页标志
	include template('diy:forum/discuz:'.$gid);
	// include template('diy:forum/discuz - 2:'.$gid);
// }
/*
echo template('diy:forum/discuz:'.$gid);
论坛首页缓存位置 D:\WWW\hielec\./data/template/1_diy_forum_discuz.tpl.php 
论坛首页真实位置 /template/default/forum/discuz.htm
*/



/*以下为相关方法*/
function get_index_announcements() {
	global $_G;
	$announcements = '';
	if($_G['cache']['announcements']) {
		$readapmids = !empty($_G['cookie']['readapmid']) ? explode('D', $_G['cookie']['readapmid']) : array();
		foreach($_G['cache']['announcements'] as $announcement) {
			if(!$announcement['endtime'] || $announcement['endtime'] > TIMESTAMP && (empty($announcement['groups']) || in_array($_G['member']['groupid'], $announcement['groups']))) {
				if(empty($announcement['type'])) {
					$announcements .= '<li><span><a href="forum.php?mod=announcement&id='.$announcement['id'].'" target="_blank" class="xi2">'.$announcement['subject'].
						'</a></span><em>('.dgmdate($announcement['starttime'], 'd').')</em></li>';
				} elseif($announcement['type'] == 1) {
					$announcements .= '<li><span><a href="'.$announcement['message'].'" target="_blank" class="xi2">'.$announcement['subject'].
						'</a></span><em>('.dgmdate($announcement['starttime'], 'd').')</em></li>';
				}
			}
		}
	}
	return $announcements;
}

function get_index_page_guest_cache() {
	global $_G;
	$indexcache = getcacheinfo(0);
	if(TIMESTAMP - $indexcache['filemtime'] > $_G['setting']['cacheindexlife']) {
		@unlink($indexcache['filename']);
		define('CACHE_FILE', $indexcache['filename']);
	} elseif($indexcache['filename']) {
		@readfile($indexcache['filename']);
		$updatetime = dgmdate($indexcache['filemtime'], 'H:i:s');
		$gzip = $_G['gzipcompress'] ? ', Gzip enabled' : '';
		echo "<script type=\"text/javascript\">
			if($('debuginfo')) {
				$('debuginfo').innerHTML = '. This page is cached  at $updatetime $gzip .';
			}
			</script>";
		exit();
	}
}

function get_index_memory_by_groupid($key) {
	$enable = getglobal('setting/memory/forumindex');
	if($enable !== null && memory('check')) {
		if(IS_ROBOT) {
			$key = 'for_robot';
		}
		$ret = memory('get', 'forum_index_page_'.$key);
		define('FORUM_INDEX_PAGE_MEMORY', $ret ? 1 : 0);
		if($ret) {
			return $ret;
		}
	}
	return array('none' => null);
}

function get_index_online_details() {
	$showoldetails = getgpc('showoldetails');
	switch($showoldetails) {
		case 'no': dsetcookie('onlineindex', ''); break;
		case 'yes': dsetcookie('onlineindex', 1, 86400 * 365); break;
	}
	return $showoldetails;
}

function do_forum_bind_domains() {
	global $_G;
	if($_G['setting']['binddomains'] && $_G['setting']['forumdomains']) {
		$loadforum = isset($_G['setting']['binddomains'][$_SERVER['HTTP_HOST']]) ? max(0, intval($_G['setting']['binddomains'][$_SERVER['HTTP_HOST']])) : 0;
		if($loadforum) {
			dheader('Location: '.$_G['setting']['siteurl'].'/forum.php?mod=forumdisplay&fid='.$loadforum);
		}
	}
}

function categorycollapse() {
	global $_G, $collapse, $catlist;
	if(!$_G['uid']) {
		return;
	}
	foreach($catlist as $fid => $forum) {
		if(!isset($_G['cookie']['collapse']) || strpos($_G['cookie']['collapse'], '_category_'.$fid.'_') === FALSE) {
			$catlist[$fid]['collapseimg'] = 'collapsed_no.gif';
			$collapse['category_'.$fid] = '';
		} else {
			$catlist[$fid]['collapseimg'] = 'collapsed_yes.gif';
			$collapse['category_'.$fid] = 'display: none';
		}
	}

	for($i = -2; $i <= 0; $i++) {
		if(!isset($_G['cookie']['collapse']) || strpos($_G['cookie']['collapse'], '_category_'.$i.'_') === FALSE) {
			$collapse['collapseimg_'.$i] = 'collapsed_no.gif';
			$collapse['category_'.$i] = '';
		} else {
			$collapse['collapseimg_'.$i] = 'collapsed_yes.gif';
			$collapse['category_'.$i] = 'display: none';
		}
	}
}
?>