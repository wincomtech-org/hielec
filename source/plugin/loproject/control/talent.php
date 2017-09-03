<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

/*
 * 分支
*/
switch ($c) {
case 'ajax':
    # code...
    break;

default://项目市场
    /*列表页*/
    // 筛选
    $sql = sprintf("SELECT cid,name from %s where pid=1 and is_show=1",$table5);
    $industrys = cache_data($sql,'industrys');
    $where = 'WHERE 1=1 ';
    // $where = ' where a.uid='.$gUid;
    // $where .= ' and c.verify2=1 AND c.verify5=1';
    extract($_GET);
    $aa = (is_null($aa)) ? null : trim($aa);
    if ($aa!=null&&$aa>-1) {
        $where .= ' AND b.field4 LIKE \'%'.$aa.'%\'';
        $jumpext .= '&aa='.$aa;
    }
    if ($bb) {
        $where .= ' AND a.user_type='.$bb;
        $jumpext .= '&bb='.$bb;
    }
    if ($ff) {
        $where .= ' AND b.resideprovince='.$ff;
        $jumpext .= '&ff='.$ff;
    }
    if ($srcval) {
        $where .= ' AND a.username LIKE \''. $srcval .'%\' ';
        // $jumpext .= '&srcval='.$srcval;
    }
    // 如果条件来自一张表，则替换
    $where2 = $leftjoin2 = $leftjoin3 = $leftjoin4 = '';
    if (strpos($where,'b.')!==false) {
        $where2 = $where;
        $leftjoin2 = sprintf(' as a left join %s as b on a.uid=b.uid',$table2);
    } elseif (strpos($where,'c.')!==false) {
        $leftjoin3 = sprintf(' as a left join %s as b on a.uid=c.uid',$table3);
    } else {
        $where2 = str_replace('a.','',$where);
        $leftjoin = '';
    }
    $jumpurl .= $jumpext;

    // 数据分页
    $sql = sprintf("SELECT count(*) from %s %s %s %s;",$table,$leftjoin2,$leftjoin3,$where2);
    $multi = plugin_common::pager($sql, $_GET['page'], $jumpurl, $pagesize);
    $multipage = $multi[0];
    // 查询结果集并处理
    $limit = $multi[1];
    // 常规
    // $sql = sprintf("SELECT %s,%s,%s FROM %s AS a LEFT JOIN %s AS b ON a.uid=b.uid LEFT JOIN %s AS c ON a.uid=c.uid %s %s %s",$fields,$fields2,$fields3,$table,$table2,$table3,$where,$order,$limit);
    // 带用户组
    // $sql = sprintf("SELECT %s,%s,%s,%s FROM %s AS a LEFT JOIN %s AS b ON a.uid=b.uid LEFT JOIN %s AS c ON a.uid=c.uid LEFT JOIN %s AS f ON a.groupid=f.groupid %s %s %s",$fields,$fields2,$fields3,$fields6,$table,$table2,$table3,$table6,$where,$order,$limit);
    // 带统计的(子查询)
    // 近3个月
    $month3 = CURTIME - 30*24*60*60;
    // $sql = sprintf("
    //         SELECT 
    //         %s,%s,%s,
    //         (select sum(task_cash) from %s as d where a.uid=d.uid and d.task_status=8 and d.pub_time>%d) as amount,
    //         (select count(*) from %s as d2 where a.uid=d2.uid and d2.task_status=8 and d2.pub_time>%d) as total 
    //         FROM %s AS a 
    //         LEFT JOIN %s AS b ON a.uid=b.uid 
    //         LEFT JOIN %s AS c ON a.uid=c.uid 
    //         %s %s %s",
    //         $fields,$fields2,$fields3,$table4,$month3,$table4,$month3,$table,$table2,$table3,$where,$order,$limit
    //     );
    // 没有使用 sprintf
    $sql = "
            SELECT 
                $fields,$fields2,$fields3,
                (select sum(task_cash) from $table4 as d where a.uid=d.uid and d.task_status=8 and d.pub_time>$month3) as amount,
                (select count(*) from $table4 as d2 where a.uid=d2.uid and d2.task_status=8 and d2.pub_time>$month3) as total 
            FROM $table AS a
            LEFT JOIN $table2 AS b ON a.uid=b.uid 
            LEFT JOIN $table3 AS c ON a.uid=c.uid 
            $where $order $limit
        ";
    // 统计2 简写(错误)
    // $sql = sprintf("SELECT a.uid,b.amount,b.total from %s as a,(select uid,sum(task_cash) as amount,count(*) as total from %s) as b",$table,$table4);
    // 多条件
    // $sql = sprintf("SELECT a.uid,b.amount,b.total from %s as a,(select uid,sum(task_cash) as amount,count(*) as total from %s where task_status=8) as b where a.uid=b.uid",$table,$table4);
// debug($sql,1);

    $list_c = cache_data($sql);
    foreach ($list_c as $v) {
        $v['reside_address'] = $v['resideprovince'].' '.$v['residecity'].' '.$v['residedist'];
        if (empty(trim($v['reside_address']))) {
            $v['reside_address'] = '未知';
        }
        $v['user_type'] = $v['user_type']?$arrProUserType[$v['user_type']]:'不明';
        $v['amount'] = $v['amount'] ? $v['amount'] : 0;
        $v['total'] = $v['total'] ? $v['total'] : 0;
        $list[] = $v;
    }
// debug($list,1);
    break;
}
?>