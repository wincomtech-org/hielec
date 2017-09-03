<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

$c = $_REQUEST['c']?trim($_REQUEST['c']):'list';
// $cAllow = array('ajax','del','op','page','list');
// if (!in_array($c,$cAllow)) {
//     plugin_common::jumpgo('非法传递！');
// }

$mainurl .= '&pluginop='.$pluginop;
$jumpurl .= '&pluginop='.$pluginop;
$jumpext = '';
$table_c = $tpre .'_category';
$table = DB::table($table_c);
$lokey = 'cid';
$fields = 'pid,level,name,brief,url,pic,addtime,modtime,is_show,sort';
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
        // level 暂时无法处理子类变化
        // $level = plugin_common::get_tid($table_c,$pid);
        // $level = $level[1]+1;
        $pid = 1;

        // 要保存的数据
        $data = array(
                'pid'       => $pid,
                // 'level'     => $level,
                'name'      => $name,
                'is_show'   => $is_show,
                'sort'      => $sort,
                'modtime'   => time(),
            );

        // 数据库操作
        if ($$lokey) {
            $res = DB::update($table_c,$data,array($lokey=>$$lokey));
            $jumpurl .= '&c=page&'.$lokey.'='.$$lokey;
        } else {
            $data['addtime'] = time();
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

    $SEO['title'] = $lang['thead'].'后台管理列表';
    $lang['thead'] = '外包分类';
    $ahr['cur'] = 'cate_page';
    $form['action'] = $mainurl;
    $form['button'] = '提交';
    // $form['sid'] = $_SESSION['sid'] = plugin_common::locrs();

    // 表单验证 前端
    // $Validform['task_title'] = 'datatype="s3-16" errormsg="名称至少3个字符,最多16个字符！"';

    // 编辑页
    // $categorys = plugin_common::get_category($table_c,'cid,pid,name');
    if ($$lokey) {
        $fields = $lokey.','.$fields;
        $fields = plugin_common::create_fields_quote($fields);
        $page = DB::fetch_first("select ". $fields ." from ". $table ." where ".$lokey."=".$$lokey);
    }
    break;

case 'list':
    /*列表页*/
    $SEO['title'] = $lang['thead'].'后台管理列表';
    $lang['thead'] = '外包分类';

    // 预设table表格
    $tab['th'] = array(/*'ID','父ID',*/'名称','发布时间','排序','是否前台显示','操作');
    $tab['td'] = array(/*'cid','pid',*/'name','addtime','sort','is_show');
    $tab['operator'] = array(
        // array($mainurl.'&pluginop=ajax&c=show'.$jumpext.'&'.$lokey.'=','显隐'),
        array($mainurl.'&c=page'.$jumpext.'&'.$lokey.'=', '编辑'),
        array($mainurl.'&c=del'.$jumpext.'&'.$lokey.'=', '删除')
    );

    // 数据查询、处理
    $where = ' WHERE pid=1';
    $order = ' ORDER BY sort';

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
        $v['addtime'] = dgmdate($v['addtime']);
        $v['is_show'] = $arrIsShow[$v['is_show']];
        $list[] = $v;
    }

// debug($list);
     break; 
}
include template( THISPLUG.':'. $oproute . ($ahr['lm']?'_'.$ahr['lm']:'_'.$pluginop) . ($c?'_'.$c:'') );
// include template(THISPLUG.':admin_list');
// include template('_public/admin_common_op');
?>