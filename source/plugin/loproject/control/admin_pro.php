<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

$c = $_REQUEST['c']?trim($_REQUEST['c']):'list';
// $cAllow = array('ajax','del','op','page','list');
// if (!in_array($c,$cAllow)) {
//     plugin_common::jumpgo('非法传递！');
// }

$jumpurl .= '&pluginop='.$pluginop;
$jumpext = '';
$table_c = $tpre .'_task';
$table = DB::table($table_c);
$lokey = 'tid';
$fields = 'task_title,task_tags,task_desc,budget,task_cash,pub_time,start_time,end_time,finish_time,province,contact,task_file,task_type,task_status,is_trust,is_top,indus_id,uid,username';
$where = '';
$order = '';
$limit = '';

/*
 * 分支
*/
switch ($c) {
case 'ajax':
    # code...
    break;
    
case 'del':
    // $table = 'project_task';
    // 全新方法
    project_del($table,$lokey);
    // 公用的老方法
    // plugin_common::common_taskdel($table, $wh, 'a', $skip, $del_filekey);
    break;
    
case 'op':
// debug($_POST,1);
    // 数据提交
    if (submitcheck('publishsubmit')) {
        extract($_POST);
        // 字段验证
        $uid = $uid?$uid:$gUid;
        $username = $username?$username:$gUsername;
        // 要保存的数据
        $data = array(
                'task_title'    => $task_title,
                'task_type'     => $task_type,
                'task_tags'     => $task_tags,
                'task_desc'     => $task_desc,
                'indus_id'      => $indus_id,
                'budget'        => $budget,
                'task_cash'     => $task_cash,
                'start_time'    => strtotime($start_time),
                'end_time'      => strtotime($end_time),
                'province'      => $province,
                'contact'       => $contact,
                'uid'           => $uid,
                'username'      => $username,
                'task_status'   => $task_status,
            );
        // 附件处理
        $new_file = plugin_common::update_file('task_file',$task_oldfile);
        if ($new_file) {
            $data['task_file'] = $new_file;
        }
        // 数据库操作
        if ($$lokey) {
            $res = DB::update($table_c,$data,array($lokey=>$$lokey));
            $jumpurl .= '&c=page&'.$lokey.'='.$$lokey;
        } else {
            $task_status = (AUDIT)?0:2;
            $data['pub_time'] = time();
            $data['task_status'] = $task_status;
            $data['is_trust'] = 0;
            $data['is_top'] = 0;
            $data['age_requirement'] = 1;
            $res = DB::insert($table_c,$data);
        }
        // 后续操作
        if ($res) {
            plugin_common::jumpgo('操作成功',$jumpurl,$jumpext,'success');
        } else {
            plugin_common::jumpgo('操作失败',$jumpurl,$jumpext);
        }
    }
    break;
    
case 'page':
    $$lokey = $_GET[$lokey]?intval($_GET[$lokey]):0;

    $ahr['cur'] = 'pro_page';
    $form['action'] = $mainurl;
    $form['button'] = '提交';
    $form['url_category'] = $mainurl.'&pluginop=cate&c=list';
    $form['url_message'] = $mainurl.'&pluginop=message&c=list';
    // $form['sid'] = $_SESSION['sid'] = plugin_common::locrs();

    // 表单验证 前端
    $Validform['task_title'] = 'datatype="s3-16" errormsg="名称至少3个字符,最多16个字符！"';

    // 编辑页
    $sql = sprintf("SELECT cid,name from %s where pid=1 and is_show=1",DB::table('project_category'));
    $industrys = cache_data($sql,'industrys');
    if ($$lokey) {
        $fields = $lokey.','.$fields;
        $fields .= ',task_tags,task_desc,contact,task_file';
        $page = DB::fetch_first("select ". $fields ." from ". $table ." where ".$lokey."=".$$lokey);
        $page['task_cash'] = $page['task_cash'] ? $page['task_cash']: 0;
        $page['start_time'] = date('Y-m-d',$page['start_time']);
        $page['end_time'] = date('Y-m-d',$page['end_time']);
        $page['attach_url'] = $page['task_file'] ? $upload_common_path . LO_ATTACH .$page['task_file']: '';
        $page['task_file'] = basename($page['task_file']);
    }

    break;

case 'list':// 项目市场列表
    /*列表页*/
    $SEO['title'] = $lang['thead'].'后台管理列表';

    // 预设table表格
    $tab['th'] = array('ID','标题','预算','实际赏金','应用领域','发布者','发布时间','任务类型','任务状态','是否托管','操作');
    $tab['td'] = array('tid','task_title','budget','task_cash','indus_id','username','pub_time','task_type','task_status','is_trust');
    $tab['operator'] = array(
        // array($mainurl.'&pluginop=ajax&c=show'.$jumpext.'&'.$lokey.'=','显隐'),
        array($mainurl.'&c=page'.$jumpext.'&'.$lokey.'=', '编辑'),
        array($mainurl.'&c=del'.$jumpext.'&'.$lokey.'=', '删除')
    );

    // 数据查询、处理
    $sql = sprintf("SELECT cid,name from %s where pid=1 and is_show=1",DB::table('project_category'));
    $industrys = cache_data($sql,'industrys');
    foreach ($industrys as  $v) {
        $industrys_c[$v['cid']] = $v['name'];
    }
    $order = ' ORDER BY pub_time DESC,'. $lokey .' DESC';

    // 数据分页
    $sql = 'SELECT count('.$lokey.') FROM '. $table . $where;
    $multi = plugin_common::pager($sql, $_GET['page'], $mainurl.$jumpext, $pluginvar['pagesize']);
    $multipage = $multi[0];
    // 查询结果集并处理
    $fields = plugin_common::create_fields_quote($fields,'',$lokey);
    $limit = $multi[1];
    $sql = sprintf("SELECT %s from %s %s %s %s",$fields,$table,$where,$order,$limit);
    $list_c = cache_data($sql);
    foreach ($list_c as $v) {
        $v['indus_id'] = $industrys_c[$v['indus_id']];
        $v['pub_time'] = date('Y-m-d H:i',$v['pub_time']);
        $v['task_type'] = $arrProTaskType[$v['task_type']];
        $v['task_status'] = $arrProTaskStatus[$v['task_status']];
        $v['is_trust'] = $arrProTrustStatus[$v['is_trust']];
        $list[] = $v;
    }

// debug($list);
     break; 
}
include template( THISPLUG.':'. $oproute . ($ahr['lm']?'_'.$ahr['lm']:'_'.$pluginop) . ($c?'_'.$c:'') );
// include template(THISPLUG.':admin_list');
// include template('_public/admin_common_op');
?>