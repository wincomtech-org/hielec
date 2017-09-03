<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
require_once dirname(__FILE__).'/common.php';

/*
 * 分支
*/
switch ($_GET['pluginop']) {
case 'ajax':
	# code...
	break;

case 'record':
	# 活动记录
	echo "活动记录";
	break;

case 'giftrecord':
	$leftmenu = 'list';
	$oppo = looppo($_REQUEST['sign'], $_REQUEST['table']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	$head_title = $oppo['head_title'];
	$jumpext = $oppo['jumpext'];
	# 兑换记录...
	echo "兑换记录";
	$list = cache_list_table('activity_log', '*',  'type =\'exchange\'','','6');// 活动列表
	echo "<pre>";
	print_r($list);
	echo "</pre>";
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
	if ($table==$tpre.'_category') {
		$cates = plugin_common::get_category($tpre.'_category','cid,pid,name');
	} else {
		$cates = session_ob($tpre.'_category', 'cid,pid,name', 'order by sort');
	}
// print_r($page);
// print_r($cates);
	include template(THISPLUG.':uc_op');
	break;

case 'op':
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
	$tab['th'] = array('名称','索引','报名积分','简述','浏览次数','收藏次数','外链','主题图','发布时间','修改时间','操作ip','状态','显隐','排序','操作');
	$tab['td'] = array('name','index','price','brief','views','cols','url','pic','addtime','modtime','ip','status','is_show','sort');
	$tab['operator'] = array(
			// array(AURL.'&pluginop=ajax&opera=show'.$jumpext.'&loid=','显隐'),
			array(LO_CURURL.'&pluginop=page'.$jumpext.'&loid=', '编辑'),
			array(LO_CURURL.'&pluginop=del'.$jumpext.'&loid=', '删除')
		);
	
	// 数据查询、处理
	$where = array(
			array('start', $_REQUEST['start'], 'addtime'),
			array('end', $_REQUEST['end'], 'addtime'),
			array('key', $_REQUEST['key'], 'name'),
			array('keyword', $_REQUEST['keyword'], 'name,tags,brief,details'),
			array('uid', $_REQUEST['uid'], 'uid'),
			'recycle not like \'%s%\''
		);
	$order = 'id asc';
	// $fields = implode(',', $tab['td']);
	$fields = 'name,price,brief,url,pic,pdf,attach,views,searchs,cols,supports,unsupports,author_id,addtime,modtime,ip,status,is_show,sort';
	$fields = $fields=='*'?$fields:($lokey?$lokey.','.$fields:$fields);
	// $join = array(
	// 		// array('b','name as cname','download_category','cid','=','cid'),
	// 		array('c','username','common_member','author_id','=','uid')
	// 	);
	// $cmt = plugin_common::common_list($table, $pluginvar['pagesize'], $_GET['page'], $where, $order, $jumpext, $fields, $join, 'a');
	$cmt = plugin_common::common_list($table, $pluginvar['pagesize'], $_GET['page'], $where, $order, '', $fields);
	$multipage = $cmt['multipage'];
	$list = $cmt['list'];

	include template(THISPLUG.':uc_index');
	break;
}
?>