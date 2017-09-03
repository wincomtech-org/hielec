<?php 
if(!defined('IN_DISCUZ')) exit('Access Denied');

if ($ac=='ajax') {

} elseif ($ac=='done') {
    DB::update($table_c,array('ext_status'=>1),array($lokey=>$loid));
    $res = DB::update('project_task',array('task_status'=>6,'sub_time'=>time()),array('tid'=>$taskID));
    if ($res) {
        plugin_common::jumpgo('已发起交付，等待对方确认！',$jumpurl,'','success');
    } else {
        plugin_common::jumpgo('发起失败！',$jumpurl);
    }
    dheader('location:'.$jumpurl);
    // showmessage('');
} elseif ($ac=='over') {
    $res1 = DB::update($table_c,array('ext_status'=>5),array($lokey=>$loid));
    $res2 = DB::update('project_task',array('task_status'=>9,'finish_time'=>time()),array('tid'=>$taskID));
    $sql = "UPDATE ".DB::table('common_member_count')." SET extcredits2=extcredits2+{$task[task_cash]} WHERE uid={$task[uid]};";
    $res3 = DB::query($sql);
    if ($res1 && $res2 && $res3) {
        plugin_common::jumpgo('成功放弃！',$jumpurl,'','success');
    } else {
        plugin_common::jumpgo('未能放弃！',$jumpurl);
    }
    // dheader('location:'.$jumpurl);
} else {
    /*列表页*/
    $table2 = DB::table('project_task');
    $table3 = DB::table('project_category');
    $fields2 = 'task_title,task_status,indus_id';
    $fields3 = 'name';
    $fields = plugin_common::create_fields_quote($fields,'a',$lokey);
    $fields2 = plugin_common::create_fields_quote($fields2,'b');
    $fields3 = plugin_common::create_fields_quote($fields3,'c');
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
    $list_c = cache_data($sql);
    foreach ($list_c as $v) {
        $v['bid_time'] = date('Y-m-d H:i:s',$v['bid_time']);
        $list[] = $v;
    }
    // debug($list);
}
?>