<?php 
if(!defined('IN_DISCUZ')) exit('Access Denied');

if ($ac=='ajax') {

} elseif ($ac=='log') {// 记录

} elseif ($ac=='msg') {// 消息
    // 如果条件来自一张表，则替换
    $where2 = str_replace('a.','',$where);
    // 数据分页
    $sql = sprintf("SELECT count(*) from %s %s;",$table,$where2);
    $multi = plugin_common::pager($sql, $_GET['page'], $jumpurl, $pagesize);
    $multipage = $multi[0];
    // 查询结果集并处理
    $limit = $multi[1];
    $sql = sprintf("SELECT %s FROM %s %s %s %s",$fields,$table,$where,$order,$limit);
    $list_c = cache_data($sql);
    foreach ($list_c as $v) {
        $list[] = $v;
    }
    // debug($list);

} elseif ($ac=='comp') {
    /*已评价项目*/
    $where .= ' AND a.msg_type=6';
    $jumpurl .= $jumpext;
    // 数据分页
    $sql = sprintf("SELECT count(*) from %s as a left join %s as b on a.tid=b.tid %s;",$table,$table2,$where);
    $multi = plugin_common::pager($sql, $_GET['page'], $jumpurl, $pagesize);
    $multipage = $multi[0];
    // 查询结果集并处理
    $limit = $multi[1];
    $sql = sprintf("SELECT %s,%s,%s FROM %s as a LEFT JOIN %s as b ON a.tid=b.tid LEFT JOIN %s as c ON b.indus_id=c.cid %s %s %s",$fields,$fields2,$fields3,$table,$table2,$table3,$where,$order,$limit);
    $list_c = cache_data($sql);
    foreach ($list_c as $v) {
        $list[] = $v;
    }
    // debug($list);

} else {
    /*待评价项目*/
    $where = 'WHERE b.task_status=8 AND d.comment_num=0 AND d.uid='.$gUid;
    // 数据分页
    $sql = sprintf("SELECT count(*) FROM %s as b LEFT JOIN %s as d ON b.tid=d.tid %s;",$table2,$table4,$where);
    $multi = plugin_common::pager($sql, $_GET['page'], $jumpurl, $pagesize);
    $multipage = $multi[0];
    // 查询结果集并处理
    $limit = $multi[1];
    $sql = sprintf("SELECT %s,%s,%s FROM %s as b JOIN %s as d ON b.tid=d.tid LEFT JOIN %s as c ON b.indus_id=c.cid %s %s %s",$fields2,$fields4,$fields3,$table2,$table4,$table3,$where,$order,$limit);
    $list_wait_c = cache_data($sql);
    foreach ($list_wait_c as $v) {
        $v['finish_time'] = dgmdate($v['finish_time']);
        $list_wait[] = $v;
    }
    // debug($list_wait);
}
?>