<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

// 初始化
$loid = intval(getgpc('loid'));
$bid = intval(getgpc('bid'));
// ID验证
if (empty($loid) && empty($bid)) plugin_common::jumpgo('非法操作！', LO_CURURL);
// 不能操作自己的
$sUid = cache_data(sprintf("SELECT uid from %s where tid=%d",$table,$loid), $table.$loid.$gUid, 'result_first', 120);
if (in_array($c, array('black','col','bid','leave_m')) && $gUid==$sUid) {
    $msgcnt = array('black'=>'不能举报自己','col'=>'不能收藏自己的','bid'=>'不能给自己投标','leave_m'=>'不能给自己留言');
    plugin_common::jumpgo($msgcnt[$c]);
}
// 是否已举报
if (in_array($c,array('black','list'))) {
    $sql = sprintf("SELECT msg_id from %s where msg_type=7 and uid=%d and tid=%d",DB::table('project_msg'),$gUid,$loid);
    $is_black = cache_data($sql,'taskblack'.$loid.$gUid,'result_first');
}
// 是否已收藏
if (in_array($c,array('col','list'))) {
    $sql = sprintf("SELECT id from %s where type='col' and source='task' and uid=%d and tid=%d",DB::table('project_log'),$gUid,$loid);
    $is_col = cache_data($sql,'taskcol'.$loid.$gUid,'result_first',120);
}
// 是否已投标
if (in_array($c,array('bid','list'))) {
    $sql = "SELECT count(*) from ".DB::table('project_task_bid')." where uid={$gUid} and tid={$loid};";
    $mkey = 'taskbid'.$loid.$gUid;
    $is_bid = cache_data($sql,$mkey,'result_first');
}
// 资格认证
if (stripos('bid,leave_m',$c)!==false) {
    if (!$gUserAuth['verify5']) {
        plugin_common::jumpgo('请进行手机认证！','home.php?mod=spacecp&ac=profile&op=verify&vid=5');
    }
}
if (in_array($c,array('bid'))) {
    if (!($gUserAuth['verify4'] || $gUserAuth['verify6'])) {
        showmessage('您还没有进行资格认证！','home.php?mod=spacecp&ac=profile&op=verify&vid=6');
    }
}

$jumpext = $loid?'&loid='.$loid:'';

/*
 * 分支
*/
switch ($c) {
case 'ajax':
    # code...
    break;

case 'black'://举报
    if ($is_black) {
        plugin_common::jumpgo('您已举报，管理员核实中……');
    }
    $task = cache_data(sprintf("SELECT uid,username from %s where tid=%d",$table,$loid), $table.$loid, 'fetch_first');
    $data = array(
            'msg_type'  => 7,
            'tid'       => $loid,
            'uid'       => $gUid,
            'uname'     => $gUsername,
            'touid'     => $task['uid'],
            'touname'   => $task['username'],
            'title'     => '用户举报',
            'content'   => '用户：【'.$gUid.'】'.$gUsername.' 举报了 用户：【'.$task['uid'].'】'.$task['username'].'的项目：'.$loid,
            'addtime'   => time(),
            'ip'        => CURIP
        );
    $res = DB::insert('project_msg',$data);
    if ($res) {
        plugin_common::jumpgo('举报成功！',$jumpurl,$jumpext,'success');
    } else {
        plugin_common::jumpgo('举报失败！',$jumpurl,$jumpext);
    }
    break;

case 'col'://收藏
    if ($is_col) {
        plugin_common::jumpgo('您已收藏过了哦！',$jumpurl,$jumpext);
    }
    $data = array(
            'type'      => 'col',
            'brief'     => '用户：'.$gUsername.' 收藏了项目：'.$loid,
            'uid'       => $gUid,
            'tid'       => $loid,
            'source'    => 'task'
        );
    $res = plugin_common::common_log($tpre.'_log',$data);
    if ($res) {
        DB::query('UPDATE '.$table.' SET focus_num=focus_num+1 WHERE tid='.$loid);
        plugin_common::jumpgo('收藏成功！',$jumpurl,$jumpext,'success');
    } else {
        plugin_common::jumpgo('收藏失败！',$jumpurl,$jumpext);
    }
    break;

case 'leave_m'://留言
    // 判断用户是否中标
    if (empty($_POST['gWinBid'])) plugin_common::jumpgo('中标后方可评价');
    $data = array(
            'msg_type'  => 6,
            'tid'       => $loid,
            'uid'       => $gUid,
            'uname'     => $gUsername,
            'content'   => $_POST['content'],
            'addtime'   => time(),
            'ip'        => CURIP
        );
    $res = DB::insert('project_msg',$data);
    if ($res) {
        // $sql = 'UPDATE '.$table.' SET leave_num=leave_num+1 WHERE tid='.$loid.';UPDATE '.DB::table('project_task_bid').' SET comment_num=comment_num+1 WHERE tid='.$loid.' AND uid='.$gUid;
        // debug($sql,1);
        // mysqli_query($sql);
        DB::query('UPDATE '.$table.' SET leave_num=leave_num+1 WHERE tid='.$loid);
        DB::query('UPDATE '.DB::table('project_task_bid').' SET comment_num=comment_num+1 WHERE tid='.$loid.' AND uid='.$gUid);
        plugin_common::jumpgo('留言成功！',$jumpurl,$jumpext,'success');
    } else {
        plugin_common::jumpgo('留言失败！',$jumpurl,$jumpext);
    }
    break;

case 'bid'://投标
    if ($is_bid) plugin_common::jumpgo('您已投过标，请耐心等待……');
    $data = array(
            'tid'  => $loid,
            'uid'  => $gUid,
            'username'  => $gUsername,
            // 'quote'  => $quote,
            // 'cycle'  => $cycle,
            // 'message'  => $message,
            'bid_time'  => time()
        );
    $res = DB::insert('project_task_bid',$data);
    if ($res) {
        DB::query('UPDATE '.$table.' SET task_status=22,bid_num=bid_num+1 WHERE tid='.$loid);
        plugin_common::jumpgo('投标成功！',$jumpurl,$jumpext,'success');
    } else {
        plugin_common::jumpgo('投标失败！',$jumpurl,$jumpext);
    }
    break;

case 'clr'://托管
    if (submitcheck('clrsubmit')) {
        // $curcredits; // \source\plugin\_public\common.php
        // $curcredits_per = $curcredits*$loper; // \source\plugin\_public\plugin_common.config.php
        $task_cash = trim(getgpc('task_cash'));
        $task_cash = is_numeric($task_cash)?$task_cash:'';
        if (empty($task_cash))
            plugin_common::jumpgo('请填写正确的金额');
        // 异常抛出 bc高精度计算
        bcscale(2);// 默认精确到2位小数
        $cash_per = bcdiv($task_cash, $loper);
        // $curcredits_per = bcmul($curcredits,$loper);
        try {
            if ($task_cash>0 && bccomp($curcredits_per, $task_cash)==-1) {
                $error = '对不起，您的外包货币不足！,还需要：'. bcsub($task_cash,$curcredits_per);
                throw new Exception($error);
            } else {
                $newcredits = bcsub($curcredits,$cash_per);
                $res = DB::update('common_member_count', array('extcredits2'=>$newcredits), array('uid'=>$gUid));
                if (!$res) {
                    throw new Exception('用户余额变动失败！');
                }
                $data = array(
                        'task_cash'     => $task_cash,
                        'work_time'     => time(),
                        'task_status'   => 41,
                        'is_trust'      => 1,
                    );
                $condition = array('tid'=>$loid);
                $res = DB::update($table_c,$data,$condition);
                if ($res) {
                    plugin_common::jumpgo('托管成功！',$jumpurl,$jumpext,'success');
                } else {
                    throw new Exception('托管失败！');
                }
            }
        } catch (Exception $e) {
            plugin_common::jumpgo($e->getMessage(),$jumpurl,$jumpext);
        }
    }
    // 托管表单页
    $ahr['lm'] = 'pro_'.$c;// 模板位置
    $form['action'] = $jumpurl.'&c=clr';
    break;

case 'bingo'://设为中标
    $data = array('bid_status'=>1);
    $condition = array('bid_id'=>$bid);
    $res = DB::update('project_task_bid',$data,$condition);
    if ($res) {
        DB::update($table_c,array('task_status'=>31),array('tid'=>$loid));
        plugin_common::jumpgo('成功设为中标！',$jumpurl,$jumpext,'success');
    } else {
        plugin_common::jumpgo('设为中标失败！',$jumpurl,$jumpext);
    }
    break;

case 'bidcheck'://确认完成
    $res = DB::update('project_task_bid',array('ext_status'=>2),array('bid_id'=>$bid));
    if ($res) {
        $res = DB::update($table_c,array('task_status'=>8,'finish_time'=>time()),array('tid'=>$loid));
        if ($res) {
            $sUid = DB::result_first("SELECT uid from ".DB::table('project_task_bid')." where bid_id={$bid};");
            $task_cash = DB::result_first("SELECT task_cash from ".$table." where tid={$loid}");
            $res = DB::query("UPDATE ".DB::table('common_member_count')." SET extcredits2=extcredits2+{$task_cash} WHERE uid={$sUid};");
            if ($res) {
                plugin_common::jumpgo('成功验收！',$jumpurl,$jumpext,'success');
            } else {
                plugin_common::jumpgo('验收失败！',$jumpurl,$jumpext);
            }
        }
    }
    break;

default://项目市场详情
    // 初始化变量

    // 数据查询处理
    $fields = plugin_common::create_fields_quote($fields,'',$lokey);
    $sql = sprintf("SELECT %s from %s where tid=%d",$fields,$table,$loid);
    $page = cache_data($sql,'project'.$loid,'fetch_first',60);
    if (empty($page)) plugin_common::jumpgo('数据为空！',$mainurl);
    $page['cycle_time'] = $page['end_time']?$page['end_time']-$page['start_time']:0;
    $page['bid_endtime'] = $page['start_time']-time();
    $page['end_times'] = $page['end_time'] - time();
    // 格式化
    $page['pub_time'] = dgmdate($page['pub_time']);
    $page['start_time'] = dgmdate($page['start_time']);
    $page['bid_endtime'] = UnitChange($page['bid_endtime'],$format_unit);
    $page['end_time'] = dgmdate($page['end_time']);
    $page['end_times'] = UnitChange($page['end_times'],$format_unit);
    $page['cycle_time'] = UnitChange($page['cycle_time'],$format_unit);
    $page['work_time'] = dgmdate($page['work_time']);
    $page['sub_time'] = dgmdate($page['sub_time']);
    $page['finish_time'] = dgmdate($page['finish_time']);
    // $page['sp_end_time'] = dgmdate($page['sp_end_time']);
    // 没有涉及 work 表了
    // $worktime = DB::result_first("SELECT work_time from ".DB::table('project_task_work')." where tid={$loid} ;");
    // $page['work_time'] = dgmdate($worktime);
    $page['attach_url'] = $upload_common_path . LO_ATTACH . $page['task_file'];
    $page['task_file'] = basename($page['task_file']);
    // 投稿 投标 竞标中
    $task_status1 = in_array($page['task_status'],array(2,21,22))?true:false;
    // 选稿 选标中
    $task_status2 = in_array($page['task_status'],array(3,31))?true:false;
    // 投票 工作 待托管
    $task_status3 = in_array($page['task_status'],array(4,41,42))?true:false;
    // 结束
    $task_status4 = in_array($page['task_status'],array(8))?true:false;
    // 结束 失败
    $task_status5 = in_array($page['task_status'],array(8,9))?true:false;
    // 设为中标
    $task_status6 = in_array($page['task_status'],array(2,21,22,3,31))?true:false;
    // 公示 交付中
    // $task_status7 = in_array($page['task_status'],array(5,6))?true:false;
    // 未付款 未审核 冻结 审核失败
    // $task_status8 = in_array($page['task_status'],array(0,1,7,10))?true:false;
    // 仲裁 交付冻结
    // $task_status9 = in_array($page['task_status'],array(11,13))?true:false;

    /*发包人信息*/
    $table1 = DB::table('common_member_profile');
    $table2 = DB::table('common_member');
    $table3 = DB::table('common_member_status');
    $fields1 = 'uid,resideprovince,residecity,residedist,residecommunity';
    $fields2 = 'username,user_type,regdate';
    $fields3 = 'lastvisit';
    $fields1 = plugin_common::create_fields_quote($fields1,'a');
    $fields2 = plugin_common::create_fields_quote($fields2,'b');
    $fields3 = plugin_common::create_fields_quote($fields3,'c');
    $sql = sprintf("SELECT %s,%s,%s from %s as a join %s as b on a.uid=b.uid join %s as c on a.uid=c.uid where a.uid=%d",$fields1,$fields2,$fields3,$table1,$table2,$table3,$page['uid']);
    $sUserInfo = DB::fetch_first($sql);
    $sUserInfo['lastvisit'] = dgmdate($sUserInfo['lastvisit'],'u');
    $sUserInfo['regdate'] = dgmdate($sUserInfo['regdate']);
    $sUserInfo['reside_address'] = $sUserInfo['resideprovince'].' '.$sUserInfo['residecity'].' '.$sUserInfo['residedist'];
    // 手机认证图标
    // $sUserInfo['icon5'] = $_G['setting']['verify'][5]['icon'];
    // $sUserInfo['unicon5'] = $_G['setting']['verify'][5]['unverifyicon'];
    // 发包数量
    $sUserInfo['publishnum'] = DB::result_first("select count(*) from ". $table ." where uid=".$sUserInfo['uid']);
    // 中标次数
    $sUserInfo['bidbingonum'] = DB::result_first("select count(*) from ". DB::table('project_task_bid') ." where bid_status=1 and uid=".$sUserInfo['uid']);

    /*竞标数据*/
    // 当前是否有人中标
    $gBidUid = DB::result_first(sprintf('SELECT uid from %s where bid_status=1 and tid=%d',DB::table('project_task_bid'),$loid));
    // 当前浏览用户的中标情况
    $gWinBid = ($gBidUid==$gUid) ? true : false;
    // 当前浏览用户的投标情况
    $gBid = DB::result_first(sprintf("SELECT bid_id from %s WHERE tid=%d and uid=%d",DB::table('project_task_bid'),$loid,$gUid));
    // 当前任务竞标 总人数 和 时间
    $bids = DB::fetch_first(sprintf("SELECT count(*) as nums,bid_time from %s WHERE tid=%d order by bid_time desc;",DB::table('project_task_bid'),$loid));
    $page['bid_time'] = dgmdate($bids['bid_time']);
    // 竞标列表数据 common_member_profile
    $table1 = DB::table('project_task_bid');
    $table2 = DB::table('common_member');
    $table3 = DB::table('common_member_profile');
    $fields1 = 'bid_id,tid,uid,username,bid_time,bid_status,ext_status';
    // $fields1 .= ',quote,cycle,message,is_view';
    $fields2 = 'user_type';
    $fields3 = 'resideprovince,residecity,residedist,residecommunity';
    $fields1 = plugin_common::create_fields_quote($fields1,'a');
    $fields2 = plugin_common::create_fields_quote($fields2,'b');
    $fields3 = plugin_common::create_fields_quote($fields3,'c');
    // $where = ' where a.tid='.$loid;
    // 如果条件来自一张表，则替换
    // $where2 = str_replace('a.','',$where);
    $jumpurl = $jumpurl . $jumpext;
    $order = '';
    // 数据分页
    $sql = sprintf("SELECT count(*) from %s where tid=%d;",$table1,$loid);
    $multi = plugin_common::pager($sql, $_GET['page'], $jumpurl, $pagesize);
    $multipage = $multi[0];
    // 查询结果集并处理
    $limit = $multi[1];
    $sql = sprintf("SELECT %s,%s,%s from %s as a join %s as b on a.uid=b.uid join %s as c on a.uid=c.uid where a.tid=%d %s %s",$fields1,$fields2,$fields3,$table1,$table2,$table3,$loid,$order,$limit);
    $biders_c = cache_data($sql);
    foreach ($biders_c as $value) {
        $value['reside_address'] = $value['resideprovince'].' '.$value['residecity'].' '.$value['residedist'];
        $biders[] = $value;
    }
    
    /*工作数据*/

    /*留言*/
    $sql = sprintf('SELECT msg_id,msg_type,msg_status,view_status,uid,uname,title,content,addtime,ip from %s where tid=%d',DB::table('project_msg'),$loid);
    $leave_msgs = cache_data($sql);

    /*竞争最激烈的项目*/
    $sql = sprintf('SELECT tid,task_title,bid_num from %s order by bid_num desc limit 10',$table);
    $task_hots = cache_data($sql,'task_hots','fetch_all',60);

    DB::query('UPDATE '.$table.' SET view_num=view_num+1 WHERE tid='.$loid);
    // DB::update($table_c,array('view_num'=>'view_num'+1),array('tid'=>$loid));
    break;
}
// debug($ahr);
?>