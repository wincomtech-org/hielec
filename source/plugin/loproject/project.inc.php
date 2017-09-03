<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');
require_once dirname(__FILE__).'/common.php';

/*
 * 分支
 * ?m=moudle&c=control&a=action&var=value
 * ?m=pluginop&c=pro&a=list&var=value
 * $mainurl = LO_CURURL
*/
$pluginop = getgpc('pluginop');
$pluginop = $pluginop ? trim($pluginop) : 'pro';
$c = getgpc('c');
$c = $c ? trim($c) : 'list';

$mainurl = 'plugin.php?pl=project';
$userurl = 'home.php?mod=space&uid=';
$jumpurl = LO_CURURL;
$jumpurl .= '&pluginop='.$pluginop;
$jumpext = '';
$oproute = 'index';// 标识

switch ($pluginop) {
case 'talent':
	// 预处理数据
    $ahr['cur'] = 'talent'; $ahr['lm'] = 'talent';
    $ur_here = '
    <div class="nav_find">
        当前位置：首页/找人才
    </div>';
    $srckey = 'srcval';// srckey => srcval
    $srctip = '请输入名字';
    $SEO['title'] = '人才大厅';
    // 用户验证
    if (in_array($c,array('ajax','del','page','op')) && empty($gUid)) plugin_common::jumpgo('您尚未登录！');
    // 预设表
    $table_c = 'common_member';
    $table = DB::table($table_c);
    $lokey = 'uid';
    $fields = 'email,username,user_type,groupid';
    // $fields .= ',groupid,status,emailstatus,avatarstatus';
    $where = '';$order = '';$limit = '';
    $table2 = DB::table('common_member_profile');
    $table3 = DB::table('common_member_verify');
    $table4 = DB::table('project_task');
    $table5 = DB::table('project_category');
    // $table6 = DB::table('common_usergroup');
    // $table7 = DB::table('project_user');
    $fields2 = 'field4,resideprovince,residecity,residedist';
    $fields3 = 'verify2,verify4,verify5,verify6';
    $fields4 = 'tid,task_title,task_status,indus_id';
    $fields5 = 'name';
    // $fields6 = 'grouptitle';
    // $fields7 = 'skilled_id';
    $fields = plugin_common::create_fields_quote($fields,'a',$lokey);
    $fields2 = plugin_common::create_fields_quote($fields2,'b');
    $fields3 = plugin_common::create_fields_quote($fields3,'c');
    $fields4 = plugin_common::create_fields_quote($fields4,'d');
    $fields5 = plugin_common::create_fields_quote($fields5,'e');
    // $fields6 = plugin_common::create_fields_quote($fields6,'f');
	break;

case 'tds'://人才大厅详情
    $ahr['cur'] = 'talent';$ahr['lm'] = 'talent_details';
    $SEO['title'] = '人才大厅详情';
    $ur_here = '
    <div class="nav_find">
        当前位置：首页/找人才/详情
    </div>';
    // 用户验证
    if (in_array($c,array('ajax','col','comeon')) && empty($gUid)) plugin_common::jumpgo('您尚未登录！');
    // 预设表
    $table_c = 'common_member';
    $table = DB::table($table_c);
    $lokey = 'uid';
    $fields = 'email,username,user_type,regdate';
    $where = '';$order = '';$limit = '';
    $table2 = DB::table('common_member_profile');
    $table3 = DB::table('common_member_verify');
    $table4 = DB::table('project_task');
    $table5 = DB::table('project_category');
    // $table6 = DB::table('project_user');
    $table7 = DB::table('project_case');
    $table8 = DB::table('project_msg');
    $table9 = DB::table('project_visit');
    $table10 = DB::table('common_usergroup');
    $fields2 = 'position,field5,graduateschool,company,field4,bio,resideprovince,residecity,residedist,residecommunity';
    $fields3 = 'verify2,verify4,verify5,verify6';
    $fields4 = 'tid,task_title,task_status,indus_id,pub_time,bid_num';
    $fields5 = 'name';
    // $fields6 = 'skilled_id';
    $fields7 = 'case_id,case_uid,obj_id,obj_type,indus_id,case_title,case_desc,case_img,addtime';
    // $where = 'WHERE case_uid='.$gUid;
    $fields8 = 'msg_id,msg_type,msg_status,view_status,tid,uid,uname,touid,touname,title,content,addtime';
    // $where = 'WHERE a.msg_status IN (0,2) AND a.uid='.$gUid;
    $fields9 = 'vid,uid,uname,tid,touid,toname,addtime,ip';
    $fields10 = 'grouptitle';
    $fields = plugin_common::create_fields_quote($fields,'a',$lokey);
    $fields2 = plugin_common::create_fields_quote($fields2,'b');
    $fields3 = plugin_common::create_fields_quote($fields3,'c');
    $fields4 = plugin_common::create_fields_quote($fields4,'d');
    $fields5 = plugin_common::create_fields_quote($fields5,'e');
    $fields7 = plugin_common::create_fields_quote($fields7,'g');
    // $fields8 = plugin_common::create_fields_quote($fields8,'h');
    $fields10 = plugin_common::create_fields_quote($fields10,'j');
	break;

case 'pro':
    // 项目市场初始化数据
    $ahr['cur'] = 'pro';$ahr['lm'] = 'pro';
    $srckey = 'srcval';// srckey => srcval
    $srctip = '请输入项目名称';
    $SEO['title'] = '项目市场';
    // 用户验证
    if (in_array($c,array('ajax','del','page','op')) && empty($gUid)) plugin_common::jumpgo('您尚未登录！');
    // 预设表
    $table_c = $tpre.'_task';
    $table = DB::table($table_c);
    $lokey = 'tid';
    $fields = 'task_title,task_tags,task_desc,budget,task_cash,pub_time,start_time,end_time,finish_time,bid_num,province,contact,task_file,task_type,task_status,is_trust,is_top,indus_id,uid,username';
    $where = '';$order = '';$limit = '';
    break;

case 'pds':
    // 项目市场初始化数据
    $ahr['cur'] = 'pro';$ahr['lm'] = 'pro_details';
    $SEO['title'] = '项目市场详情';
    // $ahr['ur_here'] = $ur_here;
    // 用户验证
    if (in_array($c,array('ajax','black','col','bid','leave_m')) && empty($gUid)) plugin_common::jumpgo('您尚未登录！');
    // 预设表
    $table_c = $tpre.'_task';
    $table = DB::table($table_c);
    $lokey = 'tid';
    $fields = 'task_title,task_tags,task_desc,budget,task_cash,pub_time,start_time,end_time,work_time,sub_time,finish_time,sp_end_time,province,contact,task_file,view_num,focus_num,leave_num,bid_num,work_num,mark_num,task_type,task_status,is_trust,is_top,indus_id,uid,username';
    $where = '';$order = '';$limit = '';
	break;

case 'help':
    // 外包帮助
    $ahr['cur'] = 'help';$ahr['lm'] = 'help';
    $SEO['title'] = '外包帮助';
    echo "帮助";
    break;
}

if ($pluginop=='talent' || $pluginop=='pro') {
    $table_pro = DB::table('project_task');
    /*额外数据*/
    if ($pluginop=='talent') {
        $extra['url'] = 'home.php?mod=spacecp&ac=profile&op=verify';
        $extra['name'] = '成为服务商';
    } elseif ($pluginop=='pro') {
        $extra['url'] = 'home.php?mod=project&do=publish&ac=page';
        $extra['name'] = '发布项目';
    }
    // 项目总量：
    $sql = 'SELECT count('.$lokey.') from '.$table_pro;
    $pros['sum'] = cache_data($sql, 'pros_sum', 'result_first', 60);
    // 今日新增项目
    $sql = $sql . ' where pub_time between '. STARTTIME .' and '.ENDTIME;
    $pros['sum_today'] = cache_data($sql, 'pros_sum'.ENDTIME, 'result_first', 60);

    // 最新成交
    $sql = 'SELECT task_title,pub_time,start_time,task_cash,budget,uid,username,task_pic from '.$table_pro.' as a left join  where task_status=8 order by pub_time desc limit 20';
    $fields_pro = 'task_title,pub_time,start_time,task_cash,budget,uid,username,task_pic';
    $fields_pro = plugin_common::create_fields_quote($fields_pro,'a','tid');
    $fields_cate = plugin_common::create_fields_quote('name','b');
    $sql = sprintf('SELECT %s,%s from %s as a left join %s as b on a.indus_id=b.cid where task_status=8 order by pub_time desc limit 20',$fields_pro,$fields_cate,$table_pro,DB::table('project_category'));
    // $sql = 'SELECT task_title,pub_time,start_time,task_cash,budget,uid,username,task_pic from '.$table_pro.' where task_status=8 order by pub_time desc limit 20';
    // debug($sql,1);
    $newProTrade_c = cache_data($sql,'newProTrade','fetch_all',60);
    foreach ($newProTrade_c as $v) {
        $v['pub_time'] = dgmdate($v['pub_time'],'u');
        $v['task_title'] = cutstr($v['task_title'],26,'…');
        $newProTrade[] = $v;
    }
    // unset($table_pro);
}

// debug($ahr,1);
// require_once LO_CTRL.'pro.php';
require_once LO_CTRL . ($ahr['lm']?$ahr['lm']:$pluginop) .'.php';
// include template(THISPLUG.':index_pro');
include template( THISPLUG.':'. $oproute . ($ahr['lm']?'_'.$ahr['lm']:'_'.$pluginop) );
?>