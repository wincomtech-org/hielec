<?php 
if(!defined('IN_DISCUZ'))exit('Access Denied');

// $allow_do = array('publish','tender','comment','case','collect');
$do = getgpc('do');
$do = $do ? trim($do) : 'publish';
$ac = $_REQUEST['ac']?$_REQUEST['ac']:'list';

$mainurl = $jumpurl = 'home.php?mod=project';
$jumpurl .= '&do='.$do;
$jumpext = '';

// 附件路径
$upload_common_path = dirname(LO_URL) . LO_UPLOAD . 'project/';// realpath(LO_URL) 会除去 / ./ ../ 根路径
$upload_common_path_op = realpath(DISCUZ_ROOT) . LO_UPLOAD . 'project/';// 物理路径

// 初始化插件数据
define('THISPLUG', 'loproject');
define('MODULE', 'project');
/*插件数据*/
loadcache('plugin');
$pluginvar = $_G['cache']['plugin'][THISPLUG];
// 每页记录数
if ($pluginvar['pagesize']) { $pagesize = intval($pluginvar['pagesize']); }
// 积分与货币转换比率
$loper = explode(':', $pluginvar['loper']);
$loper = ($loper[1]/$loper[0]);
// 审核开关
define('AUDIT', $pluginvar['audit']);

/*权限认证*/
if (in_array($do,array('publish','case'))) {
    if (!$gUserAuth['verify5']) {
        showmessage('您需要手机认证！','home.php?mod=spacecp&ac=profile&op=verify&vid=5');
    }
    if (!($gUserAuth['verify4'] || $gUserAuth['verify6'])) {
        showmessage('您还没有进行资格认证！','home.php?mod=spacecp&ac=profile&op=verify&vid=6');
    }
}

switch ($do) {
    case 'publish'://我发布的项目
        // $ahr['cur'] = 'home.php?mod=project&do=publish';
        $table_c = 'project_task';
        $table = DB::table($table_c);
        $lokey = 'tid';
        $fields = 'task_title,task_tags,task_desc,budget,task_cash,pub_time,start_time,end_time,finish_time,province,contact,task_file,task_type,task_status,is_trust,indus_id,uid,username';
        // $fields .= ',view_num,focus_num,leave_num,bid_num,is_top';
        $where = 'WHERE a.uid='.$gUid.' AND a.task_status!=7';
        $order = '';$limit = '';
        break;

    case 'bid'://我竞标的项目
        $table_c = 'project_task_bid';
        $table = DB::table($table_c);
        $lokey = 'bid_id';
        $fields = 'tid,uid,username,bid_time,bid_status,ext_status,is_view,hasdel';
        // $fields .= ',quote,cycle,message,comment_num';
        $where = 'WHERE a.uid='.$gUid;
        $order = '';$limit = '';
        if (in_array($ac,array('done','over'))) {
            $loid = intval(getgpc('loid'));
            $sql = "SELECT tid,bid_status,ext_status from ".$table." where {$lokey}={$loid};";
            $task_bid = cache_data($sql,'','fetch_first');
            if (empty($task_bid)) {
                showmessage('数据源丢失！');
            }
            if ($task_bid['bid_status']!=1 || $task_bid['ext_status']) {
                showmessage('非法操作状态！');
            }
            $taskID = $task_bid['tid'];
            $task = DB::fetch_first("SELECT uid,task_cash from ".DB::table('project_task')." where tid={$taskID}");
            if (empty($task)) {
                showmessage('该项目源不存在，已被删除 或 篡改');
            }
        }
        break;

    case 'comment'://与我相关的评价
        $table_c = 'project_msg';
        $table = DB::table($table_c);
        $lokey = 'msg_id';
        $fields = 'msg_type,msg_status,view_status,tid,uid,uname,touid,touname,title,content,addtime';
        $where = 'WHERE a.msg_status IN (0,2) AND a.uid='.$gUid;
        $order = '';$limit = '';
        $table2 = DB::table('project_task');
        $table3 = DB::table('project_category');
        $table4 = DB::table('project_task_bid');
        $fields2 = 'task_title,task_status,indus_id,task_desc,uid,finish_time';
        $fields3 = 'name';
        $fields4 = 'comment_num';
        $fields = plugin_common::create_fields_quote($fields,'a');
        $fields2 = plugin_common::create_fields_quote($fields2,'b');
        $fields3 = plugin_common::create_fields_quote($fields3,'c');
        $fields4 = plugin_common::create_fields_quote($fields4,'d');
        //待评价项目
        if ($ac=='comp') {
            $fields2 .= ',b.tid';
        }
        //已评价项目
        if ($ac=='list') {
            $fields .= ','.$lokey;
        }
        break;

    case 'case'://我的案例
        $table_c = 'project_case';
        $table = DB::table($table_c);
        $lokey = 'case_id';
        $fields = 'obj_id,obj_type,indus_id,case_title,case_desc,case_img,case_price,addtime';
        // $where = 'WHERE '. $lokey .'='. $loid;
        $where = 'WHERE case_uid='.$gUid;
        $order = '';$limit = '';
        break;

    case 'collect'://我的收藏 type='col'
        $table_c = 'project_log';
        $table = DB::table($table_c);
        $lokey = 'id';
        $fields = 'brief,tid,source';
        $where = 'WHERE a.type=\'col\' AND a.uid='.$gUid;
        $order = '';$limit = '';
        break;
}

require_once libfile('project/'.$do, 'include');

// $template_dir = TEMPDIR?TEMPDIR.'_'.$ac:'home/project_'.$do.'_'.$ac;
// include_once template($template_dir);
// include '/template/default/home/project/project_publish_list.htm';//不解析
include_once template('home/project/project_'.$do);
// include_once template('home/project_'.$do);
// include template('home/space_privacy');
// include_once template("diy:home/space_activity");
?>