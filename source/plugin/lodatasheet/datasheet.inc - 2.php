<?php
if(!defined('IN_DISCUZ')) { exit('Access Denied'); }
require_once dirname(__FILE__).'/common.php';

/*
 * 分支
*/
switch ($_REQUEST['pluginop']) {
case 'ajax':
	include_once LO_PUB_PATH .'ajaxjson.php';
	break;

case 'search':
	$table = DB::table('datasheet');
	$lokey = 'id';
	// 预设table表格 
	/*$tab_th = '名称,标签,价格,外链,浏览数,搜索数,收藏数,下载数,作者,发布时间,简述,操作';
	$tab_td = 'name,tags,price,url,views,searchs,cols,downs,username,addtime,brief';
	echo explode(',',$tab_th);die;*/
	$tab['th'] = array('名称','价格','浏览数','搜索数','收藏数','下载数','作者','发布时间','简述','操作');
	$tab['td'] = array('name','price','views','searchs','cols','downs','username','addtime','brief');
	/*$ajax = json_decode('{"col":"收藏","zan":"点赞","cai":"踩"}');
	foreach ((array)$ajax as $k => $v) {
		$tab['ajax'][] = array(LO_CURURL.'&pluginop=ajax&sign='.$k.'&loid=', $v, $k);
	}*/
	$tab['operator'] = array(
			array(LO_CURURL.'&pluginop=page&loid=', '查看', 'edit')
		);
	if ($tab['ajax']) $tab['operator'] = array_merge($tab['ajax'], $tab['operator']);

	// 数据查询处理
	$srctxt = trim($_POST['srctxt']);// 查询字段
	$index = trim($_REQUEST['index']);// 器件索引放这里了^_*
	$cid = trim($_REQUEST['cid']);// 厂商相关器件放这里了^_*
	if ($index) {
		$strWhere = '`index`=\''. $index .'\'';
		$strWhere2 = 'a.index=\''. $index .'\'';
	} elseif ($cid) {
		$strWhere = 'locate(\'"'.$cid.'"\',`cid`)>0';
		$strWhere2 = 'locate(\'"'.$cid.'"\',a.cid)>0';
	} else {
		$strWhere = sprintf('(`index`=\'%s\' OR locate(\'%s\',`name`)>0)', $srctxt, $srctxt);
		$strWhere2 = sprintf('(a.index=\'%s\' OR locate(\'%s\',a.name)>0)', $srctxt, $srctxt);
	}
	// echo $table;die;
	$fields = 'id,cid,fid,name,subhead,tags,price,brief,url,pic,views,searchs,cols,downs,buys,supports,unsupports,author_id,addtime,modtime,ip';
	$fields = plugin_common::create_fields_quote($fields,'a');
	// 数据分页跳转
	$jumpurl = LO_CURURL.'&pluginop=search';
	$jumpurl2 = LO_CURURL.'&pluginop=page';
	// 数据分页
	$pagesize = $pagesize ? $pagesize : 20;// 每页记录数
	// $pagesize = 1;// 每页记录数
	$query = DB::query(sprintf('SELECT count(*) FROM %s WHERE `is_show`=1 AND %s', $table, $strWhere));
	$amount = DB::result($query, 0);// 查询记录总数
	$pagecount = $amount?(($amount<$pagesize)?1:(($amount%$pagesize)?((int)($amount/$pagesize)+1):($amount/$pagesize))):0;// 计算总页数
	$page = max(1, intval($_GET['page']));
	$page = $page > $pagecount ? 1 : $page;// 取得当前页值
	$startlimit = ($page - 1) * $pagesize;// 查询起始的偏移量
	$multipage = multi($amount, $pagesize, $page, $jumpurl, $pagecount, 10, false, true, false);// 显示分页
	// $multipage =  multi($num, $perpage, $curpage, $mpurl, $maxpages = 0, $page = 10, $autogoto = FALSE, $simple = FALSE, $jsfunc = FALSE);
	// 查询记录集
	$temps = DB::fetch_all(sprintf('SELECT %s,b.username FROM %s as a LEFT JOIN %s as b ON a.author_id=b.uid WHERE a.is_show=1 AND %s LIMIT %s', $fields, $table, DB::table('common_member'), $strWhere2, $startlimit.','.$pagesize));
	foreach ($temps as $row) {
		if (isset($row['name'])) $row['name'] = '<a href="'. $jumpurl2 .'&loid='. $row['id'] .'">'. $row['name'] .'</a>';
		if (isset($row['addtime'])) $row['addtime'] = dgmdate($row['addtime']);
		if (isset($row['brief'])) $row['brief'] = cutstr($row['brief'],20,'……');
		$arrSearch[] = $row;
		$strId .= $strId ? ','.$row['id'] : $row['id'];
	}
	// 附加操作
	$strId = plugin_common::create_sql_in($strId);
	if ($strId) {
		DB::query("UPDATE {$table} SET searchs=searchs+1 WHERE id {$strId}");
	}
	
	unset($temps);
	// include template(THISPLUG.':index_op');
	include template('_public/common_search');
	break;

case 'firm':
	// 厂商索引
	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$index = $_GET['index']?trim($_GET['index']):'';
	$oppo = looppo('firm');
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	// $fields = $oppo['fields'];
	$head_title = $oppo['head_title'];
	$jumpext = $oppo['jumpext'];
	$SEO = lo_seo();

	// 预设table表格 name,brief,url,pic,is_show,sort
	$tab['th'] = array('名称','简述','链接','logo','操作');
	$tab['td'] = array('name','brief','url','pic');
	/*$ajax = json_decode('{"col":"收藏","zan":"点赞","cai":"踩"}');
	foreach ((array)$ajax as $k => $v) {
		$tab['ajax'][] = array(LO_CURURL.'&pluginop=ajax&sign='.$k.'&loid=', $v, $k);
	}*/
	$tab['operator'] = array(
			array(LO_CURURL.'&pluginop=search&cid=', '相关器件', 'edit')
		);
	if ($tab['ajax']) $tab['operator'] = array_merge($tab['ajax'], $tab['operator']);

	// 数据查询处理
	$order = ' ORDER BY sort asc,addtime desc,'. $lokey .' desc ';
	// $jumpext = 'commonlist'.$jumpext;
	$fields = implode(',', $tab['td']);
	$fields = $fields=='*'?$fields:($lokey?$lokey.','.$fields:$fields);
	$wh = '';
	// $wh = ' WHERE index=\''. $index .'\' or name LIKE \''. $index .'%\'';
	if ($index) $wh = ' WHERE recycle=\'\' and is_show=1 and name LIKE \''. $index .'%\' ';

	// 数据分页跳转
	$jumpext = $jumpext ?  '&pluginop='.$jumpext : '';
	$jumpext = $url ? $jumpext.$url : $jumpext;
	$jumpurl = (function_exists('cpmsg')) ? AURL : LO_CURURL ;
	$jumpurl = $jumpurl . $jumpext;

	// 数据分页
	$pagesize = $pagesize ? $pagesize : 20;// 每页记录数
	$query = DB::query("SELECT COUNT(*) FROM " . DB::table($table) . $wh);
	$amount = DB::result($query, 0);// 查询记录总数
	$pagecount = $amount?(($amount<$pagesize)?1:(($amount%$pagesize)?((int)($amount/$pagesize)+1):($amount/$pagesize))):0;// 计算总页数
	$page = max(1, intval($_GET['page']));
	$page = $page > $pagecount ? 1 : $page;// 取得当前页值
	$startlimit = ($page - 1) * $pagesize;// 查询起始的偏移量
	$multipage = multi($amount, $pagesize, $page, $jumpurl, $pagecount);// 显示分页

	// 查询记录集
	$temps = DB::fetch_all("SELECT " . $fields . " FROM " . DB::table($table) . $wh . $order . " LIMIT {$startlimit},{$pagesize}");
	foreach ($temps as $row) {
		$row['brief'] = isset($row['brief']) ? cutstr($row['brief'],20) : '' ;
		$row['url'] = isset($row['url']) ? cutstr($row['url'],20) : '' ;
		$row['pic'] = isset($row['pic']) ? cutstr($row['pic'],20) : '' ;
		$row['is_show'] = isset($row['is_show']) ? '显示' : '隐藏' ;
		$arrSearch[] = $row;
	}
	unset($temps);
	// include template(THISPLUG.':index_op');
	// include template('_public/common_list');
	include template('_public/common_search');
	break;

case 'op':
// print_r($_POST);
// die;
	plugin_common::common_op($_POST);
	break;

case 'del':
	$wh = array();
	if ($_GET['loid']) {
		$wh['loids'] = $loid = intval($_GET['loid']);
	} elseif ($_POST['loids']) {
		$wh['loids'] = $loid= $_POST['loids'];
	} else {
		cpmsg('非法操作！', '', 'error');
	}
	$oppo = looppo($_REQUEST['sign'], $_REQUEST['table']);
	$table = $oppo['table'];
	$wh['lokey'] = $oppo['lokey'];
	$del_filekey = $oppo['del_filekey'];
	if ($oppo['jumpext']) {
		$jumpext = '&pluginop=commonlist' . $oppo['jumpext'];
	}
	$skip = array(
			'msgok' => '已删除您所选的数据！',
			'urlok' => '',
			'msgno' => '删除失败！',
			'urlno' => '',
			'ext' => $jumpext
		);
	plugin_common::common_taskdel($table, $wh, 'u', $skip, $del_filekey);
	break;

case 'page':
	$leftmenu = 'page';
	$formcheck['url'] = LO_CURURL.'&pluginop=op';
	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$oppo = looppo($_REQUEST['sign'], $_REQUEST['table']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	$fields = $oppo['fields'];
	$head_title = $oppo['head_title'];
	$SEO['title'] = 'Datasheet详情页';
	$SEO = lo_seo($SEO);
	
	// 数据查询、处理
	$arrHotDown = cache_list_table($table, $fields, null, 'downs desc');;// 热门下载
	$arrNewUp = cache_list_table($table, $fields, null, 'addtime desc');;// 最新上传 
	$arrTags = '';// 热门标签
	$fields2 = plugin_common::create_fields_quote($fields,'a');
	$page = DB::fetch_first("select ". $fields2 .",b.username from ".DB::table($table)." a left join ". DB::table('common_member') ." b on a.author_id=b.uid where a.".$lokey."=".$loid);
	$page['price'] = $page['price'] ? $page['price']: '0.00';
	$page['pichref'] = $page['pic'] ? $upload_common_path . LO_PIC .$page['pic']: '';
	$page['pdfhref'] = $page['pdf'] ? $upload_common_path . LO_PDF .$page['pdf']: '';
	$page['pdfsize'] = plugin_common::transByte(filesize($upload_common_path_op . LO_PDF .$page['pdf']));
	$page['attachhref'] = $page['attach'] ? $upload_common_path . LO_ATTACH .$page['attach']: '';
	$page['addtime'] = $page['addtime'] ? dgmdate($page['addtime']) : $lang['no_resource'];
	// $page['downs'] = 99;
	/*if ($table==$tpre.'_category') {
		$cates = plugin_common::get_category($table,'cid,pid,name');
	} else {
		$cates = session_ob($tpre.'_category', 'cid,pid,name', 'order by sort');
	}*/
	$ur_here = ur_here($_REQUEST['sign'],$page['cid'],$page['name']);
	$t_cid = ur_here($_REQUEST['sign'],$page['cid'],'',true);
	// 相关推荐 按标签？["2","13"]
	$arrTuijian = cache_list_table($table, $fields, "id<>$loid and cid like '%\"$t_cid\"%'");
	// $arrShare = '';// 该用户分享的
	// 该文件最近下载记录
	$arrRecent = DB::fetch_all(sprintf("SELECT a.uid,a.addtime,a.ip,b.username FROM %s as a LEFT JOIN %s as b ON a.uid=b.uid WHERE a.type='%s' AND a.tid=%d AND a.status=%d GROUP BY a.uid",DB::table($tpre.'_log'),DB::table('common_member'),'down',$loid,1));
	// 附加操作
	DB::update($table,array('views'=>$page['views']+1),array('id'=>$loid));

echo "<pre>";
// echo dirname(LO_URL);
// echo $upload_common_path;
// print_r($arrRecent);
// print_r($page);
// print_r($cates);
// print_r($ur_here);
// print_r($_G);
echo "</pre>";
	include template(THISPLUG.':index_op');
	// include template('_public/common_op');
	break;

default:
	// 初始化变量
	$leftmenu = 'list';
	// $formcheck = '';
	$oppo = looppo($_REQUEST['sign'], $_REQUEST['table']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	// $fields = $oppo['fields'];
	$head_title = $oppo['head_title'];
	// $jumpext = $oppo['jumpext'];
	$SEO['title'] = $oppo['head_title']?$oppo['head_title']:'Datasheet';
	$SEO = lo_seo($SEO);
	$ur_here = ur_here($_REQUEST['sign']);

	/*// 预设table表格
	$tab['th'] = array('名称','简述','链接','logo','PDF','点赞','反对','发布者id','发布时间','修改时间','操作ip','显隐','排序','价格','操作');
	$tab['td'] = array('name','brief','url','pic','pdf','supports','unsupports','author_id','addtime','modtime','ip','is_show','sort','price');
	$ajax = json_decode('{"show":"显隐","col":"收藏","zan":"点赞","cai":"踩","down":"下载"}');
	foreach ((array)$ajax as $k => $v) {
		$tab['ajax'][] = array(LO_CURURL.'&pluginop=ajax&sign='.$k.'&loid=', $v, $k);
	}
	$tab['operator'] = array(
			array(LO_CURURL.'&pluginop=page'.$jumpext.'&loid=', '编辑', 'edit'),
			array(LO_CURURL.'&pluginop=del'.$jumpext.'&loid=', '删除', 'del')
		);
	if ($tab['ajax']) $tab['operator'] = array_merge($tab['ajax'], $tab['operator']);

	// 数据查询处理
	$where = array(
			array('start', $_REQUEST['start'], 'addtime'),
			array('end', $_REQUEST['end'], 'addtime'),
			array('key', $_REQUEST['key'], 'name'),
			array('keyword', $_REQUEST['keyword'], 'name,tags,brief,details'),
			array('uid', $_REQUEST['uid'], 'uid'),
			'recycle=\'\' and is_show=1'
		);
	$order = 'sort asc,addtime desc,'. $lokey .' desc';
	// $jumpext = 'commonlist'.$jumpext;
	$fields = implode(',', $tab['td']);
	$fields = $fields=='*'?$fields:($lokey?$lokey.','.$fields:$fields);
	// $join = array(
	// 		array('b','name as cname','datasheet_category','cid','=','cid'),
	// 		array('c','name as fname','datasheet_firm','fid','=','cid')
	// 	);
	// $cmt = plugin_common::common_list($table, $pagesize, $_GET['page'], $where, $order, $jumpext, $fields, $join, 'a');
	$cmt = plugin_common::common_list($table, $pagesize, $_GET['page'], $where, $order, '', $fields);
	$multipage = $cmt['multipage'];
	$list = $cmt['list'];*/


	// 热门搜索
	$hot_searchs = cache_list_table('datasheet','id,name','recycle=\'\' and is_show=1 and (status=2||status=6)','searchs desc',20);
	// 热门下载
	$hot_downs = cache_list_table('datasheet','id,name','recycle=\'\' and is_show=1 and (status=2||status=6)','downs desc',20);
	// 厂商索引
	$firm_indexs = array('*','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	// 器件索引
	$sheet_indexs = $firm_indexs;

echo "<pre>";
// print_r($adv_customs);
// var_dump($hot_searchs);
// echo THISPLUG;
// print_r($_G);
echo "</pre>";
	include template(THISPLUG.':index_list');
	break;
}
?>