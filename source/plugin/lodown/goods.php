<?php
	
	
	$id = $_REQUEST['zid'];


	$goods = DB::fetch_all("select * from ".DB::table('down')." where id=".$id); 
	foreach($goods as $k=>$v){
		$path = DB::fetch_all("select * from ".DB::table('down_category')." where cid=".$v['cid']);
		$author = DB::fetch_all("select * from ".DB::table('common_member')." where uid=".$v['author_id']);
		$log = DB::fetch_all("select * from ".DB::table('down_log')." where type = 'cmt' and tid=".$id);
		$share = DB::fetch_all("select * from ".DB::table('down_log as l,pre_down as d')." where l.tid=d.id and l.type = 'share' and  l.uid=".$v['author_id']);
		$down_user = DB::fetch_all("select * from ".DB::table('down_log as l,pre_common_member as m')." where l.uid=m.uid and l.type = 'down' and l.tid=".$id);
		$file_info = $v;
	}
	
	foreach($path as $k=>$v){
		$path_info = $v;
	}

	foreach($author as $k=>$v){
		$author_info = $v;
	}
	
	foreach($log as $k=>$v){
		$comments = DB::fetch_all("select * from ".DB::table('down_comment as c,pre_common_member as m')." where c.cid=m.uid and c.uid=".$v['tid']);
	}

	foreach($comments as $k=>$v){
		$comments_info[] = $v;
	}
	$nums = count($comments_info);

	foreach($share as $k=>$v){
		$share_infoes[] = $v;
	}
	foreach($down_user as $k=>$v){
		$users[] = $v; 
	}
	// print_r("<pre/>");
	// print_r($down_user);

	
	//获取当前url
	$collection = $_SERVER[REQUEST_URI];

	$date['type'] ='col';
	$date['uid'] = $info['uid'];
	$date['tid'] = $id;
	
	if($_REQUEST['collection']){
		$ins = DB::insert('down_log',$date);
	}
	
	include template(THISPLUG.':index');

	
?>