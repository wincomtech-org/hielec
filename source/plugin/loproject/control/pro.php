<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

/*
 * 分支
*/
switch ($c) {
case 'ajax':
    break;

case 'del':
    break;

case 'page':
    break;

case 'op':
    break;

default://项目市场
    // 筛选
    $sql = sprintf("SELECT cid,name from %s where pid=1 and is_show=1",DB::table('project_category'));
    $industrys = cache_data($sql,'industrys');

    $where = ' WHERE a.task_status NOT IN (0,1,7,10) ';
    extract($_GET);
    $aa = (is_null($aa)) ? null : intval($aa);
    if ($aa!=null&&$aa>-1) {
        $where .= ' AND a.indus_id='.$aa;
        $jumpext .= '&aa='.$aa;
    }
    $bb = (is_null($bb)) ? null : intval($bb);
    if (!is_null($bb)&&$bb>-1) {
        $where .= ' AND a.task_status='.$bb;
        $jumpext .= '&bb='.$bb;
    }
    $cc = (is_null($cc)) ? null : intval($cc);
    if (!is_null($cc)&&$cc>-1) {
        $where .= ' AND a.is_trust='.$cc;
        $jumpext .= '&cc='.$cc;
    }
    if ($dd) {
        $where .= ' AND a.task_type=\''.$dd.'\'';
        $jumpext .= '&dd='.$dd;
    }
    if ($ee) {
        $stime = STARTTIME-(($ee-1)*24*60*60);
        $etime = ENDTIME;
        $where .= ' AND (a.pub_time between '. $stime .' and '. $etime .') ';
        $jumpext .= '&ee='.$ee;
    }
    if ($ff) {
        $where .= ' AND a.province=\''.$ff.'\'';
        $jumpext .= '&ff='.$ff;
    }
    if ($gg) {
        $tch = explode('-', $arrProTaskCash[$gg]);
        $tchn = count($tcash);
        if ($tchn>1) {
            $where .= ' AND (a.task_cash between \''. $tch[0] .'\' and \''. $tch[1] .'\') ';
        } elseif ($tchn&&$gg=1) {
            $where .= ' AND a.task_cash<=\''. $tch[0] .'\' ';
        } else {
            $where .= ' AND a.task_cash>\''. $tch[0] .'\' ';
        }
        unset($tch,$tchn);
        $jumpext .= '&gg='.$gg;
    }
    if ($srcval) {
        $where .= ' AND a.task_title LIKE \''. $srcval .'%\' ';
        // $jumpext .= '&srcval='.$srcval;
    }
    // 如果条件来自一张表，则替换
    $where2 = str_replace('a.','',$where);
    $jumpurl .= $jumpext;
// debug($where2,1);

    // 数据分页
    $sql = sprintf("SELECT count(*) from %s %s;",$table,$where2);
    $multi = plugin_common::pager($sql, $_GET['page'], $jumpurl, $pagesize);
    $multipage = $multi[0];
    // 查询结果集并处理
    $fields = plugin_common::create_fields_quote($fields,'a',$lokey);
    $limit = $multi[1];
    $sql = sprintf("SELECT %s,b.name from %s AS a LEFT JOIN %s AS b ON a.indus_id=b.cid %s %s %s",$fields,$table,DB::table('project_category'),$where,$order,$limit);
    $list_c = cache_data($sql);
    foreach ($list_c as $v) {
        $v['pub_time'] = date('Y-m-d H:i:s',$v['pub_time']);
        // $v['task_cash'] = ($v['task_cash']>0.00)?$v['task_cash']:$v['budget'];
        $list[] = $v;
    }

// debug($list);
    break;
}

// debug($ahr);
?>