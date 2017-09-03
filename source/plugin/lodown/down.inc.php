<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');
require_once dirname(__FILE__).'/common.php';

/*
 * 分支
*/

switch ($_REQUEST['pluginop']) {
case 'ajax':
	include_once LO_PUB_PATH .'ajaxjson.php';
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
	$SEO['title'] = '下载详情页';
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
	$page['pdfsize'] = plugin_common::transByte(filesize($upload_common_path_op . LO_ATTACH .$page['attach']));
	$page['attachhref'] = $page['attach'] ? $upload_common_path . LO_ATTACH .$page['attach']: '';
	$page['addtime'] = $page['addtime'] ? dgmdate($page['addtime']) : $lang['no_resource'];

	// 该文件最近下载记录
	$arrRecent = DB::fetch_all(sprintf("SELECT a.uid,a.addtime,a.ip,b.username FROM %s as a LEFT JOIN %s as b ON a.uid=b.uid WHERE a.type='%s' AND a.tid=%d AND a.status=%d GROUP BY a.uid",DB::table($tpre.'_log'),DB::table('common_member'),'down',$loid,1));
	// 附加操作
	DB::update($table,array('views'=>$page['views']+1),array('id'=>$loid));
	// echo "<pre>";locate('"1"',`cid`)>0
	$t_cid = ur_here($_REQUEST['sign'],$page['cid'],'',true);
	// 相关推荐 按标签？["2","13"]
	$arrTuijian = cache_list_table($table, $fields, "id<>$loid and cid like locate('$t_cid',`cid`)>0 ");
	
	$ur_here = ur_here($_REQUEST['sign'],$page['cid'],$page['name']);
	include template(THISPLUG.':details');
	break;

case 'op':
debug($_POST,1);
	plugin_common::common_op($_POST);
	break;

default:
	$leftmenu = 'list';
	$oppo = looppo($_REQUEST['sign'], $_REQUEST['table']);
	$url = LO_CURURL;
	$table = $oppo['table'];
	$fields = $oppo['fields'];
	$SEO['title'] = '下载';
	$SEO = lo_seo($SEO);
	$strWhere = " where (a.status=2||a.status=6) and a.is_show=1 and a.recycle='' ";

	$credit = is_null($_GET['credit'])?null:intval($_GET['credit']);
	if (!is_null($credit)) {
		$url .= '&credit='.$credit;
		switch ($credit) {
			case 0: $crd = '=0'; break;
			case 1: $crd = 'between 1 and 3'; break;
			case 2: $crd = 'between 4 and 6'; break;
			case 3: $crd = 'between 7 and 9'; break;
			case 4: $crd = 'between 10 and 15'; break;
			case 5: $crd = '>15'; break;
			default: $crd = ''; break;
		}
		$strWhere .= ' and price '.$crd;
	}
	// locate('"1"',`cid`)>0
	$resource = $_GET['resource']?intval($_GET['resource']):0;//资源属性
	if ($resource){
		$url .= '&resource='. $resource;
		$strWhere .= ' and locate(\'"'.$resource.'"\',a.cid)>0 ';	
	}

	$module = $_GET['module']?intval($_GET['module']):0;//模块
	if ($module){
		$url .= '&module='. $module;
		$strWhere .= ' and locate(\'"'.$module.'"\',a.cid)>0 ';
	}

	$software = $_GET['software']?intval($_GET['software']):0;//软件	
	if ($software){
		$url .= '&software='. $software;
		$strWhere .= ' and locate(\'"'.$software.'"\',a.cid)>0 ';
	}
	
	$program_language = $_GET['program_language']?intval($_GET['program_language']):0;//编程语言
	if ($program_language){
		$url .= '&program_language='. $program_language;
		$strWhere .= ' and locate(\'"'.$program_language.'"\',a.cid)>0 ';
	}
	
	$hot_skill = $_GET['hot_skill']?intval($_GET['hot_skill']):0;//热点技术
	if ($hot_skill){
		$url .= '&hot_skill='. $hot_skill;
		$strWhere .= ' and locate(\'"'.$hot_skill.'"\',a.cid)>0 ';
	}
	
	$use = $_GET['use']?intval($_GET['use']):0;//应用
	if ($use){
		$url .= '&use='. $use;
		$strWhere .= ' and locate(\'"'.$use.'"\',a.cid)>0 ';
	}
	
	$eletron_basis = $_GET['eletron_basis']?intval($_GET['eletron_basis']):0;//电子基础
	if ($eletron_basis){
		$url .= '&eletron_basis='. $eletron_basis;
		$strWhere .= ' and locate(\'"'.$eletron_basis.'"\',a.cid)>0 ';
	}

	$srcval = $_REQUEST['srcval']?trim($_REQUEST['srcval']):'';
	if($srcval){
		$url .= '&srcval='. $srcval;
		$strWhere .= " and (a.name like '%".$srcval."%' or a.brief like '%".$srcval."%')";
	}

	$cates = cache_data('SELECT cid,name,type from '.DB::table('down_category').' where is_show=1','downcates');
	// $cates = cache_list_table('down_category','cid,name,type','is_show=1','', 300);
	foreach ($cates as $v) {
		switch ($v['type']) {
			case 'resource_property': $resource_properties[]=$v; break;
			case 'module': $modules[]=$v; break;
			case 'software': $softwares[]=$v; break;
			case 'program_language': $program_languages[]=$v; break;
			case 'hot_skill': $hot_skills[]=$v; break;
			case 'use': $uses[]=$v; break;
			case 'eletron_basis': $eletron_basises[]=$v; break;
		}
	}
	
	//热门下载
	$downinfo = DB::fetch_all(sprintf("SELECT a.id,a.price,a.addtime,a.downs,a.name,a.tags,b.username FROM %s as a LEFT JOIN %s as b ON a.author_id=b.uid where (a.status=2||a.status=6) and a.is_show=1 and a.recycle='' order by a.downs desc LIMIT 0,5 ",DB::table($table),DB::table('common_member')));
	// $downinfo = cache_data(sprintf("SELECT a.id,a.price,a.addtime,a.downs,a.name,a.tags,b.username FROM %s as a LEFT JOIN %s as b ON a.author_id=b.uid where (a.status=2||a.status=6) and a.is_show=1 and a.recycle='' order by a.downs desc LIMIT 0,5 ",DB::table($table),DB::table('common_member')),'downinfo');
	
	//最新上传
	$addinfo = DB::fetch_all(sprintf("SELECT a.id,a.price,a.addtime,a.downs,a.name,a.tags,b.username FROM %s as a LEFT JOIN %s as b ON a.author_id=b.uid where (a.status=2||a.status=6) and a.is_show=1 and a.recycle='' order by a.addtime desc LIMIT 0,5 ",DB::table($table),DB::table('common_member')));

	$pagesize = 6;// 每页记录数	
	$strWhere2 = str_replace('a.','',$strWhere);
	$multi = plugin_common::pager("SELECT COUNT(*) FROM ".DB::table('down').$strWhere2,$_GET['page'],$url,$pagesize);
	$multipage = $multi[0];
	// 查询记录集
	$limit = $multi[1];
	$lists = DB::fetch_all(sprintf("SELECT a.id,a.price,a.addtime,a.downs,a.name,a.tags,b.username FROM %s as a LEFT JOIN %s as b ON a.author_id=b.uid %s order by a.id desc %s ",DB::table($table),DB::table('common_member'),$strWhere,$limit));

	include template(THISPLUG.':index_list');
	break;
}
?>