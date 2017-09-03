<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

require_once dirname(__FILE__).'/common.php';

die('暂时关闭');
/*
 * 分支
*/
switch ($_REQUEST['pluginop']) {
case 'ajax':
	# code...
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
	$tab['th'] = array('名称','简述','链接','logo','发布时间','修改时间','操作ip','显隐','排序','操作');
	$tab['td'] = array('name','brief','url','pic','addtime','modtime','ip','is_show','sort');
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
	// $cmt = plugin_common::common_list($table, $pluginvar['pagesize'], $_GET['page'], $where, $order, 'commonlist'.$jumpext);
	$cmt = plugin_common::common_list($table, $pluginvar['pagesize'], $_GET['page'], $where, $order, $jumpext, $fields);
	$multipage = $cmt['multipage'];
	$list = $cmt['list'];

	include template('_public/admin_common_list');
	// include template(THISPLUG.':admin_common_list');
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
// print_r($fields_info);
	$where = ' where ' . $lokey . '=' . $loid;
	$page = DB::fetch_first('select '.$fields.' from '.DB::table($table) . $where);
	// $page['price'] = $page['price'] ? $page['price']: 0;
	$page['pichref'] = $page['pic'] ? $upload_common_path . LO_PIC .$page['pic'] : '';
	$page['pdfhref'] = $page['pdf'] ? $upload_common_path . LO_PDF .$page['pdf'] : '';
	if ($table==$tpre.'_category') {
		$cates = plugin_common::get_category($tpre.'_category','cid,pid,name');
	} else {
		$cates = session_ob($tpre.'_category', 'cid,pid,name', 'order by sort');
	}
	include template('_public/admin_common_op');
	// include template(THISPLUG.':admin_common_op');
	break;

case 'commonop':
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
	plugin_common::common_taskdel($table, $wh, '', $skip, $del_filekey);
	// $res = plugin_common::common_taskdel($table, $wh, '', $skip, $del_filekey);
	// var_dump($res);
	break;

case 'page':
	// 表单验证 前端
	$Validform['name'] = 'datatype="s3-16" errormsg="名称至少3个字符,最多16个字符！"';
	// $Validform['index'] = 'datatype="*"';
	// $Validform['tags'] = '';
	$Validform['price'] = 'datatype="f2" errormsg="允许两位小数"';
	// $Validform['cid'] = 'datatype="select" errormsg="请选择分类！"';
	// $Validform['fid'] = 'datatype="radio" errormsg="请选择厂商！"';
	$Validform['brief'] = 'datatype="*4-200" tip="请在这里输入您的简述。" altercss="gray" class="gray"';
	$Validform['details'] = '';
	// $Validform['url'] = '';
	// $Validform['pic'] = '';

	$leftmenu = 'page';
	$formcheck['url'] = AURL.'&pluginop=op';
	$formcheck['url_category'] = AURL.'&pluginop=commonlist&sign=cg';
	$formcheck['url_firm'] = AURL.'&pluginop=commonlist&sign=firm';
	// $formcheck['sid'] = $_SESSION['sid'] = plugin_common::locrs();
	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$oppo = looppo('', $_REQUEST['table']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	$fields = $oppo['fields'];
	$head_title = $oppo['head_title'];

	// 数据查询、处理
	$page = DB::fetch_first("select ". $fields ." from ".DB::table($table)." where ".$lokey."=".$loid);
	$page['price'] = $page['price'] ? $page['price']: 0;
	$page['status'] = isset($page['status']) ? $page['status']: (AUDIT?0:2);
	$page['pichref'] = $page['pic'] ? $upload_common_path . LO_PIC .$page['pic']: '';
	$page['pdfhref'] = $page['pdf'] ? $upload_common_path . LO_PDF .$page['pdf']: '';

	$cates = session_ob($tpre.'_category', 'cid,name', 'order by sort');
	$firms = session_ob($tpre.'_firm', 'cid,name', 'order by sort');
	// setcookie('firms', serialize($firms));
// print_r($page);
// print_r($cates);

	include template(THISPLUG.':admin_op');
	break;

case 'op':
	$oppo = looppo('', $_REQUEST['table']);
	$table = $oppo['table'];
	$wh = array();
	//防止数据重复提交
	if( $_POST['formhash']==formhash() ){
		// 字段过滤
		$data = $_POST;
		unset($data['formhash'],$data['loid']);
		// 字段验证
		$loid = $_POST['loid'] ? intval($_POST['loid']) : '';
		$name = $data['name'] ? trim($data['name']) : '';
		if (empty($name)) {
			cpmsg('型号不能为空！','','error');
		}
		// 文件上传
		if ($_FILES['pic']['name']) {
			$data['pic'] = plugin_common::upload_file('pic', $upload_common_path_op . LO_PIC, 'time', true);
		}
		if ($_FILES['pdf']['name']) {
			$data['pdf'] = plugin_common::upload_file('pdf', $upload_common_path_op . LO_PDF);
		}
		// 预处理值
		$jumpurl = $loid ? '&pluginop=page&loid='.$loid : '';
		// $data['cid'] = $data['cid'] ? serialize($data['cid']) : $data['cid'];
		$data['cid'] = (is_array($data['cid'])) ? json_encode($data['cid']) : trim($data['cid']);
		$data['fid'] = (is_array($data['fid'])) ? json_encode($data['fid']) : trim($data['fid']);
		$data['author_id'] = $_G['adminid'];
		$data['ip'] = CURIP;
		$data['modtime'] = CURTIME;
		// 新增、编辑非公共区域
		if ($loid) {
			$wh['id'] = $loid;
		} else {
			$vdata = DB::fetch_first("SELECT name,price FROM ".DB::table($table)." WHERE name='$name'");
			if ($vdata) {
				cpmsg('对不起，该型号已存在！','','error');
			}
			$data['addtime'] = CURTIME;
		}
		// 异常处理
		try {
			if ($loid) {
				$affected_rows = DB::update($table,$data,$wh);
				if (!$affected_rows) {
					throw new Exception('更新失败');
				}
			} else {
				$insert_id = DB::insert($table,$data,true);
				if (!$insert_id) {
					throw new Exception('添加失败');
				}
			}
			// 发布记录
			session_ob($table, '*', '', 'del');
			header("Location:".AURL);exit;
			// cpmsg('提交成功！', AURLJUMP, 'success');
		} catch (Exception $e) {
			cpmsg( $e->getMessage(), AURLJUMP.$jumpurl, 'error' );
		}
	} else {
		cpmsg('抱歉，您的请求来路不正确或表单验证串不符，无法提交', '', 'error');
	}
	break;

default:
	$leftmenu = 'list';
	// $formcheck = '';
	// $table = looppo()['table'];// php5.5以下版本不兼容
	$oppo = looppo('', $_REQUEST['table']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	$head_title = $oppo['head_title'];
	$jumpext = $oppo['jumpext'];

	// 预设table表格
	$tab['th'] = array('型号','索引','下载积分','简述','图片','PDF','发布时间','修改时间','操作ip','显隐','排序','操作');
	$tab['td'] = array('name','index','price','brief','pic','pdf','addtime','modtime','ip','is_show','sort');
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
	// $fields = implode(',', $tab['td']);
	// $fields = $oppo['fields'];
	// $fields = 'name,brief,url,pic,pdf,supports,unsupports,addtime,modtime,ip,is_show,sort,price,author_id';
	// $fields = $fields=='*'?$fields:($lokey?$lokey.','.$fields:$fields);
	$join = array(
			array('b','name as cname',$tpre.'_category','cid','=','cid'),
			array('c','name as fname',$tpre.'_firm','fid','=','cid')
		);
	$cmt = plugin_common::common_list($table, $pluginvar['pagesize'], $_GET['page'], $where, $order, '', '*', $join);
	$multipage = $cmt['multipage'];
	$list = $cmt['list'];

	include template(THISPLUG.':admin_list');
	break;
}
?>