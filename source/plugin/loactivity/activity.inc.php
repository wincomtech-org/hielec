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
	// switch ($_GET['sign']) {
	// 	case 'gift':
	// 		echo "giftaiax";
	// 		break;
	// 	case 'col':
	// 		echo "colajax";
	// 		break;
	// }

	include 'ajaxjson.php';
	break;
case 'join':
	$leftmenu = '';
	$formcheck['url'] = LO_CURURL.'&pluginop=join_op';

	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$url=LO_CURURL.'&pluginop=page&loid='.$loid;
	$oppo = looppo($_REQUEST['sign'], $_REQUEST['table']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	$fields = $oppo['fields'];
	$head_title = $oppo['head_title'];
	$SEO['title'] = '活动申请';
	$SEO = lo_seo($SEO);

	$pag = DB::fetch_first("select id from ".DB::table('activity_apply')." where activityid=".$loid." and uid=".$_G['uid']);

	if($pag[id]){
		showmessage('您已申请该活动',$url);
	}else{
		$list = DB::fetch_first("select ". $fields ." from ".DB::table($table)." where id=".$loid);
	}


	include template(THISPLUG.':join_op');
	break;

case 'join_op':
		# code...
		$url=LO_CURURL.'&pluginop=page&loid='.$_POST['activityid'];
		$_POST['uid']=$_G['uid'];
		$insert_id = DB::insert('activity_apply',$_POST,true);
		if ($insert_id) {
			showmessage('申请成功',$url);
		}
		die();
		break;

case 'order':
	# code...
	$leftmenu = 'exchange';
	$formcheck['url'] = LO_CURURL."&pluginop=order";
	$url=LO_CURURL."&pluginop=order_op";
	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$type = $_GET['type']?intval($_GET['type']):0;
	$oppo = looppo($_REQUEST['sign'], $_REQUEST['table']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	$fields = $oppo['fields'];
	$head_title = $oppo['head_title'];
	$SEO['title'] = '置换礼物';
	$SEO = lo_seo($SEO);
	// print_r($type);
	// die();
	if($type=='1'){
		$page = DB::fetch_first("select ". $fields ." from ".DB::table('activity_log')." where type ='exchange' and gid=".$loid." and uid=".$_G['uid']);
		if($page['id']){
			// echo "<script>alert('您已添加该礼品');</script>";
			$str=DB::update('activity_log', array('is_show'=>'1','number'=>'1'), array('id'=>$page['id']));
		}else{
			$data['type']='exchange';
			$data['brief']='置换礼品';
			$data['uid']=$_G['uid'];
			$data['gid']=$loid;
			$data['tid']=$_G['group'][groupid];
			$data['ip']=CURIP;
			$data['addtime']=time();
			$data['exp']=$page['price'];
			$data['is_show']=1;
			$insert_id = DB::insert('activity_log',$data,true);
			echo "<script>alert('添加成功');</script>";
		}
	}elseif($type=='2'){
		$formcheck['url'] = LO_CURURL."&pluginop=order&del=del";
		$did = $_GET['did']?intval($_GET['did']):0;
		$str=DB::update('activity_log', array('is_show'=>'0'), array('id'=>$did));
		if($str){
			echo "<script>alert('删除成功');</script>";
		}else{
			// echo "<script>alert('删除失败');</script>";
		}
	}

	// $wh=" type = 'exchange' and uid = '$_G[uid]' ";
	$arrorder =  DB::fetch_all(sprintf("SELECT a.id,a.number,b.addtime,b.subhead,b.gid,b.pic,b.name,b.price FROM %s as a LEFT JOIN %s as b ON a.gid=b.gid WHERE a.type='%s' AND a.uid=%d AND a.is_show =1 and a.status=0 ",DB::table('activity_log'),DB::table('activity_gift'),'exchange',$_G['uid']));
	$arrcount=count($arrorder);
	if($arrcount==0){
		showmessage('没有相关信息',LO_CURURL);
	}

	echo "<pre>";
	// print_r(count($arrorder));
	echo "</pre>";
	include template(THISPLUG.':order');
	break;
case 'dellog':
	# code...
	break;
case 'order_op':
	$leftmenu = 'exchange';
	$formcheck['url'] = LO_CURURL."&pluginop=order";
	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$oppo = looppo($_REQUEST['sign'], $_REQUEST['table']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	$fields = $oppo['fields'];
	$head_title = $oppo['head_title'];
	$SEO['title'] = '支付积分';
	$SEO = lo_seo($SEO);
	$arrorderid=$_POST['orderid'];

	try {
		if($_G['member']['credits']<$_POST['coountsum']){
			$error = '积分不足！';
            throw new Exception($error);
		}else{
			$extcredits4 = $_G['member']['extcredits4'];
			$extcredits4 = bcsub($extcredits4,bcdiv($_POST['coountsum'], $loper));
	        $resnum = DB::update('common_member_count', array('extcredits4'=>$_POST['coountsum']), array('uid'=>$_G['uid']));
	        if (!$resnum) {
	        	$error = '积分变动失败！';
                throw new Exception($error);
            }
            foreach ($arrorderid as $key => $value) {
            	DB::update('activity_log', array('addressid'=>$_POST[address],'credits'=>$extcredits4,'brief'=>$_POST['remark'],'status'=>1), array('id'=>$value));
            }

		}
    } catch (Exception $e) {
    	showmessage($e->getMessage(),$formcheck['url']);
    }
    showmessage('兑换礼物成功',LO_CURURL);
	// showmessage('111',$formcheck['url']);
	echo "<pre>";
	// print_r($_G['member']['credits']);
	echo "</pre>";

	break;
case 'exchange':
	# code...
	$leftmenu = 'exchange';
	$formcheck['url'] = LO_CURURL."&pluginop=order";
	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$oppo = looppo($_REQUEST['sign'], $_REQUEST['table']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	$fields = $oppo['fields'];
	$head_title = $oppo['head_title'];
	$SEO['title'] = '置换礼物';
	$SEO = lo_seo($SEO);
	$page = DB::fetch_first("select ". $fields ." from ".DB::table('activity_log')." where gid=".$loid." and uid=".$_G['uid']);
	// print_r($page);
	if($page['id']){
		showmessage('您已添加该礼品',$formcheck['url']);
	}

	// $page = DB::fetch_first("select ". $fields ." from ".DB::table('activity_gift')." where gid=".$loid);
	// $page['price'] = $page['price'] ? $page['price']: 0;

	// if($_G['member']['credits']<$page['price']){
	// 	showmessage('积分不足',$formcheck['url']);
	// }
	$data['type']='exchange';
	$data['brief']='置换礼品';
	$data['uid']=$_G['uid'];
	$data['gid']=$loid;
	$data['tid']=$_G['group'][groupid];
	$data['ip']=CURIP;
	$data['addtime']=time();
	$data['exp']=$page['price'];
	$insert_id = DB::insert('activity_log',$data,true);
	showmessage('添加成功',$formcheck['url']);
	// if (!$insert_id) {
	// 	throw new Exception('添加失败');
	// }else{
	// 	$page['price']=$_G['member']['credits']-$page['price'];
	// 	DB::update('common_member', array('credits'=>$page['price']), array('uid'=>$_G['uid']));
		// showmessage('置换成功',$formcheck['url']);
	// }
	break;


case 'gift':
	// 初始化变量
	$leftmenu = 'list';
	$oppo = looppo($_GET['pluginop']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	// $fields = $oppo['fields'];
	$head_title = $oppo['head_title'];
	$jumpext = $oppo['jumpext'];
// var_dump($oppo);die;
	// 预设table表格
	$tab['th'] = array('名称','简述','价格','兑换次数','相关链接','logo','点赞','反对','发布时间','操作');
	$tab['td'] = array('name','brief','price','buys','url','pic','supports','unsupports','addtime');
	// $ajax = json_decode('{"show":"显隐","col":"收藏","zan":"点赞","cai":"踩","down":"下载"}');
	// foreach ((array)$ajax as $k => $v) {
	// 	$tab['ajax'][] = array(LO_CURURL.'&pluginop=ajax&sign='.$k.'&loid=', $v, $k);
	// }
	$tab['operator'] = array(
			array(LO_CURURL.'&pluginop=ajax'.$jumpext.'&loid=', '兑换', 'edit'),
			// array(LO_CURURL.'&pluginop=del'.$jumpext.'&loid=', '删除', 'del')
		);
	// if ($tab['ajax']) $tab['operator'] = array_merge($tab['ajax'], $tab['operator']);

	// 数据查询处理
	$where = array(
			array('start', $_REQUEST['start'], 'addtime'),
			array('end', $_REQUEST['end'], 'addtime'),
			array('key', $_REQUEST['key'], 'name'),
			array('keyword', $_REQUEST['keyword'], 'name,tags,brief,details'),
			array('uid', $_REQUEST['uid'], 'uid'),
			'recycle=\'\' and is_show=1 and (status=2 or status=6)'
		);
	$order = 'sort asc,addtime desc,'. $lokey .' desc';
	$fields = implode(',', $tab['td']);
	// $fields = 'name,price,brief,url,pic,attach,views,searchs,cols,supports,unsupports,author_id,addtime,modtime,ip,status,is_show,sort';
	$fields = $fields=='*'?$fields:($lokey?$lokey.','.$fields:$fields);
	// $join = array(
	// 		// array('b','name as cname','download_category','cid','=','cid'),
	// 		array('c','username','common_member','author_id','=','uid')
	// 	);
	// $cmt = plugin_common::common_list($table, $pluginvar['pagesize'], $_GET['page'], $where, $order, $jumpext, $fields, $join, 'a');
	$cmt = plugin_common::common_list($table, $pluginvar['pagesize'], $_GET['page'], $where, $order, '', $fields);
	$multipage = $cmt['multipage'];
	$list = $cmt['list'];
	echo "<pre>";
	// print_r($cmt);
	// print_r($cmt2);
	// var_dump($tab);
	echo "</pre>";
	include template(THISPLUG.':index_list');
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
	plugin_common::common_taskdel($table, $wh, 'u', $skip, $del_filekey);
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
	$SEO['title'] = '活动详情';
	$SEO = lo_seo($SEO);

	// 数据查询、处理
	$page = DB::fetch_first("select ". $fields ." from ".DB::table($table)." where ".$lokey."=".$loid);
	$page['price'] = $page['price'] ? $page['price']: 0;
	$page['pichref'] = $page['pic'] ? $upload_common_path . LO_PIC .$page['pic']: '';
	// $page['pdfhref'] = $page['pdf'] ? $upload_common_path . LO_PDF .$page['pdf']: '';
	// $page['attachhref'] = $page['attach'] ? $upload_common_path . LO_ATTACH .$page['attach']: '';
	$page['views']=$page['views']+1;
	DB::update($table, array('views'=>$page['views']), array('id'=>$loid));//浏览次数增加

	$ur_here = ur_here($_REQUEST['sign'],$page['cid'],$page['name']);


	if ($table==$tpre.'_category') {
		$cates = plugin_common::get_category($tpre.'_category','cid,pid,name');
	} else {
		$cates = session_ob($tpre.'_category', 'cid,pid,name', 'order by sort');
	}


	$arrTuijian = cache_list_table($table, $fields, null, 'modtime desc');// 最新活动

	$arrHotDown = cache_list_table($table, $fields, null, 'views desc');// 热门活动

	$arrMore = cache_list_table($table, $fields, null, 'id desc');;// 更多活动


echo "<pre>";

echo "</pre>";
include template(THISPLUG.':details');
// print_r($cates);
	// include template(THISPLUG.':index_op');
	break;

case 'op':
// print_r($_POST);
// die;
	plugin_common::common_op($_POST);
	break;

default:
	$leftmenu = 'list';
	$formcheck['url'] = LO_CURURL;
	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$oppo = looppo($_REQUEST['sign'], $_REQUEST['table']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	$fields = $oppo['fields'];
	$head_title = $oppo['head_title'];
	$SEO['title'] = '列表页';
	$SEO = lo_seo($SEO);

	$tab['th'] = array('标题','简述','活动时间','奖项设置');
	$tab['td'] = array('name','brief','views','prize');


	$wh=' where recycle=\'\' and is_show=1 and (status=2||status=6) ';
	$pagesize = 6;// 每页记录数
	$query = DB::query("SELECT COUNT(*) FROM ".DB::table('activity').$wh);
	$amount = DB::result($query, 0);// 查询记录总数
	$pagecount = $amount?(($amount<$pagesize)?1:(($amount%$pagesize)?((int)($amount/$pagesize)+1):($amount/$pagesize))):0;// 计算总页数
	$page = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
	$page = $page > $pagecount ? 1 : $page;// 取得当前页值
	$startlimit = ($page - 1) * $pagesize;// 查询起始的偏移量
	$multipage = multi($amount, $pagesize, $page, LO_CURURL.$url, $pagecount);// 显示分页
		// 查询记录集

	$list = DB::fetch_all("SELECT * FROM ".DB::table('activity').$wh."  order by id desc  LIMIT {$startlimit},{$pagesize}");


	$listfift = DB::fetch_all("SELECT * FROM ".DB::table('activity_gift').$wh."  order by gid desc LIMIT {$startlimit},{$pagesize}");


	// // 数据查询、处理
	// $list = cache_list_table($table, $fields, null, 'modtime desc','6');// 活动列表
	// $listfift = cache_list_table('activity_gift', $fields, null, 'gid desc','6');// 礼物列表

	include template(THISPLUG.':index_list');
	break;
}
?>