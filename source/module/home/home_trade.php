<?php
if(!defined('IN_DISCUZ'))exit('Access Denied');

// $allow_do = array('up_post','log','del','up','cols','list');
$do = getgpc('do');
$do = $do ? trim($do) : 'list';

$mainurl = $jumpurl = 'home.php?mod=trade';
$jumpurl .= '&do='.$do;
$jumpext = '';

$upload_common_path = dirname(LO_URL) . LO_UPLOAD .'trade/';// realpath(LO_URL) 会除去 / ./ ../ 根路径
$upload_common_path_op = realpath(DISCUZ_ROOT) . LO_UPLOAD .'trade/';// 物理路径
define('IMG_TPL', '/uploads/trade/pic/');
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
	$SEO['title'] = '收藏的商品';

	$wh.=" where uid='$_G[uid]' and type='col' ";
	$wh1.=" where b.uid='$_G[uid]' and b.type='col' ";
	$pagesize = 6;// 每页记录数
	
	$query = DB::query("SELECT COUNT(*) FROM ".DB::table('trade_log').$wh);
	$amount = DB::result($query, 0);// 查询记录总数
	$pagecount = $amount?(($amount<$pagesize)?1:(($amount%$pagesize)?((int)($amount/$pagesize)+1):($amount/$pagesize))):0;// 计算总页数
	$page = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
	$page = $page > $pagecount ? 1 : $page;// 取得当前页值
	$startlimit = ($page - 1) * $pagesize;// 查询起始的偏移量
	$multipage = multi($amount, $pagesize, $page, $jumpurl, $pagecount);// 显示分页
		// 查询记录集
	// $list = DB::fetch_all("SELECT * FROM ".DB::table('trade').$wh."  order by id desc  LIMIT {$startlimit},{$pagesize}");
	$list =  DB::fetch_all(sprintf("SELECT a.pic,a.id,b.id as gid,a.name,a.brief,a.shop_price FROM %s as a LEFT JOIN %s as b ON a.id=b.tradeid %s order by b.id desc LIMIT %s,%s",DB::table('trade'),DB::table('trade_log'),$wh1,$startlimit,$pagesize));

	break;

case 'list_post':
	$table="trade";

	if($_POST['name']==''){
		showmessage('商品名不能为空',$mainurl);
	}elseif($_POST['store_count']==''){
		showmessage('商品数量不能为空',$mainurl);
	}elseif($_POST['shop_price']==''){
		showmessage('商品价格不能为空',$mainurl);
	}elseif($_POST['brief']==''){
		showmessage('商品简介不能为空',$mainurl);
	}elseif($_FILES['pic']['name']==''){
		showmessage('原始图片不能为空',$mainurl);
	}

	if ($_FILES['pic']['name']) {
		$pic = plugin_common::upload_file('pic',$upload_common_path_op . LO_PIC);		
	}
	$_POST['pic']=$pic;
	$_POST['modtime']=time();
	$_POST['uid']=$_G['uid'];
	$_POST['status']=1;

	$insert_id = DB::insert('trade',$_POST,true);	
	if ($insert_id) {
		showmessage('添加成功',$mainurl);
	}else{
		showmessage('添加失败',$mainurl);
	}
	break;

case 'log':
	$SEO['title'] = '交易记录';

	$wh="SELECT count(*) FROM ".DB::table('trade')." as a LEFT JOIN ".DB::table('trade_log')." as b ON a.id=b.tradeid WHERE b.uid=".$_G['uid']." AND b.type ='buy' AND b.status=1 ";

	$pagesize = 6;// 每页记录数
	$query = DB::query($wh);
	$amount = DB::result($query, 0);// 查询记录总数
	$pagecount = $amount?(($amount<$pagesize)?1:(($amount%$pagesize)?((int)($amount/$pagesize)+1):($amount/$pagesize))):0;// 计算总页数
	$page = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
	$page = $page > $pagecount ? 1 : $page;// 取得当前页值
	$startlimit = ($page - 1) * $pagesize;// 查询起始的偏移量
	$multipage = multi($amount, $pagesize, $page, $jumpurl.$jumpext, $pagecount);// 显示分页
		// 查询记录集
		
	// $list = DB::fetch_all(sprintf("SELECT a.name,b.addtime,a.*,b.* FROM %s as a LEFT JOIN %s as b ON a.id=b.tradeid WHERE b.uid=%d AND b.type ='%s' AND b.status=1 ",DB::table('trade'),DB::table('trade_log'),$_G['uid'],'buy'));
	
	$list = DB::fetch_all(sprintf("SELECT a.name,b.addtime,c.username,b.id,a.id as gid FROM %s as a LEFT JOIN %s as b ON a.id=b.tradeid  LEFT join %s as c on a.uid=c.uid  WHERE b.uid=%d AND b.type ='%s' AND b.status=1 limit %s,%s",DB::table('trade'),DB::table('trade_log'),DB::table('common_member'),$_G['uid'],'buy',$startlimit,$pagesize));

	$list1 = DB::fetch_all(sprintf("SELECT a.name,b.addtime,c.username,b.id,a.id as gid FROM %s as a LEFT JOIN %s as b ON a.id=b.tradeid  LEFT join %s as c on a.uid=c.uid  WHERE a.uid=%d AND b.type ='%s' AND b.status=1 limit %s,%s",DB::table('trade'),DB::table('trade_log'),DB::table('common_member'),$_G['uid'],'buy',$startlimit,$pagesize));

	// $selid = DB::fetch_all(sprintf("SELECT id FROM %s WHERE uid = %s  ",DB::table('trade'),$_G['uid']));
	// $ids="(";
	// foreach ($selid as $key => $value) {
	// 	# code...
	// 	$ids.=$value['id'].",";
	// }
	// $ids = substr($ids,0,-1).')';
	// $list1 = DB::fetch_all(sprintf("SELECT a.name,b.addtime,c.username,b.id FROM %s as a LEFT JOIN %s as b ON a.id=b.tradeid  LEFT join %s as c on a.uid=c.uid  WHERE b.tradeid in %s AND b.type ='%s' AND b.status=1 ",DB::table('trade'),DB::table('trade_log'),DB::table('common_member'),$ids,'buy'));

	break;

default:
	$SEO['title'] = '出售闲置品';

	$wh=" SELECT count(*) FROM ".DB::table('trade')." WHERE uid=".$_G['uid']."  AND recycle=''  ";

	$pagesize = 3;// 每页记录数
	$query = DB::query($wh);
	$amount = DB::result($query, 0);// 查询记录总数
	$pagecount = $amount?(($amount<$pagesize)?1:(($amount%$pagesize)?((int)($amount/$pagesize)+1):($amount/$pagesize))):0;// 计算总页数
	$page = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
	$page = $page > $pagecount ? 1 : $page;// 取得当前页值
	$startlimit = ($page - 1) * $pagesize;// 查询起始的偏移量
	$multipage = multi($amount, $pagesize, $page, $jumpurl.$jumpext, $pagecount);// 显示分页
		// 查询记录集
		
	// $list = DB::fetch_all(sprintf("SELECT * FROM %s  WHERE uid=%d  limit %s,%s ",DB::table('trade'),$_G['uid'],$startlimit,$pagesize));
	$list =  DB::fetch_all(sprintf("SELECT a.id,a.pic,a.brief,a.name,a.store_count,a.shop_price,b.name as area FROM %s as a LEFT JOIN %s as b ON a.trade_type2=b.id WHERE a.uid=%s AND a.recycle='' order by a.id desc LIMIT %s,%s",DB::table('trade'),DB::table('common_district'),$_G['uid'],$startlimit,$pagesize));

	$district = DB::fetch_all("SELECT `id`,`name` from ".DB::table('common_district')." WHERE level=1");//地区
	$trade_type1 =  DB::fetch_all("SELECT `id`,`name` from ".DB::table('trade_type')." WHERE type=1");//交易类型
	$trade_type3 =  DB::fetch_all("SELECT `id`,`name` from ".DB::table('trade_type')." WHERE type=3");//交易状态

	$cates = plugin_common::get_category('trade_category','cid,pid,name');//商品分类

	$brands = $trade_type3 =  DB::fetch_all("SELECT `id`,`name` from ".DB::table('trade_brand')."");//品牌

	// TAB【我要发布】 表单验证 前端
	$Validform['name'] = 'datatype="s3-16" errormsg="名称至少3个字符,最多16个字符！"';
	$Validform['store_count'] = 'datatype="f2" errormsg="允许两位小数"';
	$Validform['shop_price'] = 'datatype="f2" errormsg="允许两位小数"';
	// $Validform['cat_id'] = 'datatype="select" errormsg="请选择分类！"';
	// $Validform['brand_id'] = 'datatype="radio" errormsg="请选择品牌！"';
	$Validform['brief'] = 'datatype="*4-200" tip="请在这里输入您的简述。" altercss="gray" class="gray"';
	$Validform['details'] = 'datatype="*2-2000"';
	$Validform['pic'] = '';

	break;
}

include_once template(TEMPDIR);
?>