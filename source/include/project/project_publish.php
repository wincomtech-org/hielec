<?php 
if(!defined('IN_DISCUZ')) exit('Access Denied');

if ($ac=='ajax') {

} elseif ($ac=='del') {

} elseif ($ac=='op' && submitcheck('publishsubmit')) {
    extract($_POST);
    // 字段验证
    // 要保存的数据
    $data = array(
            'task_title'    => $task_title,
            'task_type'     => $task_type,
            'task_tags'     => $task_tags,
            'task_desc'     => $task_desc,
            'indus_id'      => $indus_id,
            'budget'        => $budget,
            'start_time'    => strtotime($start_time),
            'end_time'      => strtotime($end_time),
            'province'      => $province,
            'contact'       => $contact,
        );
    // 附件处理
    $new_file = plugin_common::update_file('task_file',$task_oldfile);
    if ($new_file) {
        $data['task_file'] = $new_file;
    }
    // 数据库操作
    if ($tid) {
        $res = DB::update('project_task',$data,array('tid'=>$tid));
        $jumpurl .= '&tid='.$tid;
    } else {
        $task_status = (AUDIT)?0:2;
        $data['pub_time']       = time();
        $data['task_status']    = $task_status;
        $data['uid']            = $gUid;
        $data['username']       = $gUsername;
        $data['is_trust']       = 0;
        $data['is_top']         = 0;
        $data['age_requirement']= 1;
        $res = DB::insert('project_task',$data);
    }
    // 后续操作
    if ($res) {
        showmessage('操作成功',$jumpurl);
    } else {
        showmessage('操作失败',$jumpurl);
    }
    // runquery($sql);

} elseif ($ac=='page') {
    $tid = $_REQUEST['tid']?intval($_REQUEST['tid']):0;
    $SEO['title'] = '发布项目';
    $form['action'] = 'home.php?mod=project&do=publish';
    $form['button'] = '发布项目';
    // 表单验证 前端
    $Validform['task_title'] = 'datatype="s3-16" errormsg="名称至少3个字符,最多16个字符！"';
    // 获取应用领域
    $sql = sprintf("SELECT cid,name from %s where pid=1 and is_show=1 order by sort",DB::table('project_category'));
    $industrys = cache_data($sql,'industrys');
    /*编辑页*/
    if ($tid) {
        // 如果条件来自一张表，则替换
        $where = str_replace('a.','',$where);
        $where .= ' AND tid='.$tid;
        $fields = $lokey.','.$fields;
        $task = DB::fetch_first(sprintf("SELECT %s from %s %s;",$fields,$table,$where));
        if (empty($task)) showmessage('抱歉，数据源未能获取到。');
        $task['start_time'] = date('Y-m-d',$task['start_time']);
        $task['end_time'] = date('Y-m-d',$task['end_time']);
        $task['attach_url'] = $task['task_file'] ? $upload_common_path . LO_ATTACH .$task['task_file']: '';
        $page['task_file'] = basename($page['task_file']);
    }

} else {
    /*列表页*/
    $jumpurl .= $jumpext;
    // 数据分页
    // 如果条件来自一张表，则替换
    $where2 = str_replace('a.','',$where);
    $sql = sprintf("SELECT count(*) from %s %s;",$table,$where2);
    $multi = plugin_common::pager($sql, $_GET['page'], $jumpurl,10);
    $multipage = $multi[0];
    // 查询结果集并处理
    $fields = plugin_common::create_fields_quote($fields,'a',$lokey);
    $limit = $multi[1];
    $sql = sprintf("SELECT %s,b.name from %s AS a LEFT JOIN %s AS b ON a.indus_id=b.cid %s %s %s",$fields,$table,DB::table('project_category'),$where,$order,$limit);
    $list_c = cache_data($sql);
    foreach ($list_c as $v) {
        $v['pub_time'] = date('Y-m-d H:i:s',$v['pub_time']);
        $list[] = $v;
    }

    /*调试*/
    // echo $pagesize;
    // debug($task);
    // debug($district_prov);
    // $sql = sprintf("INSERT into pre_project_category (`pid`,`level`,`name`,`addtime`,`modtime`,`is_show`,`sort`) values (1,2,'智能穿戴',".time().",".time().",1,50) ;");
    // echo $sql;
}
// debug($jumpurl);
?>