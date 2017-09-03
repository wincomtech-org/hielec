<?php
if(!defined('IN_DISCUZ'))exit('Access Denied');

// $allow_do = array('up_post','log','del','up','cols','list');
$do = getgpc('do');
$do = $do ? trim($do) : 'list';

$mainurl = $jumpurl = 'home.php?mod=activity';
$jumpurl .= '&do='.$do;
$jumpext = '';

// $upload_common_path = dirname(LO_URL) . LO_UPLOAD .'activity/';// realpath(LO_URL) 会除去 / ./ ../ 根路径
// $upload_common_path_op = realpath(DISCUZ_ROOT) . LO_UPLOAD .'activity/';// 物理路径
// define('IMG_TPL', '/uploads/activity/pic/');
// require_once libfile('space/'.$do, 'include');


switch ($do) {
case 'apply':
	$SEO['title'] = '活动报名';
	$loid= $_GET['id']?intval($_GET['id']):0;
	$pag = DB::fetch_first("select id from ".DB::table('activity_apply')." where activityid=".$loid." and uid=".$_G['uid']);
	if($pag[id]){
		showmessage('您已申请该活动',$mainurl);
	}else{
		$list = DB::fetch_first("select * from ".DB::table('activity')." where id=".$loid);
	}
	break;

case 'apply_post':
	$_POST['uid']=$_G['uid'];
	$insert_id = DB::insert('activity_apply',$_POST,true);
	if ($insert_id) {
		showmessage('申请成功',$jumpurl);
	}else{
		showmessage('申请失败',$jumpurl);
	}
	break;

case 'del':
	// $str=DB::update('activity_log', array('is_show'=>'0'), array('id'=>$id));
	$del=DB::delete($_GET['tab']," id = '$_GET[id]'");
	if($del){
		showmessage('删除成功',$mainurl.'&do='.$_GET['url']);
	}else{
		showmessage('删除失败',$mainurl.'&do='.$_GET['url']);
	}	
	break;

case 'exchange':
	$SEO['title'] = '兑换记录';
	$wh="SELECT count(*) FROM ".DB::table('activity')." as a LEFT JOIN ".DB::table('activity_log')." as b ON a.id=b.gid WHERE b.uid=".$_G['uid']." AND b.type ='exchange' AND b.status=1 AND a.is_show =1 ";

	$pagesize = 6;// 每页记录数
	$query = DB::query($wh);
	$amount = DB::result($query, 0);// 查询记录总数
	$pagecount = $amount?(($amount<$pagesize)?1:(($amount%$pagesize)?((int)($amount/$pagesize)+1):($amount/$pagesize))):0;// 计算总页数
	$page = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
	$page = $page > $pagecount ? 1 : $page;// 取得当前页值
	$startlimit = ($page - 1) * $pagesize;// 查询起始的偏移量
	$multipage = multi($amount, $pagesize, $page, $jumpurl.$jumpext, $pagecount);// 显示分页
	// 查询记录集
	$list = DB::fetch_all(sprintf("SELECT a.id as gid,a.name,a.addtime,a.endtime,a.status,b.id,b.credits FROM %s as a LEFT JOIN %s as b ON a.id=b.gid WHERE b.uid=%d AND b.type ='%s' AND b.status=1 AND a.is_show =1 limit %s,%s ",DB::table('activity'),DB::table('activity_log'),$_G['uid'],'exchange',$startlimit,$pagesize));
	break;

case 'cols':
	$SEO['title'] = '活动收藏';
	$wh="SELECT count(*) FROM ".DB::table('activity')." as a LEFT JOIN ".DB::table('activity_log')." as b ON a.id=b.gid WHERE b.uid=".$_G['uid']." AND b.type ='col' AND a.is_show=1";
	$pagesize = 6;// 每页记录数
	$query = DB::query($wh);
	$amount = DB::result($query, 0);// 查询记录总数
	$pagecount = $amount?(($amount<$pagesize)?1:(($amount%$pagesize)?((int)($amount/$pagesize)+1):($amount/$pagesize))):0;// 计算总页数
	$page = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
	$page = $page > $pagecount ? 1 : $page;// 取得当前页值
	$startlimit = ($page - 1) * $pagesize;// 查询起始的偏移量
	$multipage = multi($amount, $pagesize, $page, $jumpurl.$jumpext, $pagecount);// 显示分页
	// 查询记录集
	$list = DB::fetch_all(sprintf("SELECT a.id as gid,a.name,a.addtime,a.endtime,a.status,b.id FROM %s as a LEFT JOIN %s as b ON a.id=b.gid WHERE b.uid=%d AND b.type ='%s' AND a.is_show =1 limit %s,%s ",DB::table('activity'),DB::table('activity_log'),$_G['uid'],'col',$startlimit,$pagesize));
	break;
	
default:
	$SEO['title'] = '参加活动';
	$wh=" SELECT count(*) FROM ".DB::table('activity')." as a LEFT JOIN ".DB::table('activity_apply')." as b ON a.id=b.activityid WHERE b.uid=".$_G['uid']." AND a.is_show =1   ";
	$pagesize = 10;// 每页记录数
	$query = DB::query($wh);
	$amount = DB::result($query, 0);// 查询记录总数
	$pagecount = $amount?(($amount<$pagesize)?1:(($amount%$pagesize)?((int)($amount/$pagesize)+1):($amount/$pagesize))):0;// 计算总页数
	$page = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
	$page = $page > $pagecount ? 1 : $page;// 取得当前页值
	$startlimit = ($page - 1) * $pagesize;// 查询起始的偏移量
	$multipage = multi($amount, $pagesize, $page, $jumpurl.$jumpext, $pagecount);// 显示分页
		// 查询记录集
	$list = DB::fetch_all(sprintf("SELECT a.id as gid,a.name,a.addtime,a.endtime,a.status,b.* FROM %s as a LEFT JOIN %s as b ON a.id=b.activityid WHERE b.uid=%d AND a.is_show =1 limit %s,%s ",DB::table('activity'),DB::table('activity_apply'),$_G['uid'],$startlimit,$pagesize));
	break;
}

include_once template(TEMPDIR);
?>