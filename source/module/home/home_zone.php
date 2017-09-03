<?php
if(!defined('IN_DISCUZ'))exit('Access Denied');

// $allow_do = array('up_post','log','del','up','cols','list');
$do = getgpc('do');
$do = $do ? trim($do) : 'list';

$module_url = $mainurl = 'home.php?mod='.$mod;
$jumpurl = $mainurl .'&do='.$do;
$jumpext = '';

$sUid = intval($_REQUEST['uid']);
$uid = $sUid ? $sUid : $gUid;
$mInfo = DB::fetch_first(sprintf('SELECT a.*,b.*,c.* FROM %s a LEFT JOIN %s b ON a.uid=b.uid LEFT JOIN %s c ON a.uid=c.uid WHERE a.uid=%d',DB::table('common_member'),DB::table('common_member_count'),DB::table('common_member_status'),$uid));
// print_r($mInfo);
// require_once libfile('space/'.$do, 'include');


switch ($do) {
case 'ajax':
    // zone_jsonchange('不能操作自己！');
    if ($sUid==$gUid) { zone_jsonchange('不能操作自己！'); }
    if (empty($sUid)) { zone_jsonchange('操作对象失败'); }
    $type = trim($_POST['type']);
    switch ($type) {
        case 'gz':
            $is_find = DB::result_first(sprintf('SELECT count(*) from %s where uid=%d and followuid=%d',$gUid,$sUid));
            if ($is_find) { zone_jsonchange('您已关注过TA'); }
            // 对方是否关注过你
            $is_xh = DB::result_first(sprintf('SELECT count(*) from %s where uid=%d and followuid=%d',$sUid,$gUid));
            if ($is_xh) { $mutual = 1; }
            $data = array('uid'=>$gUid,'username'=>$gUsername,'followuid'=>$sUid,'fusername'=>$mInfo['username'],'bkname'=>'','status'=>0,'mutual'=>$mutual,'dateline'=>time());
            $InId = DB::insert('home_follow',$data);
            // DB::update('common_member_count',array('following'=>'following'+1),array('uid'=>$gUid));
            DB::query(sprintf('UPDATE %s set following=ifnull(following,0)+1 where uid=%d',DB::table('common_member_count'),$gUid));
            DB::query(sprintf('UPDATE %s set follower=ifnull(follower,0)+1 where uid=%d',DB::table('common_member_count'),$sUid));
            if ($InId) {
                zone_jsonchange('关注成功');
            } else {
                zone_jsonchange('关注失败');
            }
            break;
        case 'friend':
            $is_find = DB::result_first(sprintf('SELECT count(*) from %s where uid=%d and fuid=%d',DB::table('home_friend_request'),$gUid,$sUid));
            if ($is_find) { zone_jsonchange('请勿重复申请加好友'); }
            $data = array('uid'=>$gUid,'fuid'=>$sUid,'fusername'=>$mInfo['username'],'dateline'=>time());
            $InId = DB::insert('home_friend_request',$data);
            // DB::update('common_member_count',array('friends'=>+1),array('uid'=>$gUid));
            if ($InId) {
                zone_jsonchange('申请成功，等待对方回应');
            } else {
                zone_jsonchange('申请失败');
            }
            break;
        case 'mess':
            # code...
            break;
        case 'news':
            # code...
            break;
        default:
            # code...
            break;
    }
    echo $type;
    exit;
    break;

case 'dynamic':
    $SEO['title'] = '动态';
    break;

case 'record':
    $SEO['title'] = '记录';
    break;

case 'photos':
    $SEO['title'] = '相册';
    break;

case 'theme':
    $SEO['title'] = '主题';
    break;

case 'board_mess':
    $SEO['title'] = '留言板';
    break;

case 'person_data':
    $SEO['title'] = '个人资料';
    break;

case 'friends':
    $SEO['title'] = '好友';
    $flist = DB::fetch_all(sprintf('select * from %s where uid=%d',DB::table('home_friend'),$uid));
    break;
    
default:
    $SEO['title'] = '个人空间主页';
    break;
}

include_once template(TEMPDIR);



function zone_jsonchange($m='非法！',$s='error')
{
    echo json_encode(array('s'=>$s,'m'=>$m));exit;
}
?>