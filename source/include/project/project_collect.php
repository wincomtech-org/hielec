<?php 
if(!defined('IN_DISCUZ')) exit('Access Denied');

if ($ac=='del') {
    $loid = intval(getgpc('loid'));
    if (empty($loid)) showmessage('非法操作！');
    $condition = array('id'=>$loid);
    $res = DB::delete($table_c,$condition);
    if ($res) {
        // $tid = DB::result_first("SELECT tid from %s where id=%d",$table,$loid);
        // DB::query("UPDATE ".DB::table('project_task')." SET focus_num=focus_num-1 WHERE tid={$tid};");
        showmessage('您已取消收藏！',$jumpurl);
    } else {
        showmessage('操作失败！');
    }
} elseif ($ac=='talent') {
    $table2 = DB::table('common_member');
    $table3 = DB::table('common_member_profile');
    $fields2 = 'uid,email,username,user_type,groupid';
    $fields3 = 'field4,resideprovince,residecity,residedist';
    $fields = plugin_common::create_fields_quote($fields,'a',$lokey);
    $fields2 = plugin_common::create_fields_quote($fields2,'b');
    $fields3 = plugin_common::create_fields_quote($fields3,'c');
    // 如果条件来自一张表，则替换
    $where .= ' AND a.source=\'talent\'';
    $where2 = str_replace('a.','',$where);
    $jumpurl .= $jumpext;
    // 数据分页
    $sql = sprintf("SELECT count(*) from %s %s;",$table,$where2);
    $multi = plugin_common::pager($sql, $_GET['page'], $jumpurl, $pagesize);
    $multipage = $multi[0];
    // 查询结果集并处理
    $limit = $multi[1];
    $sql = sprintf("SELECT %s,%s,%s FROM %s AS a LEFT JOIN %s as b ON a.tid=b.uid LEFT JOIN %s AS c ON b.uid=c.uid %s %s %s",$fields,$fields2,$fields3,$table,$table2,$table3,$where,$order,$limit);
    // debug($sql,1);
    $list_c = cache_data($sql);
    foreach ($list_c as $v) {
        $v['reside_address'] = $v['resideprovince'].' '.$v['residecity'].' '.$v['residedist'];
        $v['user_type'] = $v['user_type'] ? $arrProUserType[$v['user_type']] : '不明';
        $list[] = $v;
    }
// debug($list,1);

} elseif ($ac=='case') {
    $table2 = DB::table('project_case');
    $table3 = DB::table('project_category');
    $fields2 = 'obj_id,obj_type,indus_id,case_title,case_desc,case_img,case_price,addtime';
    $fields3 = 'name';
    $fields = plugin_common::create_fields_quote($fields,'a',$lokey);
    $fields2 = plugin_common::create_fields_quote($fields2,'b');
    $fields3 = plugin_common::create_fields_quote($fields3,'c');
    // 如果条件来自一张表，则替换
    $where .= ' AND a.source=\'case\'';
    $where2 = str_replace('a.','',$where);
    $jumpurl .= $jumpext;
    // 数据分页
    $sql = sprintf("SELECT count(*) from %s %s;",$table,$where2);
    $multi = plugin_common::pager($sql, $_GET['page'], $jumpurl, $pagesize);
    $multipage = $multi[0];
    // 查询结果集并处理
    $limit = $multi[1];
    $sql = sprintf("SELECT %s,%s,%s FROM %s AS a LEFT JOIN %s as b ON a.tid=b.case_uid LEFT JOIN %s AS c ON b.indus_id=c.cid %s %s %s",$fields,$fields2,$fields3,$table,$table2,$table3,$where,$order,$limit);
    // debug($sql,1);
    $list_c = cache_data($sql);
    foreach ($list_c as $v) {
        $list[] = $v;
    }
// debug($list,1);

} else {
    /*列表页*/
    $table2 = DB::table('project_task');
    $table3 = DB::table('project_category');
    $fields2 = 'task_title,indus_id,task_status,uid,username,province,pub_time,budget,bid_num,start_time,end_time';
    $fields3 = 'name';
    $fields = plugin_common::create_fields_quote($fields,'a',$lokey);
    $fields2 = plugin_common::create_fields_quote($fields2,'b');
    $fields3 = plugin_common::create_fields_quote($fields3,'c');
    // $where = ' where a.tid='.$loid;
    // 如果条件来自一张表，则替换
    $where2 = str_replace('a.','',$where);
    $jumpurl .= $jumpext;
    // 数据分页
    $sql = sprintf("SELECT count(*) from %s %s;",$table,$where2);
    $multi = plugin_common::pager($sql, $_GET['page'], $jumpurl, $pagesize);
    $multipage = $multi[0];
    // 查询结果集并处理
    $limit = $multi[1];
    $sql = sprintf("SELECT %s,%s,%s FROM %s AS a LEFT JOIN %s as b ON a.tid=b.tid LEFT JOIN %s AS c ON b.indus_id=c.cid %s %s %s",$fields,$fields2,$fields3,$table,$table2,$table3,$where,$order,$limit);
    // debug($sql,1);
    $list_c = cache_data($sql);
    foreach ($list_c as $v) {
        $v['expire_s'] = ($v['task_status']<2 && $v['end_time']<time())?true:false;
        // $v['time_remaining'] = dgmdate($v['start_time'],'u');
        // $v['time_remaining'] = date('Y-m-d H:i:s',$v['start_time']);
        $v['time_remaining'] = UnitChange($v['start_time']-time(),$format_unit);
        $list[] = $v;
    }
    // debug($list,1);
}
?>