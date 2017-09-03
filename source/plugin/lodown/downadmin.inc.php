<?php
//后台下载
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
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

//分类列表页
case 'commonlist':
	// 初始化变量
	$leftmenu = 'commonlist';
	// $formcheck['url'] = AURL.'&pluginop=commonpage';
	$oppo = looppo($_REQUEST['sign']);
	//var_dump($_REQUEST['sign']);die;
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	// $fields = $oppo['fields'];
	$head_title = $oppo['head_title'];
	$jumpext = $oppo['jumpext'];
	// 预设table表格
	$tab['th'] = array('名称','类型','发布时间','修改时间','操作ip','显隐','排序','操作');
	$tab['td'] = array('name','type','addtime','modtime','ip','is_show','sort');
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
	$cmt = plugin_common::common_list($table, $pagesize, $_GET['page'], $where, $order, $jumpext, $fields);
	$multipage = $cmt['multipage'];
	$list = $cmt['list'];

	// include template('_public/admin_common_list');
	include template(THISPLUG.':admin_cate_list');
	break;

//分类页编辑
case 'commonpage':
	// 初始化变量
	$leftmenu = 'commonpage';
	$formcheck['url'] = AURL.'&pluginop=commonop';
	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$oppo = looppo($_REQUEST['sign']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	// $fields = $oppo['fields'];
	$fields = 'name,brief,is_show,sort';
	$head_title = $oppo['head_title'];
	// 数据查询、处理
	$fields_info = session_ob($table, $fields, '', 'column');
	$where = ' where ' . $lokey . '=' . $loid;
	$fields=$fields.",type";

	$page = DB::fetch_first('select '.$fields.' from '.DB::table($table) . $where);

	$page['price'] = $page['price'] ? $page['price']: 0;
	$page['pichref'] = $page['pic'] ? $upload_common_path . LO_PIC .$page['pic'] : '';
	$page['pdfhref'] = $page['pdf'] ? $upload_common_path . LO_PDF .$page['pdf'] : '';
	$page['attachhref'] = $page['attach'] ? $upload_common_path . LO_ATTACH .$page['attach']: '';
	if ($table==$tpre.'_category') {
		$cates = plugin_common::get_category($tpre.'_category','cid,pid,name');
	} else {
		$cates = session_ob($tpre.'_category', 'cid,pid,name', 'order by sort');
	}

	include template(THISPLUG.':admin_cate_op');
	// include template('_public/admin_common_op');
	break;

case 'commonop':
// print_r($_POST);
// die;
	plugin_common::common_op($_POST);
	break;

//删除
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

//列表页编辑
case 'page':
	$leftmenu = 'page';
	$formcheck['url'] = AURL.'&pluginop=op';
	$formcheck['url_category'] = AURL.'&pluginop=commonlist&sign=cg';
	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$oppo = looppo('', $_REQUEST['table']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	$fields = $oppo['fields'];
	$head_title = $oppo['head_title'];
	// 数据查询、处理
	$page = DB::fetch_first("select ". $fields ." from ".DB::table($table)." where ".$lokey."=".$loid);
	//var_dump($page);die;
	$page['price'] = $page['price'] ? $page['price']: 0;
	$page['status'] = isset($page['status']) ? $page['status']: (AUDIT?0:2);
	$page['pichref'] = $page['pic'] ? $upload_common_path . LO_PIC .$page['pic']: '';
	$page['pdfhref'] = $page['pdf'] ? $upload_common_path . LO_PDF .$page['pdf']: '';
	$page['attachhref'] = $page['attach'] ? $upload_common_path . LO_ATTACH .$page['attach']: '';

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

	include template(THISPLUG.':admin_op');
	break;

case 'op':
	$oppo = looppo('', $_REQUEST['table']);
	$table = $oppo['table'];
	$wh = array();

	//防止数据重复提交
	if( $_POST['formhash']==formhash() ) {
		extract($_POST);
		// 字段验证
		$loid = $loid ? intval($loid) : '';
		
		if (empty($name)) {
			cpmsg('分类名称不能为空！','','error');
		} else {
			$name = trim($name);
		}
		// 文件上传
		if ($_FILES['pic']['name']) {
			$pic = plugin_common::upload_file('pic', $upload_common_path_op . LO_PIC, 'time', true);
		}
		if ($_FILES['pdf']['name']) {
			$pdf = plugin_common::upload_file('pdf', $upload_common_path_op . LO_PDF);
		}
		if ($_FILES['attach']['name']) {
			$attach = plugin_common::upload_file('attach', $upload_common_path_op . LO_ATTACH, 'time', null, '', $pluginvar['filetype']);
		}
		// 预处理值
		$jumpurl = $loid ? '&pluginop=page&loid='.$loid : '';

		$cids[] = $resource_property;
		$cids[] = $module;
		$cids[] = $software;
		$cids[] = $program_language;
		$cids[] = $hot_skill;
		$cids[] = $use;
		$cids[] = $eletron_basis;
		$cids = (is_array($cids)) ? json_encode($cids) : trim($cids);
		// $data['cid'] = $data['cid'] ? serialize($data['cid']) : $data['cid'];
		// debug($cids,1);

		$data = array(
				'name'		=> $name,
				'tags'		=> $tags,
				'price'		=> $price,
				'tags'		=> $tags,
				'brief'		=> $brief,
				'details'	=> $details,
				'url'		=> $url,
				'status'	=> $status,
				'cid'		=> $cids,
				'author_id'	=> $_G['adminid'],
				'ip'		=> CURIP,
				'modtime'	=> CURTIME,
			);

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
	$tab['th'] = array('标题','下载积分','简述','附件','发布时间','修改时间','操作ip','状态','显隐','排序','操作');
	$tab['td'] = array('name','price','brief','attach','addtime','modtime','ip','status','is_show','sort');
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
			array('keyword', $_REQUEST['keyword'], 'name,tags,brief,details'),
			array('uid', $_REQUEST['uid'], 'uid'),
			'recycle not like \'%a%\''
		);
	$order = 'sort ASC,modtime DESC,'. $lokey .' DESC';
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