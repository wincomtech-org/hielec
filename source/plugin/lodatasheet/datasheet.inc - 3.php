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
	$lokey = 'id';
	$tab['th'] = array('名称','价格','分类','制造商','销量','发布时间','操作');
	$tab['td'] = array('part','price','cat_name','mfg_name','num_sales','createtime');
	$tab['operator'] = array(
			array(LO_CURURL.'&pluginop=page&loid=', '查看', 'edit')
		);
	if ($tab['ajax']) $tab['operator'] = array_merge($tab['ajax'], $tab['operator']);

	// 数据查询处理
	$srctxt = trim($_POST['srctxt']);// 查询字段
	$index = trim($_REQUEST['index']);// 器件索引放这里了^_*
	$cid = trim($_REQUEST['cid']);// 厂商相关器件放这里了^_*
	$mfg_id = trim($_REQUEST['mfg_id']);// 厂商相关器件放这里了^_*

	// 数据分页跳转
	$jumpurl = LO_CURURL.'&pluginop=search';
	$jumpurl2 = LO_CURURL.'&pluginop=page';

	$strWhere = ' AND ';
	$strWhere2 = ' AND ';
	if ($index) {
		$strWhere .= '`part` like \''.$index.'%\' ';
		$strWhere2 .= 'a.part like \''.$index.'%\' ';
		$jumpurl .= '&index='.$index;
	} elseif ($cid) {
		$strWhere .= 'locate(\'"'.$cid.'"\',`cat_id`)>0 ';
		$strWhere2 .= 'a.cat_id=\''.$cid.'\' ';
		$jumpurl .= '&cid='.$cid;
	} elseif ($mfg_id) {
		$strWhere2 .= 'a.mfg_id=\''.$mfg_id.'\' ';
		$jumpurl .= '&mfg_id='.$mfg_id;
	} else {
		$strWhere .= sprintf('(`part`=\'%s\' OR locate(\'%s\',`part`)>0) ', $srctxt, $srctxt);
		$strWhere2 .= sprintf('locate(\'%s\',a.part)>0 ', $srctxt);
		$jumpurl .= '&srctxt='.$srctxt;
	}
	// echo $table;die;
	// $fields = 'id,cid,fid,name,subhead,tags,price,brief,url,pic,views,searchs,cols,downs,buys,supports,unsupports,author_id,addtime,modtime,ip';
	// $fields = plugin_common::create_fields_quote($fields,'a');
	// $fields = 'a.id,a.part as name,a.num_sales,b.mfg_name,c.cat_name';

	// 数据分页
	$pagesize = $pagesize ? $pagesize : 20;// 每页记录数
	// $pagesize = 1;// 每页记录数

	// 使用 Memcache
	// if (memory('get','dt_nums')===NULL || memory('get','dt_nums')===false) {
	// 	$sql = sprintf(
	// 			'SELECT count(*) from %s.ic_product as a '.
	// 			'RIGHT JOIN %s.ic_manufacturer_lang as b ON a.mfg_id=b.mfg_id '.
	// 			'RIGHT JOIN %s.ic_category_lang as c ON a.cat_id=c.cat_id '.
	// 			'WHERE b.lang_id=1 AND c.lang_id=1 %s',
	// 			$IS_DB3,$IS_DB3,$IS_DB3,$strWhere2
	// 		);
	// 	// echo $sql;die;
	// 	$query = DB::query($sql);
	// 	$amount = DB::result($query, 0);// 查询记录总数
	// 	memory('set', 'dt_nums', $amount);
	// 	// memory('set', 'arrdts', $arrDts, 0, 60);
	// 	// memory('rm', 'dt_nums');
	// } else {
	// 	$amount = memory('get', 'dt_nums');
	// }
	$sql = sprintf(
			'SELECT count(*) as cnt from %s.ic_product as a '.
			'RIGHT JOIN %s.ic_manufacturer_lang as b ON a.mfg_id=b.mfg_id '.
			'RIGHT JOIN %s.ic_category_lang as c ON a.cat_id=c.cat_id '.
			'WHERE b.lang_id=1 AND c.lang_id=1 %s',
			$IS_DB3,$IS_DB3,$IS_DB3,$strWhere2
		);
	$amount = cache_list_table($sql,'','','','',true);
	// echo $amount;
	$amount = $amount[0]['cnt'];
	// print_r($amount);

	$pagecount = $amount?(($amount<$pagesize)?1:(($amount%$pagesize)?((int)($amount/$pagesize)+1):($amount/$pagesize))):0;// 计算总页数
	$page = max(1, intval($_GET['page']));
	$page = $page > $pagecount ? 1 : $page;// 取得当前页值
	$startlimit = ($page - 1) * $pagesize;// 查询起始的偏移量
	$multipage = multi($amount, $pagesize, $page, $jumpurl, $pagecount, 10, false, true, false);// 显示分页
	// $multipage =  multi($num, $perpage, $curpage, $mpurl, $maxpages = 0, $page = 10, $autogoto = FALSE, $simple = FALSE, $jsfunc = FALSE);
	// 查询记录集
	$sql2 = sprintf(
			'SELECT a.id,a.cat_id,a.part,a.price,a.num_sales,a.createtime,b.mfg_name,c.cat_name from %s.ic_product as a '.
			'RIGHT JOIN %s.ic_manufacturer_lang as b ON a.mfg_id=b.mfg_id '.
			'RIGHT JOIN %s.ic_category_lang as c ON a.cat_id=c.cat_id '.
			'WHERE b.lang_id=1 AND c.lang_id=1 %s'.
			// 'GROUP BY a.part '.
			// 'ORDER BY a.num_sales DESC '.
			'LIMIT %s;',
			$IS_DB3,$IS_DB3,$IS_DB3,$strWhere2,$startlimit.','.$pagesize
		);
	// echo $sql2;
	$temps = cache_list_table($sql2,'','','','',true);
	foreach ($temps as $row) {
		if (isset($row['part'])) $row['part'] = '<a href="'. $jumpurl2 .'&loid='. $row['id'] .'">'. $row['part'] .'</a>';
		if (isset($row['createtime'])) $row['createtime'] = dgmdate($row['createtime']);
		$arrSearch[] = $row;
	}

	include template('_public/common_search');
	break;

case 'search2':
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
	// 预设table表格 name,brief,url,pic,is_show,sort
	$tab['th'] = array('名称','简介','主营产品','代理信息','联系方式','操作');
	$tab['td'] = array('mfg_name','mfg_intro','main_prod','agent_info','contact');
	// $tab['th'] = array('名称','简述','链接','logo','操作');
	// $tab['td'] = array('name','brief','url','pic');
	/*$ajax = json_decode('{"col":"收藏","zan":"点赞","cai":"踩"}');
	foreach ((array)$ajax as $k => $v) {
		$tab['ajax'][] = array(LO_CURURL.'&pluginop=ajax&sign='.$k.'&loid=', $v, $k);
	}*/
	$tab['operator'] = array(
			array(LO_CURURL.'&pluginop=search&mfg_id=', '相关器件', 'edit')
		);
	if ($tab['ajax']) $tab['operator'] = array_merge($tab['ajax'], $tab['operator']);

	// 厂商索引
	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$index = $_GET['index']?trim($_GET['index']):'';
	// $oppo = looppo('firm');
	// $table = $oppo['table'];
	// $lokey = $oppo['lokey'];
	// $fields = $oppo['fields'];
	// $head_title = $oppo['head_title'];
	// $jumpext = $oppo['jumpext'];
	$SEO = lo_seo();
	$lokey = 'mfg_id';

	// 数据查询处理
	// $order = ' ORDER BY sort asc,addtime desc,'. $lokey .' desc ';
	// $jumpext = 'commonlist'.$jumpext;
	// $fields = implode(',', $tab['td']);
	// $fields = $fields=='*'?$fields:($lokey?$lokey.','.$fields:$fields);

	// 数据分页跳转
	// $jumpext = $jumpext ?  '&pluginop='.$jumpext : '';
	// $jumpext = $url ? $jumpext.$url : $jumpext;
	$jumpurl = (function_exists('cpmsg')) ? AURL : LO_CURURL ;
	$jumpurl = $jumpurl . '&pluginop=firm';

	$wh = '';
	// $wh = ' WHERE index=\''. $index .'\' or name LIKE \''. $index .'%\'';
	// if ($index) $wh = ' WHERE recycle=\'\' and is_show=1 and name LIKE \''. $index .'%\' ';
	if ($index) {
		$wh = ' AND a.mfg_name LIKE \''. $index .'%\' ';
		$jumpurl .= '&index='.$index;
	}

	// 数据分页
	// $pagesize = $pagesize ? $pagesize : 20;// 每页记录数
	// $query = DB::query("SELECT COUNT(*) FROM " . DB::table($table) . $wh);
	// $amount = DB::result($query, 0);// 查询记录总数
	$sql = sprintf(
			'SELECT count(*) as cnt from %s.ic_manufacturer_lang as a '.
			'LEFT JOIN %s.ic_product as b ON a.mfg_id=b.mfg_id '.
			'WHERE a.lang_id=1 %s',
			$IS_DB3,$IS_DB3,$wh
		);
	$amount = cache_list_table($sql,'','','','',true);
	$amount = $amount[0]['cnt'];

	$pagecount = $amount?(($amount<$pagesize)?1:(($amount%$pagesize)?((int)($amount/$pagesize)+1):($amount/$pagesize))):0;// 计算总页数
	$page = max(1, intval($_GET['page']));
	$page = $page > $pagecount ? 1 : $page;// 取得当前页值
	$startlimit = ($page - 1) * $pagesize;// 查询起始的偏移量
	$multipage = multi($amount, $pagesize, $page, $jumpurl, $pagecount);// 显示分页

	// 查询记录集
	$sql2 = sprintf(
			'SELECT a.mfg_id,a.mfg_name,a.mfg_intro,a.main_prod,a.agent_info,a.contact from %s.ic_manufacturer_lang as a '.
			'RIGHT JOIN %s.ic_product as b ON a.mfg_id=b.mfg_id '.
			'WHERE a.lang_id=1 %s'.
			// 'GROUP BY a.mfg_id '.
			// 'ORDER BY a.mfg_id DESC '.
			'LIMIT %s;',
			$IS_DB3,$IS_DB3,$strWhere2,$startlimit.','.$pagesize
		);
	$arrSearch = $temps = cache_list_table($sql2,'','','','',true);
	// $temps = DB::fetch_all("SELECT " . $fields . " FROM " . DB::table($table) . $wh . $order . " LIMIT {$startlimit},{$pagesize}");

	// foreach ($temps as $row) {
	// 	$row['brief'] = isset($row['brief']) ? cutstr($row['brief'],20) : '' ;
	// 	$row['url'] = isset($row['url']) ? cutstr($row['url'],20) : '' ;
	// 	$row['pic'] = isset($row['pic']) ? cutstr($row['pic'],20) : '' ;
	// 	$row['is_show'] = isset($row['is_show']) ? '显示' : '隐藏' ;
	// 	$arrSearch[] = $row;
	// }
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
	// $oppo = looppo($_REQUEST['sign'], $_REQUEST['table']);
	// $table = $oppo['table'];
	// $lokey = $oppo['lokey'];
	// $fields = $oppo['fields'];
	// $head_title = $oppo['head_title'];
	$head_title = $lang['thead'];
	// $SEO['title'] = 'Datasheet详情页';
	// $SEO = lo_seo($SEO);

	// EXPLAIN 排除语言包？lang_id=1为简体中文
	$sqlpre = sprintf(
		'SELECT a.id,a.cat_id,a.part as name,a.price,a.img_url as pic,a.pdf_url as pdf,a.num_sales as downs,a.createtime as addtime,b.mfg_name,c.cat_name from %s.ic_product as a '.
		'LEFT JOIN %s.ic_manufacturer_lang as b ON a.mfg_id=b.mfg_id '.
		'LEFT JOIN %s.ic_category_lang as c ON a.cat_id=c.cat_id ',
		$IS_DB3,$IS_DB3,$IS_DB3
	);
	$limit = 'limit '.$pagesize;

	// 数据查询、处理
	// $arrHotDown = cache_list_table($table, $fields, null, 'downs desc');;// 热门下载
	// $arrNewUp = cache_list_table($table, $fields, null, 'addtime desc');;// 最新上传 
	$sql = $sqlpre . 'order by a.num_sales desc '.$limit;
	$arrHotDown = cache_list_table($sql,'','','','',true);
	$sql = $sqlpre . 'order by a.createtime desc '.$limit;
	$arrNewUp = cache_list_table($sql,'','','','',true);

	// $arrTags = '';// 热门标签
	// $fields2 = plugin_common::create_fields_quote($fields,'a');
	// $page = DB::fetch_first("select ". $fields2 .",b.username from ".DB::table($table)." a left join ". DB::table('common_member') ." b on a.author_id=b.uid where a.".$lokey."=".$loid);
	$sql = $sqlpre . 'WHERE a.id='.$loid;
	$page = DB::fetch_first($sql);
	if (empty($page)) {
		plugin_common::jumpgo('数据源丢失',LO_CURURL);
	}
// print_r($arrNewUp);
	// 如何获取 pdf 的大小？？？
	// print_r($page['pdf']);
	// echo file_get_contents('http://www.vishay.com/docs/84672/tfbs4650.pdf');
	// echo plugin_common::convert(file_get_contents('http://www.vishay.com/docs/84672/tfbs4650.pdf'),'utf-8');
	// echo filesize($page['pdf']);

	// $page['price'] = $page['price'] ? $page['price']: '0.00';
	// $page['pichref'] = $page['pic'] ? $upload_common_path . LO_PIC .$page['pic']: '';
	// $page['pdfhref'] = $page['pdf'] ? $upload_common_path . LO_PDF .$page['pdf']: '';
	// $page['pdfsize'] = plugin_common::transByte(filesize($upload_common_path_op . LO_PDF .$page['pdf']));
	// $page['pdfsize'] = plugin_common::transByte(filesize($page['pdf']));
	// $page['attachhref'] = $page['attach'] ? $upload_common_path . LO_ATTACH .$page['attach']: '';
	// $page['addtime'] = $page['addtime'] ? dgmdate($page['addtime']) : $lang['no_resource'];
	// $page['downs'] = 99;

	/*if ($table==$tpre.'_category') {
		$cates = plugin_common::get_category($table,'cid,pid,name');
	} else {
		$cates = session_ob($tpre.'_category', 'cid,pid,name', 'order by sort');
	}*/
	// $ur_here = ur_here($_REQUEST['sign'],$page['cid'],$page['name']);
	// $t_cid = ur_here($_REQUEST['sign'],$page['cid'],'',true);
	$t_cid = $page['cat_id'];

	// 相关推荐 按标签？["2","13"]
	// $arrTuijian = cache_list_table($table, $fields, "id<>$loid and cid like '%\"$t_cid\"%'");
	// $sql = $sqlpre . sprintf('WHERE a.id!=%s and a.cat_id like \'%s\' %s',$loid,$t_cid,$limit);// 30秒查询
	// $sql = $sqlpre . sprintf('WHERE a.cat_id like \'%s\' order by a.id %s',$t_cid,$limit);// 30秒查询
	$sql = "SELECT id,part as name FROM {$IS_DB3}.ic_product WHERE cat_id LIKE {$t_cid} ORDER BY id ASC LIMIT 20";
	$arrTuijian = cache_list_table($sql,'','','','',true);
	// print_r($arrTuijian);
	// $arrShare = '';// 该用户分享的
	// 该文件最近下载记录
	// $arrRecent = DB::fetch_all(sprintf("SELECT a.uid,a.addtime,a.ip,b.username FROM %s as a LEFT JOIN %s as b ON a.uid=b.uid WHERE a.type='%s' AND a.tid=%d AND a.status=%d GROUP BY a.uid",DB::table($tpre.'_log'),DB::table('common_member'),'down',$loid,1));
	// 附加操作
	// DB::update($table,array('views'=>$page['views']+1),array('id'=>$loid));

// echo "<pre>";
// echo dirname(LO_URL);
// echo $upload_common_path;
// print_r($arrRecent);
// print_r($page);
// print_r($cates);
// print_r($ur_here);
// print_r($_G);
// echo "</pre>";
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

	// $fields = 'a.part as name,a.mfg_part,a.price,a.unit,a.createtime,a.updatetime,a.img_url,a.page_url,a.pdf_url,b.mfg_name,c.cat_name';

	// 热门搜索
	// $hot_searchs = cache_list_table('datasheet','id,name','recycle=\'\' and is_show=1 and (status=2||status=6)','searchs desc',20);
	$sql = sprintf('SELECT id,part as name from %s.ic_product limit 30',$IS_DB3);
	$hot_searchs = cache_list_table($sql,'','','','',true);

	// 热门下载 GROUP BY 会建立临时表，在没有索引的情况下效率低下 , ORDER BY 联表效率低下
	// $hot_downs = cache_list_table('datasheet','id,name','recycle=\'\' and is_show=1 and (status=2||status=6)','downs desc',20);
	$sql = sprintf(
			'SELECT a.id,a.part as name,a.num_sales,b.mfg_name,c.cat_name from %s.ic_product as a '.
			'LEFT JOIN %s.ic_manufacturer_lang as b ON a.mfg_id=b.mfg_id '.
			'LEFT JOIN %s.ic_category_lang as c ON a.cat_id=c.cat_id '.
			'WHERE b.lang_id=1 AND c.lang_id=1 '.
			// 'GROUP BY a.part '.
			// 'ORDER BY a.num_sales DESC '.
			'LIMIT 30;',
			$IS_DB3,$IS_DB3,$IS_DB3
		);
	$hot_downs = cache_list_table($sql,'','','','',true);
	$hot_downs = plugin_common::getArrayUniqueByKeys($hot_downs,array('name'));
	$hot_downs = plugin_common::array_sort($hot_downs,'num_sales','desc');

	// 厂商索引
	$firm_indexs = array('*','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	// 器件索引
	$sheet_indexs = $firm_indexs;

// echo "<pre>";
// print_r($adv_customs);
// var_dump($hot_searchs);
// echo THISPLUG;
// print_r($_G);
// echo "</pre>";
	include template(THISPLUG.':index_list');
	break;
}
?>