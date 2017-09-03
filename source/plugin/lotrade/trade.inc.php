<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
require_once dirname(__FILE__).'/common.php';

if (in_array($_GET['pluginop'],array('message_op','order_op','order','del','op'))) {
	if (empty($gUid)) showmessage('请登录！',LO_CURURL);
}

/*
 * 分支
*/
switch ($_GET['pluginop']) {
case 'ajax':
	include_once 'ajaxjson.php';
	break;

case 'message_op':
	// 留言
	$_POST['uid']=$_G['uid'];
	$_POST['addtime']=time();
	$insert_id = DB::insert('trade_message',$_POST,true);
	if ($insert_id) {
		showmessage('添加成功', LO_CURURL."&pluginop=page&loid=".$_POST['trade_id']);
	} else {
		showmessage('添加失败');
	}
	break;

case 'order_op':
	# 订单提交
	$leftmenu = 'exchange';
	$formcheck['url'] = LO_CURURL;
	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$oppo = looppo($_REQUEST['sign'], $_REQUEST['table']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	$fields = $oppo['fields'];
	$head_title = $oppo['head_title'];
	$SEO['title'] = '支付金钱';
	$SEO = lo_seo($SEO);

	$arrorderid = $_POST['orderid'];
	// $arrorderid = implode(',',$_POST['orderid']);

	$data = array();
	$data['type']="buy";
	$data['uid']=$_G['uid'];
	$data['tid']=$_G['group']['groupid'];
	$data['ip']=CURIP;
	$data['addtime']=CURTIME;
	$data['brief']=$_POST['remark'];
	$data['tradeid']=$_POST['loid'];
	$data['number']=$_POST['number'];
	if(empty($_POST['address'])){
		showmessage('地址不能为空', LO_CURURL ."&pluginop=page&loid=".$data['tradeid']);
	}
	$data['addressid']=$_POST['address'];
	$data['orderid'] = date('Ymdhis');
	$insert_id = DB::insert('trade_log',$data,true);
	try {
		$counts =DB::fetch_first(sprintf("SELECT store_count,sales_sum FROM %s WHERE id='%s' ",DB::table('trade'),$data['tradeid']));
		$count=$counts['store_count'];
		$sales_sum=$counts['sales_sum']+$_POST['number'];
		if($count<$data['number']){
			$error = '库存数量不足！';
            throw new Exception($error);
		}elseif($_G['member']['extcredits2']<$_POST['paycount']){
            showmessage('金额不足！请及时充值','home.php?mod=spacecp&ac=credit&op=buy');
		}else{
			$curcredits = bcsub($curcredits,$_POST['paycount']);
			$count=$count-$data['number'];
			$rescount = DB::update('trade', array('store_count'=>$count,'sales_sum'=>$sales_sum), array('id'=>$data['tradeid']));
			if (!$rescount) {
	        	$error = '商品数量变动失败！';
                throw new Exception($error);
            }
	        $resnum = DB::update('common_member_count', array('extcredits2'=>$curcredits), array('uid'=>$_G['uid']));
	        if (!$resnum) {
	        	$error = '金钱变动失败！';
                throw new Exception($error);
            }
            // DB::update('trade_log', array('addressid'=>$_POST['address'],'cos'=>$_POST['paycount'],'brief'=>$_POST['remark'],'status'=>1), ' orderid IN ()');
            foreach ($arrorderid as $key => $value) {
            	DB::update('trade_log', array('addressid'=>$_POST['address'],'cos'=>$_POST['paycount'],'brief'=>$_POST['remark'],'status'=>1), array('orderid'=>$data['orderid']));
            }
		}                    
    } catch (Exception $e) {
    	showmessage($e->getMessage(),$formcheck['url'].'&pluginop=page&loid='.$data['tradeid']);
    }

	showmessage('添加成功', LO_CURURL ."&pluginop=page&loid=".$data['tradeid']);
	// include template(THISPLUG.':order_op');
	break;

case 'order':
	$leftmenu = 'order';
	$formcheck['url'] = LO_CURURL."&pluginop=order";
	$url = LO_CURURL."&pluginop=order_op";
	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$oppo = looppo($_REQUEST['sign'], $_REQUEST['table']);
	$table = 'trade';
	$lokey = $oppo['lokey'];
	$fields = $oppo['fields'];
	$head_title = $oppo['head_title'];
	$SEO['title'] = '二手交易';
	$SEO = lo_seo($SEO);

	$list =  DB::fetch_first(sprintf("SELECT a.*,b.name as area FROM %s as a LEFT JOIN %s as b ON a.trade_type2=b.id WHERE a.id='%s' ",DB::table($table),DB::table('common_district'),$loid));

	// $wh=" type = 'exchange' and uid = '$_G[uid]' ";
	// $arrorder =  DB::fetch_all(sprintf("SELECT a.id,b.gid,b.pic,b.name,b.price FROM %s as a LEFT JOIN %s as b ON a.gid=b.gid WHERE a.type='%s' AND a.uid=%d AND a.is_show =1 ",DB::table('activity_log'),DB::table('activity_gift'),'exchange',$_G['uid']));
	// $arrcount=count($arrorder);

	include template(THISPLUG.':order');
	break;

case 'page':
	$leftmenu = 'page';
	$formcheck['url'] = LO_CURURL.'&pluginop=op';
	$url=LO_CURURL;
	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$oppo = looppo('', $_REQUEST['table']);
	$table = $oppo['table'];
	$lokey = $oppo['lokey'];
	$fields = $oppo['fields'];
	$head_title = $oppo['head_title'];
	$SEO['title'] = '商品详情';
	$SEO = lo_seo($SEO);
	$recommend = DB::fetch_all("select ". $fields ." from ".DB::table($table)." where (status=2||status=6) and is_on_sale=1 and is_recommend=1 order by id desc LIMIT 0,3");//推荐

	$list =  DB::fetch_first(sprintf("SELECT a.*,b.name as area FROM %s as a LEFT JOIN %s as b ON a.trade_type2=b.id WHERE a.id='%s' ",DB::table($table),DB::table('common_district'),$loid));
	$cates = plugin_common::get_category($tpre.'_category','cid,pid,name');//商品分类

	DB::update($table, array('click_count'=>$list['click_count']+1), array('id'=>$loid));//浏览次数增加
	
	$pagesize = 6;// 每页记录数
	$multi = plugin_common::pager("SELECT COUNT(*) FROM ".DB::table('trade_message')." where trade_id=".$loid,$_GET['page'],$page,$url."&pluginop=page&loid=".$loid,$pagesize);
	$multipage = $multi[0];
	// 查询记录集
	$limit = $multi[1];
	$arrmessage = DB::fetch_all(sprintf("SELECT a.*,b.username FROM %s as a LEFT JOIN %s as b ON a.uid=b.uid WHERE a.trade_id=%d order by a.id desc %s",DB::table('trade_message'),DB::table('common_member'),$loid,$limit));
	// $arrmessage = DB::fetch_all(sprintf("SELECT a.*,b.username FROM %s as a LEFT JOIN %s as b ON a.uid=b.uid WHERE a.trade_id='%s' ",DB::table('trade_message'),DB::table('common_member'),$loid));

	$arrBrand = cache_list_table('trade_brand', $fields, " is_hot=0 ", 'id desc ','7');// 相关品牌
	$arrBrandHot = cache_list_table('trade_brand', $fields, " is_hot=1 ", 'id desc ','7');// 相关品牌

	$collect = DB::result_first(sprintf('SELECT uid from %s where uid=%d and tradeid=%d',DB::table('trade_log'),$gUid,$loid));

	include template(THISPLUG.':detail');
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
	$is_del = trim($_GET['is_del']) ? '' : 'u';
	$skip = array(
			'msgok' => '已删除您所选的数据！',
			'urlok' => '',
			'msgno' => '删除失败！',
			'urlno' => '',
			'ext' => $jumpext
		);
	plugin_common::common_taskdel($table, $wh, $is_del, $skip, $del_filekey);
	break;

case 'op':
	plugin_common::common_op($_POST);
	break;

default:
	// 初始化变量
	$leftmenu = 'list';
	$oppo = looppo($_REQUEST['sign'], $_REQUEST['table']);
	$formcheck['url'] = LO_CURURL;
	$table = $oppo['table'];
	$fields = $oppo['fields'];
	$SEO['title'] = '二手交易';
	$SEO = lo_seo($SEO);
	// echo $formcheck['url'];
	$type1 = $_GET['type1']?intval($_GET['type1']):0;
	$type2 = $_GET['type2']?intval($_GET['type2']):0;
	$type3 = $_GET['type3']?intval($_GET['type3']):0;
	$type4 = $_GET['type4']?intval($_GET['type4']):0;
	$wh=' where (status=2||status=6) and is_on_sale=1 ';
	if ($type1!=0){
		$formcheck['url'].="&type1=".$type1;
		$wh.=" and trade_type1 =".$type1;
	}
	if ($type2!=0) {
		$formcheck['url'].="&type2=".$type2;
		$wh.=" and trade_type2 =".$type2;
	}
	if ($type3!=0) {
		$formcheck['url'].="&type3=".$type3;
		$wh.=" and trade_type3 =".$type3;
	}
	if ($type4!=0) {
		$formcheck['url'].="&type4=".$type4;
		$wh.=" and  cat_id =".$type4;
	}
	$wh1=$wh;
	$keyword="";
	if($_POST){
		$wh="";
		$wh1="";
		$keyword=$_POST['search'];
		$wh.=" where (status=2||status=6) and is_on_sale=1 and (name like '%$_POST[search]%' or brief like '%$_POST[search]%') ";
		$wh1.=" where (a.status=2||a.status=6) and a.is_on_sale=1 and (a.name like '%$_POST[search]%' or a.brief like '%$_POST[search]%') ";
	}

	$lokey = $oppo['lokey'];
	// $fields = $oppo['fields'];
	$head_title = $oppo['head_title'];
	$jumpext = $oppo['jumpext'];

	$pagesize = 6;// 每页记录数
	$query = DB::query("SELECT COUNT(*) FROM ".DB::table('trade').$wh);
	$amount = DB::result($query, 0);// 查询记录总数
	$pagecount = $amount?(($amount<$pagesize)?1:(($amount%$pagesize)?((int)($amount/$pagesize)+1):($amount/$pagesize))):0;// 计算总页数
	$page = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
	$page = $page > $pagecount ? 1 : $page;// 取得当前页值
	$startlimit = ($page - 1) * $pagesize;// 查询起始的偏移量
	$multipage = multi($amount, $pagesize, $page, $formcheck['url'].$url, $pagecount);// 显示分页
		// 查询记录集
	// $list = DB::fetch_all("SELECT * FROM ".DB::table('trade').$wh."  order by id desc  LIMIT {$startlimit},{$pagesize}");
	$list =  DB::fetch_all(sprintf("SELECT a.*,b.name as area FROM %s as a LEFT JOIN %s as b ON a.trade_type2=b.id %s order by id desc LIMIT %s,%s",DB::table('trade'),DB::table('common_district'),$wh1,$startlimit,$pagesize));

	$cates = plugin_common::get_category($tpre.'_category','cid,pid,name');// 商品分类
	$recommend = DB::fetch_all("select ". $fields ." from ".DB::table($table)." where (status=2||status=6) and is_on_sale=1 and is_recommend=1 order by id desc LIMIT 0,3");//推荐
	$district = DB::fetch_all("SELECT `id`,`name` from ".DB::table('common_district')." WHERE level=1");//地区
	$trade_type1 = DB::fetch_all("SELECT `id`,`name` from ".DB::table('trade_type')." WHERE type=1");//交易类型
	$trade_type3 = DB::fetch_all("SELECT `id`,`name` from ".DB::table('trade_type')." WHERE type=3");//交易状态
	// 当前交易类型
	$trade_type4 = DB::fetch_first(sprintf('SELECT name,pid from %s where cid=%d',DB::table('trade_category'),$type4));
	if ($trade_type4['pid']) {
		$trade_type4_title_pid = DB::result_first(sprintf('SELECT name from %s where cid=%d',DB::table('trade_category'),$trade_type4['pid']));
	}
	$trade_type4_title = $trade_type4_title_pid ? $trade_type4_title_pid.'-'.$trade_type4['name'] : ($trade_type4?$trade_type4:'所有交易');
	// 最新发布
	$wh=' where (a.status=2||a.status=6) and a.is_on_sale=1 ';
	// $arrTuijian = cache_list_table($table, $fields, " is_on_sale=1 and (status=2||status=6) ", 'modtime desc ','7');
	$arrTuijian=DB::fetch_all(sprintf("SELECT a.name,a.id,b.username FROM %s as a LEFT JOIN %s as b ON a.uid=b.uid %s order by a.id desc LIMIT %s,%s",DB::table($table),DB::table('common_member'),$wh,'0','7'));
	// 最新购买信息
	// $arrHot = cache_list_table($table, $fields, " is_on_sale=1 and (status=2||status=6) and is_hot =1 ", 'id desc ','7');
	// $wh=" where a.type='buy' and (a.status=2||a.status=1) ";
	// $arrHot=DB::fetch_all(sprintf("SELECT a.*,b.* FROM %s as a LEFT JOIN %s as b ON b.id=a.tradeid %s order by a.id desc LIMIT %s,%s",DB::table("trade_log"),DB::table($table),$wh,'0','7'));
	// SELECT a.brief,b.username,c.name,c.1id as t FROM trade_log as a LEFT JOIN `common_member` as b ON a.uid=b.uid LEFT JOIN `trade` as c ON a.tradeid=c.id LIMIT 0,7
	// $arrHot=DB::fetch_all(sprintf("SELECT a.brief,b.username,c.name,c.id as t FROM %s as a LEFT JOIN `%s` as b ON a.uid=b.uid LEFT JOIN `%s` as c ON a.tradeid=c.id LIMIT 0,7",DB::table('trade_log'),DB::table('common_member'),DB::table('trade')));
	$arrHot=DB::fetch_all(sprintf("SELECT a.brief,b.username,c.name,c.id FROM %s as a LEFT JOIN `%s` as b ON a.uid=b.uid LEFT JOIN `%s` as c ON a.tradeid=c.id where a.type='buy' and (a.status=2||a.status=1) LIMIT 0,7",DB::table('trade_log'),DB::table('common_member'),DB::table('trade')));

	// $arrid = array();
	// foreach ($arrids as $key => $value) {
	// 	# code...
	// 	$arrid[]=$value['id'];
	// }
	// $join = array(
	// 		array('b','username','common_member','uid','=','uid'),
	// 		array('c','name,1id as t','trade','tradeid','=','id')
	// 	);
	// $cmt = plugin_common::common_list('trade_log', 7, 1, $where, $order, '', 'brief', $join);
	
	include template(THISPLUG.':index_list');
	break;
}
?>