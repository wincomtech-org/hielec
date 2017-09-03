<?php
if(!defined('IN_DISCUZ'))exit('Access Denied');

// $allow_do = array('up_post','log','del','up','cols','list');
$do = getgpc('do');
$do = $do ? trim($do) : 'list';

$mainurl = $jumpurl = 'home.php?mod=down';
$jumpurl .= '&do='.$do;
$jumpext = '';

$upload_common_path = dirname(LO_URL) . LO_UPLOAD .'down/';// realpath(LO_URL) 会除去 / ./ ../ 根路径
$upload_common_path_op = realpath(DISCUZ_ROOT) . LO_UPLOAD .'down/';// 物理路径
define('IMG_TPL', '/uploads/down/pic/');
// require_once libfile('space/'.$do, 'include');

switch ($do) {
case 'log':
	$SEO['title'] = '下载记录';
	$sql = "SELECT count(*) FROM ".DB::table('down')." as a LEFT JOIN ".DB::table('down_log')." as b ON a.id=b.tid WHERE b.uid=".$gUid." AND b.type ='buy' AND b.status=1 ";
	$pagesize = 6;// 每页记录数
	$multi = plugin_common::pager($sql,$_GET['page'],$jumpurl.$jumpext,$pagesize);
	$multipage = $multi[0];
		// 查询记录集
	$limit = $multi[1];
	// $list = DB::fetch_all(sprintf("SELECT a.name,b.addtime,a.*,b.* FROM %s as a LEFT JOIN %s as b ON a.id=b.tradeid WHERE b.uid=%d AND b.type ='buy' AND b.status=1 ",DB::table('trade'),DB::table('trade_log'),$gUid));
	$list = DB::fetch_all(sprintf("SELECT a.id as gid,a.name,b.addtime,c.username,b.id,b.cos FROM %s as a LEFT JOIN %s as b ON a.id=b.tid LEFT join %s as c on a.author_id=c.uid WHERE b.uid=%d AND b.type ='down' AND b.status=1 %s",DB::table('down'),DB::table('down_log'),DB::table('common_member'),$gUid,$limit));

	// $selid = DB::fetch_all(sprintf("SELECT id FROM %s WHERE uid = %s  ",DB::table('trade'),$gUid));
	// $ids="(";
	// foreach ($selid as $key => $value) {
	// 	# code...
	// 	$ids.=$value['id'].",";
	// }
	// $ids = substr($ids,0,-1).')';
	// $list1 = DB::fetch_all(sprintf("SELECT a.name,b.addtime,c.username,b.id FROM %s as a LEFT JOIN %s as b ON a.id=b.tradeid  LEFT join %s as c on a.uid=c.uid  WHERE b.tradeid in %s AND b.type ='%s' AND b.status=1 ",DB::table('trade'),DB::table('trade_log'),DB::table('common_member'),$ids,'buy'));
	break;

case 'cols':
	$SEO['title'] = '我的收藏';
	$wh=" SELECT count(*) FROM ".DB::table('down')." as a LEFT JOIN ".DB::table('down_log')." as b ON a.id=b.tid WHERE b.uid=".$gUid." AND b.type ='col'   ";
	$pagesize = 6;// 每页记录数
	$multi = plugin_common::pager($wh,$_GET['page'],$jumpurl.$jumpext,$pagesize);
	$multipage = $multi[0];
	// 查询记录集
	$limit = $multi[1];	
	$list = DB::fetch_all(sprintf("SELECT a.*,b.*,a.id as gid FROM %s as a LEFT JOIN %s as b ON a.id=b.tid WHERE b.uid=%d AND b.type ='col' %s ",DB::table('down'),DB::table('down_log'),$gUid,$limit));
	break;

case 'del':
	$id = getgpc('id');
	$id = $id ? intval($id) : 0;
	if (empty($id)) {
		showmessage('ID非法！',$jumpurl);
	}
	$table = $_GET['tab'] ? $_GET['tab'] : 'down';
	$url = $_GET['url'] ? $mainurl.'&do='.$_GET['url'] : $_SERVER['HTTP_REFERER'];
	$del = DB::delete($table," id=".$id);
	if($del){
		showmessage('删除成功',$url);
	}else{
		showmessage('删除失败',$url);
	}
	break;

case 'up':
	// 表单验证 前端
	$Validform['name'] = 'datatype="s3-16" errormsg="名称至少3个字符,最多16个字符！"';
	// $Validform['tags'] = 'datatype="s3-16" errormsg="名称至少3个字符,最多16个字符！"';
	$Validform['price'] = 'datatype="f2" errormsg="允许两位小数"';
	$Validform['brief'] = 'tip="请在这里输入您的简述。" datatype="*2-200" errormsg="请填写至少2个字符,最多200个字符！" altercss="gray" class="gray"';
	$Validform['details'] = 'datatype="*2-2000"';
	// $Validform['url'] = 'datatype="url"';

	$SEO['title'] = '资料上传';
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

	// 我的上传
	$page = is_null($_GET['page'])?null:$_GET['page'];
	$multi  = plugin_common::pager(sprintf("SELECT count(*) from %s where author_id=%d",DB::table('down'),$gUid),$page,$jumpurl,$pagesize);
	$multipage = $multi[0];
	$limit = $multi[1];
	$my_downs_c = DB::fetch_all(sprintf('SELECT id,name,price,status,cols,downs,addtime,is_show from %s where author_id=%d %s',DB::table('down'),$gUid,$limit));
	foreach ($my_downs_c as $row) {
		$row['addtime'] = isset($row['addtime']) ? dgmdate($row['addtime'],'d') : '' ;
		if (isset($row['status'])) $row['status'] = plugin_common::replace_preg($row['status'], $lostatus);
		$row['is_show'] = isset($row['is_show']) ? '显示' : '隐藏' ;
		$my_downs[] = $row;
	}
	break;

case 'up_post':	
	if (empty($_POST['name'])) {
		showmessage('名称不能为空');
	}

	$cids[] = $_POST['resource_property'];
	$cids[] = $_POST['module'];
	$cids[] = $_POST['software'];
	$cids[] = $_POST['program_language'];
	$cids[] = $_POST['hot_skill'];
	$cids[] = $_POST['use'];
	$cids[] = $_POST['eletron_basis'];
	$cids = (is_array($cids)) ? json_encode($cids) : trim($cids);
	// debug($cids,1);

	if ($_FILES['pic']['name']) {
		$pic = plugin_common::upload_file('pic',$upload_common_path_op . LO_PIC);		
	}
	if ($_FILES['attach']['name']) {
		$attach = plugin_common::upload_file('attach', $upload_common_path_op . LO_ATTACH);	
	}

	$data = array(
			'name'		=> $_POST['name'],
			'tags'		=> $_POST['tags'],
			'price'		=> $_POST['price'],
			'brief'		=> $_POST['brief'],
			'details'	=> $_POST['details'],
			'url'		=> $_POST['url'],
			'cid'		=> $cids,
			'author_id'	=> $gUid,
			'modtime'	=> CURTIME,
		);

	$id = getgpc('id');
	$id = $id ? intval($id) : 0;
	if ($pic) 
		$data['pic'] = $pic;
	if ($attach) 
		$data['attach'] = $attach;

	if (empty($id)) {
		$data['addtime'] = CURTIME;
		$data['downs'] = 0;
		$i_id = DB::insert('down',$data,true);
	} else {
		$condition = array('id'=>$id);
		$i_id = DB::update('down',$data,$condition);
	}
	if ($i_id) {
		showmessage('提交成功',$mainurl.'&do=list');
	}else{
		showmessage('提交失败',$mainurl.'&do=list');
	}
	break;
	
default:
	$SEO['title'] = '资料下载';
	$wh = " SELECT count(*) FROM ".DB::table('down')." WHERE author_id=".$gUid." AND recycle='' ";
	$pagesize = 20;// 每页记录数
	$multi = plugin_common::pager($wh,$_GET['page'],$jumpurl.$jumpext,$pagesize);
	$multipage = $multi[0];
	// 查询记录集
	$limit = $multi[1];
	$list =  DB::fetch_all(sprintf(" SELECT name,id FROM %s WHERE author_id=%s AND recycle='' %s",DB::table('down'),$gUid,$limit));
	break;
}

include_once template(TEMPDIR);
?>
