<?php
if(!defined('IN_DISCUZ'))exit('Access Denied');

// $allow_do = array('up_post','log','del','up','cols','list');
$do = getgpc('do');
$do = $do ? trim($do) : 'list';

$mainurl = $jumpurl = 'home.php?mod=datasheet';
$jumpurl .= '&do='.$do;
$jumpext = '';

$upload_common_path = dirname(LO_URL) . LO_UPLOAD .'datasheet/';// realpath(LO_URL) 会除去 / ./ ../ 根路径
$upload_common_path_op = realpath(DISCUZ_ROOT) . LO_UPLOAD .'datasheet/';// 物理路径
define('IMG_TPL', '/uploads/datasheet/pic/');
// require_once libfile('space/'.$do, 'include');


switch ($do) {
case 'del':
	# code...		
	// $str=DB::update('activity_log', array('is_show'=>'0'), array('id'=>$id));
	$del=DB::delete($_GET['tab']," id = '$_GET[id]' and uid='$_G[uid]' ");
	if($del){
		showmessage('删除成功',$mainurl.'&do='.$_GET['url']);
	}else{
		showmessage('删除失败',$mainurl.'&do='.$_GET['url']);
	}
	break;

case 'cols':
	$SEO['title'] = '我的收藏';
	$wh.=" where uid='$_G[uid]' and type='col' ";
	$wh1.=" where b.uid='$_G[uid]' and b.type='col' ";

	$pagesize = 6;// 每页记录数
	$query = DB::query("SELECT COUNT(*) FROM ".DB::table('datasheet_log').$wh);
	$amount = DB::result($query, 0);// 查询记录总数
	$pagecount = $amount?(($amount<$pagesize)?1:(($amount%$pagesize)?((int)($amount/$pagesize)+1):($amount/$pagesize))):0;// 计算总页数
	$page = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
	$page = $page > $pagecount ? 1 : $page;// 取得当前页值
	$startlimit = ($page - 1) * $pagesize;// 查询起始的偏移量
	$multipage = multi($amount, $pagesize, $page, $jumpurl.$jumpext, $pagecount);// 显示分页
	// 查询记录集
	// $list = DB::fetch_all("SELECT * FROM ".DB::table('datasheet').$wh."  order by id desc  LIMIT {$startlimit},{$pagesize}");
	$list =  DB::fetch_all(sprintf("SELECT a.pic,a.id,b.id as gid,a.name,a.brief FROM %s as a LEFT JOIN %s as b ON a.id=b.tid %s order by b.id desc LIMIT %s,%s",DB::table('datasheet'),DB::table('datasheet_log'),$wh1,$startlimit,$pagesize));
	break;

case 'log':
	$SEO['title'] = '下载记录';
	$wh="SELECT count(*) FROM ".DB::table('datasheet')." as a LEFT JOIN ".DB::table('datasheet_log')." as b ON a.id=b.tid WHERE b.uid=".$_G['uid']." AND b.type ='down' AND b.recycle='' ";

	$pagesize = 6;// 每页记录数
	$query = DB::query($wh);
	$amount = DB::result($query, 0);// 查询记录总数
	$pagecount = $amount?(($amount<$pagesize)?1:(($amount%$pagesize)?((int)($amount/$pagesize)+1):($amount/$pagesize))):0;// 计算总页数
	$page = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
	$page = $page > $pagecount ? 1 : $page;// 取得当前页值
	$startlimit = ($page - 1) * $pagesize;// 查询起始的偏移量
	$multipage = multi($amount, $pagesize, $page, $jumpurl.$jumpext, $pagecount);// 显示分页
		// 查询记录集
	$list = DB::fetch_all(sprintf("SELECT b.cos,a.name,b.addtime,c.username,b.id,a.id as gid FROM %s as a LEFT JOIN %s as b ON a.id=b.tid  LEFT join %s as c on a.author_id=c.uid  WHERE b.uid=%d AND b.type ='%s' AND b.recycle='' limit %s,%s ",DB::table('datasheet'),DB::table('datasheet_log'),DB::table('common_member'),$_G['uid'],'down',$startlimit,$pagesize));
	break;

default:
	$SEO['title'] = '我的下载';
	$wh=" SELECT count(*) FROM ".DB::table('datasheet')." WHERE author_id=".$_G['uid']." AND recycle='' AND (status=2 or status =6) AND is_show=1 ";

	$pagesize = 16;// 每页记录数
	$query = DB::query($wh);
	$amount = DB::result($query, 0);// 查询记录总数
	$pagecount = $amount?(($amount<$pagesize)?1:(($amount%$pagesize)?((int)($amount/$pagesize)+1):($amount/$pagesize))):0;// 计算总页数
	$page = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
	$page = $page > $pagecount ? 1 : $page;// 取得当前页值
	$startlimit = ($page - 1) * $pagesize;// 查询起始的偏移量
	$multipage = multi($amount, $pagesize, $page, $jumpurl.$jumpext, $pagecount);// 显示分页
	// 查询记录集
	$list = DB::fetch_all(sprintf("SELECT id,name FROM %s  WHERE author_id=%d AND recycle=''  AND (status=2 or status =6) AND is_show=1 limit %s,%s ",DB::table('datasheet'),$_G['uid'],$startlimit,$pagesize));
	// $list =  DB::fetch_all(sprintf("SELECT a.id,a.pic,a.brief,a.name,a.store_count,a.shop_price,b.name as area FROM %s as a LEFT JOIN %s as b ON a.trade_type2=b.id WHERE a.uid=%s AND a.recycle='' order by a.id desc LIMIT %s,%s",DB::table('datasheet'),DB::table('common_district'),$_G['uid'],$startlimit,$pagesize));
	break;
}

include_once template(TEMPDIR);
?>