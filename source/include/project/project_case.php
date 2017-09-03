<?php 
if(!defined('IN_DISCUZ')) exit('Access Denied');

if ($ac=='ajax') {

} elseif ($ac=='del') {
    debug('未处理',1);
} elseif ($ac=='op' && submitcheck('casesubmit')) {
    extract($_POST);
    // 字段验证
    // 要保存的数据
    $data = array(
            'case_title'    => $case_title,
            'indus_id'      => $indus_id,
            'case_desc'     => $case_desc,
            'modtime'       => time()
        );
    // 数据库操作
    if ($loid) {
        $res = DB::update($table_c,$data,array($lokey=>$loid));
        $jumpurl .= '&loid='.$loid;
    } else {
        $data['addtime'] = time();
        $res = DB::insert($table_c,$data);
    }
    // 后续操作
    if ($res) {
        showmessage('操作成功',$jumpurl);
    } else {
        showmessage('操作失败',$jumpurl);
    }
} elseif ($ac=='page') {
    $loid = intval(getgpc('loid'));
    $SEO['title'] = '发布案例';
    $form['action'] = 'home.php?mod=project&do=case';
    $form['button'] = '发布';
    // 表单验证 前端
    $Validform['case_title'] = 'datatype="*3-16" errormsg="名称至少3个字符,最多16个字符！"';
    // 获取应用领域
    $sql = sprintf("SELECT cid,name from %s where pid=1 and is_show=1 order by sort",DB::table('project_category'));
    $industrys = cache_data($sql,'industrys');
    /*发布页*/
    if ($loid) {
        $where = 'WHERE '. $lokey .'='. $loid;
        $fields = plugin_common::create_fields_quote($fields,'',$lokey);
        $sql = sprintf("SELECT %s from %s %s;",$fields,$table,$where);
        $page = cache_data($sql,'','fetch_first');
        if (empty($page)) showmessage('抱歉，数据源丢失。');
    }
} else {
    /*列表页*/
    $table2 = DB::table('project_task');
    $table3 = DB::table('project_category');
    $fields2 = 'tid,task_title,task_status,indus_id';
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
    $sql = sprintf("SELECT %s,%s,%s FROM %s AS a LEFT JOIN %s as b ON a.obj_id=b.tid LEFT JOIN %s AS c ON b.indus_id=c.cid %s %s %s",$fields,$fields2,$fields3,$table,$table2,$table3,$where,$order,$limit);
    $list_c = cache_data($sql);
    foreach ($list_c as $v) {
        $list[] = $v;
    }
}
?>