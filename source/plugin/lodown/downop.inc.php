<?php
//用户中心下载
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
require_once dirname(__FILE__).'/common.php';

/*
 * 分支
*/
switch ($_REQUEST['pluginop']) {
case 'ajax':
	# code...
	break;

case 'up':
	echo "up";
	include template(THISPLUG.':uc_upload');
	break;

case 'sell':
	echo "sell";
	include template(THISPLUG.':uc_sell');
	break;

case 'del':
	$wh = array();
	if ($_GET['loid']) {
		$wh['loids'] = $loid = intval($_GET['loid']);
	} elseif ($_POST['loids']) {
		$wh['loids'] = $loid= $_POST['loids'];
	} else {
		cpmsg('非法操作！', '', 'error');
		// cpmsg('删除成功！', AURLJUMP, 'success');
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
	plugin_common::common_taskdel($table, $wh, 's', $skip, $del_filekey);
	break;

case 'page':
	$leftmenu = 'page';
	$formcheck['url'] = LO_CURURL.'&pluginop=op';
	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$oppo = looppo('', $_REQUEST['table']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	$fields = $oppo['fields'];
	$head_title = $oppo['head_title'];
	// 数据查询、处理
	$page = DB::fetch_first("select ". $fields ." from ".DB::table($table)." where ".$lokey."=".$loid);
	$page['price'] = $page['price'] ? $page['price']: 0;
	$page['pichref'] = $page['pic'] ? $upload_common_path . LO_PIC .$page['pic']: '';
	$page['pdfhref'] = $page['pdf'] ? $upload_common_path . LO_PDF .$page['pdf']: '';
	$page['attachhref'] = $page['attach'] ? $upload_common_path . LO_ATTACH .$page['attach']: '';
	// if ($table==$tpre.'_category') {
	// 	$cates = plugin_common::get_category($tpre.'_category','cid,pid,name');
	// } else {
	// 	$cates = session_ob($tpre.'_category', 'cid,pid,name', 'order by sort');
	// }
// print_r($page);
// print_r($cates);
	include template(THISPLUG.':uc_op');
	break;

case 'op':
	// var_dump(plugin_common::common_op($_POST));
	plugin_common::common_op($_POST);
	break;

default:
	$leftmenu = 'list';
	$oppo = looppo($_REQUEST['sign'], $_REQUEST['table']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	$head_title = $oppo['head_title'];
	$jumpext = $oppo['jumpext'];
	
	// 预设table表格
	$tab['th'] = array('名称','索引','简述','外链','主题图','发布时间','修改时间','操作ip','显隐','排序','操作');
	$tab['td'] = array('name','index','brief','url','pic','addtime','modtime','ip','is_show','sort');
	$tab['operator'] = array(
			// array(AURL.'&pluginop=ajax&opera=show'.$jumpext.'&loid=','显隐'),
			array(LO_CURURL.'&pluginop=page'.$jumpext.'&loid=', '编辑'),
			array(LO_CURURL.'&pluginop=del'.$jumpext.'&loid=', '删除')
		);
	
	// 数据查询、处理
	$where = array(
			// array('start', $_REQUEST['start'], 'addtime'),
			// array('end', $_REQUEST['end'], 'addtime'),
			// array('key', $_REQUEST['key'], 'name'),
			// array('keyword', $_REQUEST['keyword'], 'name,tags,brief,details'),
			// array('uid', $_G['uid'], 'author_id'),
			// 'recycle not like \'%u%\''
		);
	$order = 'sort ASC,modtime DESC,'. $lokey .' DESC';
	$fields = implode(',', $tab['td']);
	$fields = $fields=='*'?$fields:($lokey?$lokey.','.$fields:$fields);
	// $join = array(
	// 		array('b','name as cname','download_category','cid','=','cid')
	// 	);
	// $cmt = plugin_common::common_list($table, $pluginvar['pagesize'], $_GET['page'], $where, $order, $jumpext, $fields, $join);
	$cmt = plugin_common::common_list($table, $pluginvar['pagesize'], $_GET['page'], $where, $order);
	$multipage = $cmt['multipage'];
	$list = $cmt['list'];
	echo "<pre>";
	// echo $table;
	// var_dump($oppo);
	// var_dump($list);
	echo "</pre>";
	include template(THISPLUG.':uc_index');
	break;
}
?>