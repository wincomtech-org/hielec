<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

// 初始化
$loid = intval(getgpc('loid'));
// ID验证
if (empty($loid)) plugin_common::jumpgo('非法操作！', LO_CURURL);
// 
$sUsername = cache_data('select username from '.DB::table('common_member').' where uid='.$loid, 'talent_sname'.$loid, 'result_first', 60);
// 不能操作自己
if (in_array($c,array('col','comeon')) && $gUid==$loid) {
    plugin_common::jumpgo('不能操作自己！');
}

/*
 * 分支
*/
switch ($c) {
case 'col'://收藏人
    // 是否已收藏
    $is_col = DB::result_first(sprintf("SELECT id from %s where uid=%d and tid=%d and source='talent' and type='col'",DB::table('project_log'),$gUid,$loid));
    if ($is_col) plugin_common::jumpgo('您已收藏过了！');
    $data = array(
            'type'      => 'col',
            'brief'     => '用户['.$gUid.']'.$gUsername.' 收藏了 用户['.$loid.']'.$sUsername,
            'uid'       => $gUid,
            'tid'       => $loid,
            'source'    => 'talent',
            'addtime'   => time()
        );
    $res = DB::insert('project_log',$data);
    if ($res) plugin_common::jumpgo('收藏成功',$jumpurl,'','success');
    else plugin_common::jumpgo('收藏失败');
    break;

case 'comeon':// 邀请做项目
    // 是否邀请做项目
    $is_comeon = DB::result_first('select msg_id from '.DB::table('project_msg').' where msg_type=4 and uid='.$gUid.' and touid='.$loid.' and title=\'邀请竞标\';');
    if ($is_comeon) plugin_common::jumpgo('您已邀请，请等待！');
    $data = array(
            'msg_type'  => '4',
            'uid'       => $gUid,
            'touid'     => $loid,
            'touname'   => $sUsername,
            'title'     => '邀请竞标',
            'content'   => '用户：【'.$gUid.'】'.$gUsername.' 邀请您【'.$loid.'】'.$sUsername.' 竞标',
            'addtime'   => time()
        );
    $res = DB::insert('project_msg',$data);
    if ($res) {
        plugin_common::jumpgo('邀请成功，请耐心等待对方回应。',$jumpurl,'','success');
    } else {
        plugin_common::jumpgo('邀请失败',$jumpurl);
    }
    break;

default://人才大厅详情
    // 初始化
    // 用户基本信息 介绍
    $where = 'WHERE a.uid='.$loid;
    $sql = sprintf("SELECT %s,%s,%s,%s FROM %s AS a LEFT JOIN %s AS b ON a.uid=b.uid LEFT JOIN %s AS c ON a.uid=c.uid LEFT JOIN %s AS j ON a.groupid=j.groupid %s %s %s",$fields,$fields2,$fields3,$fields10,$table,$table2,$table3,$table10,$where,$order,$limit);
    $page = cache_data($sql,'','fetch_first',30);
    if (empty($page)) plugin_common::jumpgo('数据为空！',$mainurl);
    $page['reside_address'] = $page['resideprovince'].' '.$page['residecity'].' '.$page['residedist'];
    if (empty(trim($page['reside_address']))) {
        $page['reside_address'] = '未知';
    }
    $page['user_type'] = $page['user_type']?$arrProUserType[$page['user_type']]:'不明';
    $page['regdate'] = dgmdate($page['regdate'],'d');
    $page['field5'] = $page['field5']?$page['field5'].'年':'';

    // 项目动态
    // $fields4 = str_replace('d.','',$fields4);
    $sql = sprintf("SELECT %s,%s from %s as d left join %s as e on d.indus_id=e.cid where d.uid=%d limit 8",$fields4,$fields5,$table4,$table5,$loid);
    $tasks = cache_data($sql,'talent_task_'.$loid,'fetch_all',15);
    // 用户案例
    // $fields7 = str_replace('g.','',$fields7);
    $sql = sprintf("SELECT %s,%s from %s as g left join %s as e on g.indus_id=e.cid where g.case_uid=%d %s",$fields7,$fields5,$table7,$table5,$loid,'LIMIT 5');
    $cases = cache_data($sql,'talent_case_'.$loid,'fetch_all',15);
    // 信誉评价
    ###
    // 评价列表
    // $fields8 = str_replace('h.','',$fields8);
    // $sql = sprintf("SELECT %s,%s from %s as h left join %s as b on h.uid=b.uid where h.touid=%d",$fields8,$fields2,$table8,$table2,$loid);
    $sql = "SELECT ".$fields8." FROM ".$table8." WHERE touid=".$loid.' LIMIT 20';
    $comments = cache_data($sql,'talent_comment_'.$loid,'fetch_all',15);
    // 最近访客
    if ($gUid!=$loid) {
        // 今日已访问
        $is_view = DB::result_first(sprintf("SELECT count(*) from %s where uid=%d and touid=%d",DB::table('project_visit'),$gUid,$loid));
        if (empty($is_view)) {
            $data = array(
                    'uid'       => $gUid,
                    'uname'     => $gUsername,
                    'touid'     => $loid,
                    'toname'    => $sUsername,
                    'addtime'   => time(),
                    'ip'        => CURIP
                );
            DB::insert('project_visit',$data);
        }
    }
    $sql = "SELECT ".$fields9." FROM ".$table9." WHERE touid=".$loid.' LIMIT 30';
    // debug($sql,1);
    $visits = cache_data($sql,'talent_visit_'.$loid,'fetch_all',15);



// debug($tasks,1);
// debug($page,1);
    break;
}

// debug($ahr);
?>