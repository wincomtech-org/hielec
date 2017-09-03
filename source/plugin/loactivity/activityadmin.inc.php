<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
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
case 'joinlist':
// 初始化变量
	$leftmenu = 'joinlist';
	// $formcheck['url'] = AURL.'&pluginop=commonpage';
	$oppo = looppo('gift');
	$table = 'activity_apply';
	$lokey = 'id';
	// $fields = $oppo['fields'];
	$head_title = $oppo['head_title'];
	// $jumpext = $oppo['jumpext'];
	// 预设table表格
	$tab['th'] = array('活动名称','姓名','参与活动的原因','学校或者工作单位','邮箱','手机','留言','状态','操作');
	$tab['td'] = array('activityname','username','reason','unit','email','phone','message','status');
	$tab['operator'] = array(
			// array(AURL.'&pluginop=ajax&opera=show'.$jumpext.'&loid=','显隐'),
			array(AURL.'&pluginop=joinpage'.$jumpext.'&loid=', '编辑'),
			array(AURL.'&pluginop=delgapply'.$jumpext.'&loid=', '删除')
		);
	// var_dump($oppo);die;
	// 数据查询、处理
	$where = array(
			'recycle not like \'%a%\''
		);
	$order =  $lokey .' DESC';
	$jumpext = 'gift'.$jumpext;
	$fields = implode(',', $tab['td']);
	$fields = $fields=='*'?$fields:($lokey?$lokey.','.$fields:$fields);
	$cmt = plugin_common::common_list($table, $pluginvar['pagesize'], $_GET['page'], $where, $order, $jumpext, $fields);
	$multipage = $cmt['multipage'];
	$list = $cmt['list'];

	include template('_public/admin_common_list');

	# code...
	break;
case 'joinpage':
	# code...
	// 初始化变量
	$leftmenu = 'commonpage';
	$formcheck['url'] = AURL.'&pluginop=joinpage_update';
	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$oppo = looppo('gift');
	$table = 'activity_apply';
	$lokey = 'id';
	
	$head_title = $oppo['head_title'];
	// 数据查询、处理
	$fields_info = session_ob($table, $fields, '', 'column');
	$where = ' where ' . $lokey . '=' . $loid;
	

	// $fields = plugin_common::create_fields_quote($fields);
	$page = DB::fetch_first('select * from '. DB::table($table) . $where);
	
	include template(THISPLUG.':admin_joinpage');
	break;
case 'joinpage_update':
	# code...
	$leftmenu = 'commonpage';
	$formcheck['url'] = AURL.'&pluginop=joinlist';
	$loid=$_POST['id'];
	unset($_POST['id']);
	$table = 'activity_apply';
	$str=DB::update($table, $_POST, array('id'=>$loid));
	if($str){
		cpmsg('修改成功','','true');
	}else{
		cpmsg('修改失败','','error');
	}
	break;
case 'gift':
	// 初始化变量
	$leftmenu = 'giftlist';
	// $formcheck['url'] = AURL.'&pluginop=commonpage';
	$oppo = looppo('gift');
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	// $fields = $oppo['fields'];
	$head_title = $oppo['head_title'];
	// $jumpext = $oppo['jumpext'];
	// 预设table表格
	$tab['th'] = array('名称','价格（积分）','简述','相关链接','logo','发布时间','修改时间','操作ip','状态','显隐','排序','操作');
	$tab['td'] = array('name','price','brief','url','pic','addtime','modtime','ip','status','is_show','sort');
	$tab['operator'] = array(
			// array(AURL.'&pluginop=ajax&opera=show'.$jumpext.'&loid=','显隐'),
			array(AURL.'&pluginop=giftpage'.$jumpext.'&loid=', '编辑'),
			array(AURL.'&pluginop=delgift'.$jumpext.'&loid=', '删除')
		);
	// var_dump($oppo);die;
	// 数据查询、处理
	$where = array(
			array('start', $_REQUEST['start'], 'addtime'),
			array('end', $_REQUEST['end'], 'addtime'),
			array('key', $_REQUEST['key'], 'name'),
			array('keyword', $_REQUEST['keyword'], 'name,brief,details'),
			array('uid', $_REQUEST['uid'], 'uid'),
			'recycle not like \'%a%\''
		);
	$order = 'sort ASC,modtime DESC,'. $lokey .' DESC';
	$jumpext = 'gift'.$jumpext;
	$fields = implode(',', $tab['td']);
	$fields = $fields=='*'?$fields:($lokey?$lokey.','.$fields:$fields);
	$cmt = plugin_common::common_list($table, $pluginvar['pagesize'], $_GET['page'], $where, $order, $jumpext, $fields);
	$multipage = $cmt['multipage'];
	$list = $cmt['list'];

	include template('_public/admin_common_list');
	break;

case 'giftpage':
	// 初始化变量
	$leftmenu = 'commonpage';
	$formcheck['url'] = AURL.'&pluginop=commonop';
	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$oppo = looppo('gift');
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	$fields = $oppo['fields'];
	$head_title = $oppo['head_title'];
	// 数据查询、处理
	$fields_info = session_ob($table, $fields, '', 'column');
	$where = ' where ' . $lokey . '=' . $loid;
	$fields= str_replace("index","'index'", $fields);

	// $fields = plugin_common::create_fields_quote($fields);
	$page = DB::fetch_first('select '. $fields .' from '. DB::table($table) . $where);
	// if ($page['pid']) {
	// 	# code...
	// }
	
	$page['price'] = $page['price'] ? $page['price']: 0;
	$page['status'] = isset($page['status']) ? $page['status']: (AUDIT?0:2);
	$page['pichref'] = $page['pic'] ? $upload_common_path . LO_PIC .$page['pic'] : '';
	$page['pdfhref'] = $page['pdf'] ? $upload_common_path . LO_PDF .$page['pdf'] : '';
	$page['attachhref'] = $page['attach'] ? $upload_common_path . LO_ATTACH .$page['attach']: '';
	if ($table==$tpre.'_category') {
		$cates = plugin_common::get_category($table,'cid,pid,name');
	} else {
		$cates = session_ob($tpre.'_category', 'cid,pid,name', 'order by sort');
	}

	include template('_public/admin_common_op');
	break;

case 'giftop':
// print_r($_POST);
// die;
	plugin_common::common_op($_POST);
	break;

case 'commonlist':
	// 初始化变量
	$leftmenu = 'commonlist';
	// $formcheck['url'] = AURL.'&pluginop=commonpage';
	$oppo = looppo($_REQUEST['sign']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	// $fields = $oppo['fields'];
	$head_title = $oppo['head_title'];
	$jumpext = $oppo['jumpext'];
	// 预设table表格
	$tab['th'] = array('名称','级别','父级id','简述','链接','logo','发布时间','修改时间','操作ip','显隐','排序','操作');
	$tab['td'] = array('name','level','pid','brief','url','pic','addtime','modtime','ip','is_show','sort');
	$tab['operator'] = array(
			// array(AURL.'&pluginop=ajax&opera=show'.$jumpext.'&loid=','显隐'),
			array(AURL.'&pluginop=commonpage'.$jumpext.'&loid=', '编辑'),
			array(AURL.'&pluginop=del'.$jumpext.'&loid=', '删除')
		);
	
	// 数据查询、处理
	$where = array(
			array('start', $_REQUEST['start'], 'addtime'),
			array('end', $_REQUEST['end'], 'addtime'),
			array('key', $_REQUEST['key'], 'name'),
			array('keyword', $_REQUEST['keyword'], 'name,brief,details'),
			array('uid', $_REQUEST['uid'], 'uid'),
			'recycle not like \'%a%\''
		);
	$order = 'sort ASC,modtime DESC,'. $lokey .' DESC';
	$jumpext = 'commonlist'.$jumpext;
	$fields = implode(',', $tab['td']);
	$fields = $fields=='*'?$fields:($lokey?$lokey.','.$fields:$fields);
	$cmt = plugin_common::common_list($table, $pluginvar['pagesize'], $_GET['page'], $where, $order, $jumpext, $fields);
	$multipage = $cmt['multipage'];
	$list = $cmt['list'];

	include template('_public/admin_common_list');
	break;

case 'commonpage':
	// 初始化变量
	$leftmenu = 'commonpage';
	$formcheck['url'] = AURL.'&pluginop=commonop';
	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$oppo = looppo($_REQUEST['sign']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	$fields = $oppo['fields'];
	$head_title = $oppo['head_title'];
	// 数据查询、处理
	$fields_info = session_ob($table, $fields, '', 'column');
	$where = ' where ' . $lokey . '=' . $loid;
	$page = DB::fetch_first('select '.$fields.' from '.DB::table($table) . $where);
	// if ($page['pid']) {
	// 	# code...
	// }
	$page['price'] = $page['price'] ? $page['price']: 0;
	$page['pichref'] = $page['pic'] ? $upload_common_path . LO_PIC .$page['pic'] : '';
	$page['pdfhref'] = $page['pdf'] ? $upload_common_path . LO_PDF .$page['pdf'] : '';
	$page['attachhref'] = $page['attach'] ? $upload_common_path . LO_ATTACH .$page['attach']: '';
	if ($table==$tpre.'_category') {
		$cates = plugin_common::get_category($tpre.'_category','cid,pid,name');
	} else {
		$cates = session_ob($tpre.'_category', 'cid,pid,name', 'order by sort');
	}

	include template('_public/admin_common_op');
	break;

case 'commonop':
// print_r($_POST);
// die;
	plugin_common::common_op($_POST);
	break;
case 'delgift':
	# code...
	$wh = array();
	if ($_GET['loid']) {
		$wh['loids'] = $loid = intval($_GET['loid']);
	} elseif ($_POST['loids']) {
		$wh['loids'] = $loid= $_POST['loids'];
	} else {
		cpmsg('非法操作！', '', 'error');
	}	
	$table = 'activity_gift';
	$wh['lokey'] = 'gid';
	$del_filekey = $oppo['del_filekey'];
	if ($oppo['jumpext']) {
		$jumpext = '&pluginop=gift' . $oppo['jumpext'];
	}
	$skip = array(
			'msgok' => '已删除您所选的数据！',
			'urlok' => '',
			'msgno' => '删除失败！',
			'urlno' => '',
			'ext' => $jumpext
		);
	plugin_common::common_taskdel($table, $wh, '', $skip, $del_filekey);
	break;
case 'delgapply':
	# code...
	$wh = array();
	if ($_GET['loid']) {
		$wh['loids'] = $loid = intval($_GET['loid']);
	} elseif ($_POST['loids']) {
		$wh['loids'] = $loid= $_POST['loids'];
	} else {
		cpmsg('非法操作！', '', 'error');
	}	
	$table = 'activity_apply';
	$wh['lokey'] = 'id';
	$del_filekey = $oppo['del_filekey'];
	if ($oppo['jumpext']) {
		$jumpext = '&pluginop=joinpage' . $oppo['jumpext'];
	}
	$skip = array(
			'msgok' => '已删除您所选的数据！',
			'urlok' => '',
			'msgno' => '删除失败！',
			'urlno' => '',
			'ext' => $jumpext
		);
	plugin_common::common_taskdel($table, $wh, '', $skip, $del_filekey);
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
	plugin_common::common_taskdel($table, $wh, '', $skip, $del_filekey);
	break;

case 'page':
	$leftmenu = 'page';
	$formcheck['url'] = AURL.'&pluginop=op';
	$formcheck['url_category'] = AURL.'&pluginop=commonlist&sign=cg';
	$formcheck['url_comment'] = AURL.'&pluginop=commonlist&sign=comment';
	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$oppo = looppo('', $_REQUEST['table']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	$fields = $oppo['fields'];
	$head_title = $oppo['head_title'];

	// 数据查询、处理
	$page = DB::fetch_first("select ". $fields ." from ".DB::table($table)." where ".$lokey."=".$loid);
	$page['price'] = $page['price'] ? $page['price']: 0;
	$page['status'] = isset($page['status']) ? $page['status']: (AUDIT?2:0);
	// $page['is_show'] = isset($page['is_show']) ? $page['is_show']: (AUDIT?1:0);
	$page['pichref'] = $page['pic'] ? $upload_common_path . LO_PIC .$page['pic']: '';
	$page['pdfhref'] = $page['pdf'] ? $upload_common_path . LO_PDF .$page['pdf']: '';
	$page['attachhref'] = $page['attach'] ? $upload_common_path . LO_ATTACH .$page['attach']: '';

	$cates = session_ob($tpre.'_category', 'cid,pid,name', 'order by sort');

	include template(THISPLUG.':admin_op');
	break;

case 'op':
	plugin_common::common_op($_POST);
	break;

default:
	$leftmenu = 'list';
	$oppo = looppo('', $_REQUEST['table']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	$head_title = $oppo['head_title'];
	$jumpext = $oppo['jumpext'];
	
	// 预设table表格
	$tab['th'] = array('标题','索引','简述','浏览次数','收藏次数','发布时间','修改时间','操作ip','状态','显隐','排序','操作');
	$tab['td'] = array('name','index','brief','view','col','addtime','modtime','ip','status','is_show','sort');
	$tab['operator'] = array(
			// array(AURL.'&pluginop=ajax&opera=show'.$jumpext.'&loid=','显隐'),
			array(AURL.'&pluginop=page'.$jumpext.'&loid=', '编辑'),
			array(AURL.'&pluginop=del'.$jumpext.'&loid=', '删除')
		);

	// 数据查询、处理
	$where = array(
			array('start', $_REQUEST['start'], 'addtime'),
			array('end', $_REQUEST['end'], 'addtime'),
			array('key', $_REQUEST['key'], 'name'),
			array('keyword', $_REQUEST['keyword'], 'name,brief,details'),
			array('uid', $_REQUEST['uid'], 'uid'),
			'recycle not like \'%a%\''
		);
	$order = 'sort ASC,modtime DESC,'. $lokey .' DESC';
	// $fields = $fields=='*'?$fields:($lokey?$lokey.','.$fields:$fields);
	// $join = array(
	// 		array('b','name as cname',$tpre.'_category','cid','=','cid')
	// 	);
	// $cmt = plugin_common::common_list($table, $pluginvar['pagesize'], $_GET['page'], $where, $order, '', '*', $join);
	$cmt = plugin_common::common_list($table, $pluginvar['pagesize'], $_GET['page'], $where, $order);
	$multipage = $cmt['multipage'];
	$list = $cmt['list'];
	include template(THISPLUG.':admin_list');
	break;
}
?>