<?php 
if(!defined('IN_DISCUZ'))exit('Access Denied');

// $do = getgpc('do');
// $do = $do ? trim($do) : 'list';
$dos = array('base','avatar','collect');
$do = (!empty($_GET['do']) && in_array($_GET['do'], $dos))?$_GET['do']:'base';

$module_url = $mainurl = 'home.php?mod='.$mod;
$jumpurl = $mainurl .'&do='.$do;
$jumpext = '';

$sUid = intval($_REQUEST['uid']);
$uid = $sUid ? $sUid : $gUid;
$mInfo = DB::fetch_first(sprintf('SELECT a.*,b.*,c.* FROM %s a LEFT JOIN %s b ON a.uid=b.uid LEFT JOIN %s c ON a.uid=c.uid WHERE a.uid=%d',DB::table('common_member'),DB::table('common_member_count'),DB::table('common_member_status'),$uid));
// print_r($mInfo);
// require_once libfile('space/'.$do, 'include');


switch ($do) {
    case 'avatar':
        # code...
        break;
    case 'collect':
        # code...
        break;
    default:
        # code...
        break;
}

include template(CURSCRIPT.'/'.$mod.'_'.$do);
// include template(TEMPDIR);
?>