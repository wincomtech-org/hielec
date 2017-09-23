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

case 'message':
	// 初始化变量
	$leftmenu = 'list';
	$table = $tpre.'_message';
	$lokey = 'id';
	$head_title = '留言';
	// 预设table表格
	$tab['th'] = array('ID','活动名称','信息','添加人','添加时间','操作');
	$tab['td'] = array('id','name','message','username','addtime');
	$tab['operator'] = array(
			// array(AURL.'&pluginop=ajax&opera=show'.$jumpext.'&loid=','显隐'),
			array(AURL.'&pluginop=del&mark=article&del_filekey=pic&loid=', '删除')
		);
	// 数据查询、处理
	$where = array(
			array('start', $_REQUEST['start'], 'addtime'),
			array('end', $_REQUEST['end'], 'addtime'),
			array('key', $_REQUEST['key'], 'name'),
			array('keyword', $_REQUEST['keyword'], 'name,desc'),
			array('uid', $_REQUEST['uid'], 'uid')
		);
	$order = $lokey .' DESC';
	$jumpext = 'message'.$jumpext;
	$fields = implode(',', $tab['td']);
	$fields = $fields=='*'?$fields:($lokey?$lokey.','.$fields:$fields);
	$join = array(
			array('b','name',$tpre,'trade_id','=','id'),
			array('c','username','common_member','uid','=','uid')
		);
	$cmt = plugin_common::common_list($table, $pluginvar['pagesize'], $_GET['page'], $where, $order, '', '*', $join);
	$multipage = $cmt['multipage'];
	$list = $cmt['list'];

	include template('_public/admin_common_list');
	// include template(THISPLUG.':admin_common_list');
	break;

case 'brand':
	// 初始化变量
	$leftmenu = 'list';
	// $formcheck['url'] = AURL.'&pluginop=commonpage';
	// $oppo = looppo($_REQUEST['pluginop']);
	$table = $tpre.'_brand';
	$lokey = 'id';
	$head_title = '品牌';
	// 预设table表格
	$tab['th'] = array('名称','logo','相关链接','是否热门','排序','操作');
	$tab['td'] = array('name','logo','url','is_hot','sort');
	$tab['operator'] = array(
			// array(AURL.'&pluginop=ajax&opera=show'.$jumpext.'&loid=','显隐'),
			array(AURL.'&pluginop=brandpage'.$jumpext.'&loid=', '编辑'),
			array(AURL.'&pluginop=del&mark=brand&del_filekey=pic&loid=', '删除')
			// array(AURL.'&pluginop=del'.$jumpext.'&loid=', '删除')
		);

	// 数据查询、处理
	$where = array(
			array('start', $_REQUEST['start'], 'addtime'),
			array('end', $_REQUEST['end'], 'addtime'),
			array('key', $_REQUEST['key'], 'name'),
			array('keyword', $_REQUEST['keyword'], 'name,desc'),
			array('uid', $_REQUEST['uid'], 'uid')
		);
	$order = 'sort ASC,'. $lokey .' DESC';
	$jumpext = 'brand'.$jumpext;
	$fields = implode(',', $tab['td']);
	$fields = $fields=='*'?$fields:($lokey?$lokey.','.$fields:$fields);
	$cmt = plugin_common::common_list($table, $pluginvar['pagesize'], $_GET['page'], $where, $order, $jumpext, $fields);
	$multipage = $cmt['multipage'];
	$list = $cmt['list'];

	include template('_public/admin_common_list');
	// include template(THISPLUG.':admin_common_list');
	break;

case 'brandpage':
	// 初始化变量
	$formcheck['url'] = AURL.'&pluginop=op';
	$formcheck['url_return_ok'] = '&pluginop=brand';
	$formcheck['url_return_no'] = '&pluginop=brandpage';
	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$table = $tpre.'_brand';
	$lokey = 'id';
	$head_title = '品牌';
	$fields = 'name,cat_pid,cat_id,logo,`desc`,url,is_hot,sort';
	// $fields = plugin_common::create_fields_quote($fields);

	// 数据查询、处理
	$where = ' where ' . $lokey . '=' . $loid;
	$page = DB::fetch_first('select '.$fields.' from '.DB::table($table) . $where);
	$page['logohref'] = $page['logo'] ? $upload_common_path . LO_PIC .$page['logo'] : '';
	// $cates = plugin_common::get_category($table,'cid,pid,name');

	include template(THISPLUG.':admin_brand_op');
	break;

case 'cg':
	// 初始化变量
	$leftmenu = 'commonlist';
	// $formcheck['url'] = AURL.'&pluginop=commonpage';
	$oppo = looppo($_REQUEST['pluginop']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	// $fields = $oppo['fields'];
	$head_title = '商品分类';
	// $jumpext = $oppo['jumpext'];
	// 预设table表格
	$tab['th'] = array('名称','级别','是否显示','是否热门','分类图片','排序','操作');
	$tab['td'] = array('name','level','is_show','is_hot','pic','sort');
	$tab['operator'] = array(
			array(AURL.'&pluginop=cgpage'.$jumpext.'&loid=', '编辑'),
			array(AURL.'&pluginop=del&mark=cg&loid=', '删除')
			// array(AURL.'&pluginop=del'.$jumpext.'&loid=', '删除')
		);

	// 数据查询、处理
	$where = array(
			array('start', $_REQUEST['start'], 'addtime'),
			array('end', $_REQUEST['end'], 'addtime'),
			array('key', $_REQUEST['key'], 'name'),
			array('keyword', $_REQUEST['keyword'], 'name'),
			array('uid', $_REQUEST['uid'], 'uid')
		);
	$order = 'sort ASC,'. $lokey .' DESC';
	$jumpext = 'cg'.$jumpext;
	$fields = implode(',', $tab['td']);
	$fields = $fields=='*'?$fields:($lokey?$lokey.','.$fields:$fields);
	// $cmt = plugin_common::common_list($table, $pluginvar['pagesize'], $_GET['page'], $where, $order, 'commonlist'.$jumpext);
	$cmt = plugin_common::common_list($table, $pluginvar['pagesize'], $_GET['page'], $where, $order, $jumpext, $fields);
	$multipage = $cmt['multipage'];
	$list = $cmt['list'];

	include template('_public/admin_common_list');
	// include template(THISPLUG.':admin_common_list');
	break;

case 'cgpage':
	// 初始化变量
	$leftmenu = 'categorypage';
	$formcheck['url'] = AURL.'&pluginop=op';
	$formcheck['url_return_ok'] = '&pluginop=cg';
	$formcheck['url_return_no'] = '&pluginop=cgpage';
	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$oppo = looppo('cg');
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	$fields = 'name,pid,level,pic,is_show,is_hot,sort';
	$head_title = $oppo['head_title'];

	// 数据查询、处理
	$fields_info = session_ob($table, $fields, '', 'column');
	$where = ' where ' . $lokey . '=' . $loid;
	$page = DB::fetch_first('select '.$fields.' from '.DB::table($table) . $where);
	$page['pichref'] = $page['pic'] ? $upload_common_path . LO_PIC .$page['pic'] : '';
	$cates = plugin_common::get_category($table,'cid,pid,name');
	include template('_public/admin_common_op');
	// include template(THISPLUG.':admin_cg_op');
	break;

case 'op':
// debug($_POST,1);
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

	// 选择器
	switch ($_GET['mark']) {
		case 'brand':
			$table = 'trade_brand';// $tpre.'_brand'
			$wh['lokey'] = 'id';
			// $del_filekey = 'logo';
			$is_del = '';
			$jumpext = '&pluginop=brand' . $oppo['jumpext'];
			break;
		case 'cg':
			$table = 'trade_category';
			$wh['lokey'] = 'cid';
			$del_filekey = 'pic';
			$is_del = '';
			$jumpext = '&pluginop=cg' . $oppo['jumpext'];
			break;
		case 'article':
			$table = 'trade_message';
			$wh['lokey'] = 'id';
			$del_filekey = '';
			$is_del = '';
			$jumpext = '&pluginop=article' . $oppo['jumpext'];
			break;
		default:
			$oppo = looppo($_REQUEST['sign'], $_REQUEST['table']);
			$table = $oppo['table'];
			$wh['lokey'] = $oppo['lokey'];
			// $del_filekey = $_GET['del_filekey'] ? trim($_GET['del_filekey']) : $oppo['del_filekey'];
			if ($oppo['jumpext']) {
				$jumpext = '&pluginop=commonlist' . $oppo['jumpext'];
			}
			$is_del = '';
			break;
	}
	$skip = array(
			'msgok' => '已删除您所选的数据！',
			'urlok' => '',
			'msgno' => '删除失败！',
			'urlno' => '',
			'ext' => $jumpext
		);
	plugin_common::common_taskdel($table, $wh, $is_del, $skip, $del_filekey);
	break;

case 'page':
	$leftmenu = 'page';
	$formcheck['url'] = AURL.'&pluginop=op';
	$formcheck['url_category'] = AURL.'&pluginop=cg';
	$formcheck['url_brand'] = AURL.'&pluginop=brand';
	// $formcheck['sid'] = $_SESSION['sid'] = plugin_common::locrs();
	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$oppo = looppo();
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	$fields = $oppo['fields'];
	$head_title = '商品';

	// 数据查询、处理
	// $fields_info = session_ob($table, $fields, '', 'column');
	// echo "<pre>";
	// print_r($fields_info);die;
	$page = DB::fetch_first("select ". $fields ." from ".DB::table($table)." where ".$lokey."=".$loid);
	$page['price'] = $page['price'] ? $page['price']: 0;
	$page['status'] = isset($page['status']) ? $page['status']: (AUDIT?0:2);
	$page['pichref'] = $page['pic'] ? $upload_common_path . LO_PIC .$page['pic']: '';
	// $page['pdfhref'] = $page['pdf'] ? $upload_common_path . LO_PDF .$page['pdf']: '';

	$cates = plugin_common::get_category($tpre.'_category','cid,pid,name');

	$brands = session_ob($tpre.'_brand', 'id,name', 'order by sort');

	$district = DB::fetch_all("SELECT `id`,`name` from ".DB::table('common_district')." WHERE level=1");//地区
	$trade_type1 =  DB::fetch_all("SELECT `id`,`name` from ".DB::table('trade_type')." WHERE type=1");//交易类型
	$trade_type3 =  DB::fetch_all("SELECT `id`,`name` from ".DB::table('trade_type')." WHERE type=3");//交易状态

	include template(THISPLUG.':admin_op');
	// include template('_public/admin_common_op');
	break;

default:
	$leftmenu = 'list';
	// $formcheck = '';
	// $table = looppo()['table'];// php5.5以下版本不兼容
	$oppo = looppo('', $_REQUEST['table']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	$head_title = $oppo['head_title'];
	// 预设table表格
	$tab['th'] = array('名称','商品编号','售价','点击数','库存数量','商品销量','是否上架','是否推荐','是否新品','是否热卖','状态','排序','操作');
	$tab['td'] = array('name','goods_sn','shop_price','click_count','store_count','sales_sum','is_on_sale','is_recommend','is_new','is_hot','status','sort');
	$tab['operator'] = array(
			// array(AURL.'&pluginop=ajax&opera=show'.$jumpext.'&loid=','显隐'),
			array(AURL.'&pluginop=page'.$jumpext.'&loid=', '编辑'),
			// array(AURL.'&pluginop=del&is_del=1&del_filekey=pic&loid=', '删除')
			array(AURL.'&pluginop=del'.$jumpext.'&loid=', '删除')
		);

	// 数据查询、处理
	$where = array(
			// array('start', $_REQUEST['start'], 'addtime'),
			// array('end', $_REQUEST['end'], 'addtime'),
			// array('key', $_REQUEST['key'], 'name'),
			// array('keyword', $_REQUEST['keyword'], 'name,tags,brief,details'),
			array('uid', $_REQUEST['uid'], 'uid'),
			'reycle not like \'%a%\''
		);
	$order = ' sort ASC,modtime DESC,'. $lokey .' DESC';
	// $fields = $fields=='*'?$fields:($lokey?$lokey.','.$fields:$fields);
	$join = array(
	// 		array('b','name as cname',$tpre.'_category','cid','=','cid')
		);
	// $cmt = plugin_common::common_list($table, $pluginvar['pagesize'], $_GET['page'], $where, $order, '', '*', $join);
	$cmt = plugin_common::common_list($table, $pluginvar['pagesize'], $_GET['page'], $where, $order, '', '*','');
	$multipage = $cmt['multipage'];
	$list = $cmt['list'];

	include template(THISPLUG.':admin_list');
	break;
}
?>