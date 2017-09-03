<?php 
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}
// require_once './source/plugin/_public/plugin_common.class.php';
// C::memory()->clear();
// $type = array(1=>'支付宝',2=>'微信',3=>'银行卡',4=>'QQ',5=>'话费');
// $type = serialize($type);
// $type = dunserialize($type);

$_G['action_lock'] = $action;
cpheader();
$operation = $operation ? $operation : 'list';
$withdraw_url = ADMINSCRIPT.'?action=withdraw&operation=';
$Orz = 'action=withdraw&operation=';

$wd_conf = withdraw_config();
$wdval = $wd_conf[1];
$withdraw_type = dunserialize($wdval['withdraw_type']);
$destarr = array('未处理','已提','失败');
// debug($withdraw_type);
switch ($operation) {
case 'list':
    $wh = ' where 1=1 ';$jumpurl = $withdraw_url;
    $srcselkey = $_REQUEST['srcselkey']?trim($_REQUEST['srcselkey']):'';
    $srcselval = $_REQUEST['srcselval']?trim($_REQUEST['srcselval']):'';
    $srcstatus = is_numeric($_REQUEST['srcstatus'])?$_REQUEST['srcstatus']:-1;
    $srctype = is_numeric($_REQUEST['srctype'])?$_REQUEST['srctype']:-1;
// debug($srcselkey.'A'.$srcselval.'A'.$srcstatus.'A'.$srctype);
    if ($srcselkey&&$srcselval) {
        $wh .= ' AND '. $srcselkey .'=\''. $srcselval .'\'';
        $jumpurl .= '&'. $srcselkey .'='. $srcselval;
    }
    if ($srcstatus && $srcstatus>-1) {
        $wh .= ' AND status='. $srcstatus;
        $jumpurl .= '&srcstatus='. $srcstatus;
    }
    if ($srctype && $srctype>-1) {
        $wh .= ' AND account_type='. $srctype;
        $jumpurl .= '&srctype='. $srctype;
    }
    $order = ' ORDER BY addtime DESC ';
    // 数据分页
    $pagesize = $pagesize ? $pagesize : 20;// 每页记录数
    $sql = "select count(*) from ".DB::table('common_credit_withdraw') . $wh;
    $amount = DB::result_first($sql);
    $pagecount = $amount?(($amount<$pagesize)?1:(($amount%$pagesize)?((int)($amount/$pagesize)+1):($amount/$pagesize))):0;// 计算总页数
    $page = max(1, intval($_GET['page']));
    $page = $page > $pagecount ? 1 : $page;// 取得当前页值
    $startlimit = ($page - 1) * $pagesize;// 查询起始的偏移量
    $multipage = multi($amount, $pagesize, $page, $jumpurl, $pagecount);// 显示分页
    // 数据
    $fields = 'wid,uid,uname,credit,ratiofee,account_type,account,account_name,addtime,ip,status';
    $infos = DB::fetch_all('SELECT '. $fields .' from '. DB::table('common_credit_withdraw') . $wh . $order . DB::limit($startlimit,$pagesize));
    break;

case 'del':
    if (submitcheck('deletesubmit')) {
        $arrWid = $_POST['widarray'];
        if (empty($arrWid)) {
            cpmsg_error('请选中要操作的项。');
        }
        if (count($arrWid)==1) {
            $arrWid = $arrWid[0];
        }
    } else {
        $arrWid = intval($_GET['wid']);
    }
    if (empty($arrWid)) {
        cpmsg_error('非法请求！');
    }
    if (is_array($arrWid)) {
        $arrWid = implode(',',$arrWid);
        $sql = sprintf('DELETE %s WHERE wid IN (%s)',DB::table('common_credit_withdraw'),$arrWid);
        $res = DB::query($sql);
    } else {
        $res = DB::delete('common_credit_withdraw',array('wid'=>$arrWid));
    }
    if ($res) {
        cpmsg('删除成功', $Orz);
    } else {
        cpmsg_error('删除失败', $Orz);
    }
    exit();
    break;

case 'edit':
    $wid = $_GET['wid'] ? intval($_GET['wid']) : 0;
    if (empty($wid)) {
        cpmsg_error('对不起，数据ID丢失');
    }
    if (submitcheck('editsubmit')) {
        $data = $_POST['cash'];
        $where = array('wid'=>$wid);
        $res = DB::update('common_credit_withdraw',$data,$where);
        if ($res) {
            cpmsg('修改成功', $Orz.'edit&wid='.$wid);
        } else {
            cpmsg_error('修改失败', $Orz.'edit&wid='.$wid);
        }
    }
    $fields = 'wid,uid,uname,credit,ratiofee,account_type,account,account_name,bio,ip,addtime,status';
    $page = DB::fetch_first(sprintf('SELECT %s from %s where wid=%d',$fields,DB::table('common_credit_withdraw'),$wid));
    break;

case 'truck':
    // 安全过滤
    // checkformulaperm();
    // dhtmlspecialchars();
    $sta = $_GET['sta']?trim($_GET['sta']):'';
    $wid = $_GET['wid'] ? intval($_GET['wid']) : 0;
    if (empty($wid)) {
        cpmsg_error('对不起，数据ID丢失');
    }
    $wdinfo = DB::fetch_first(sprintf('SELECT uid,credit from %s where wid=%d',DB::table('common_credit_withdraw'),$wid));
    $wdinfo or cpmsg_error('数据源出错！', $Orz);
    $where = array('wid'=>$wid);
    if ($sta=='yes') {
        // 回滚事务 mysqli_rollback($con); 不支持MyISAM引擎
        try {
            $res = DB::update('common_credit_withdraw',array('status'=>1),$where);
            /** 效率低
            $curcredits = DB::result_first("SELECT extcredits2 FROM ".DB::table('common_member_count')." WHERE uid=".$wdinfo['uid']);
            $curcredits += $wdinfo['credit'];
            $res2 = DB::update('common_member_count',array('extcredits2'=>$curcredits),array('uid'=>$wdinfo['uid']));
            */
            if ($res) { 
                $res2 = DB::query("UPDATE ".DB::table('common_member_count')." set extcredits2=extcredits2+".$wdinfo['credit']." where uid=".$wdinfo['uid']);
                if (!$res2) { DB::update('common_credit_withdraw',array('status'=>0),$where); throw new Exception('提现失败！'); }
            } else {
                throw new Exception('提现状态修改失败！');
            }
        } catch (Exception $e) {
            cpmsg_error($e->getMessage(), $Orz);
        }
    } elseif ($sta=='no') {
        $res = DB::update('common_credit_withdraw',array('status'=>2),$where);
    }
    if ($res) {
        cpmsg('修改成功', $Orz);
    } else {
        cpmsg_error('修改失败', $Orz);
    }
    break;

case 'blacklist':
    
    break;

case 'config':
    $wdkey = $wd_conf[0];
    // $wdlang = '最低提现额度,最高提现额度,扣除手续费,备注';
    if (submitcheck('editsubmit')) {
        $sql = "UPDATE ". DB::table('common_setting') ." SET svalue = CASE skey ";
        foreach ($_POST as $k => $v) {
            if (!in_array($k,$wdkey)) {
                unset($v);
            } else {
                $sql .= sprintf("WHEN '%s' THEN '%s' ", $k, trim($v));
            }
        }
        $skey = $wd_conf[3];
        $sql .= "END WHERE skey IN ($skey);";
        $res = DB::query($sql);
        if ($res) {
            $mkey = strrev('withdraw_config');
            memory('rm',$mkey);
            cpmsg('修改成功');
        } else {
            cpmsg_error('修改失败');
        }
    }
    break;
}

require $admincp->admincpfile($action,$operation);
cpfooter();
?>