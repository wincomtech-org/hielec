<?php 
define('APPTYPEID', 21);// 应用标志
define('CURSCRIPT', 'ajax');// 后来又赋给了$_G['basescript']
/*完整初始化*/
require_once './source/class/class_core.php';
$discuz = C::app();
$discuz->init();
require_once './public.php';

$table = $_REQUEST['table']?trim($_REQUEST['table']):showmessage('表名非法！');
$loid = getgpc('loid')?intval(getgpc('loid')):showmessage('ID非法！');
$tag = getgpc('tag')?trim(getgpc('tag')):plugin_common::jumpgo('对象跑哪去了？');
// debug($_POST,1);

switch ($table) {
    case 'project_task':$lokey='tid';break;
    case 'project_task_bid':$lokey='bid_id';break;
    case 'project_msg':$lokey='msg_id';break;
    case 'project_log':$lokey='id';break;
}
$condition = array($lokey=>$loid);

switch ($tag) {
    case 'del':
        $data = array('task_status'=>7);
        $res = DB::update($table,$data,$condition);
        if ($res) {
            echo true;exit;
        } else {
            echo false;exit;
        }
        break;

    case 'deltrue':
        $res = DB::delete($table,$condition);
        // 是否有其他需要删除的？
        if ($res) {
            echo true;exit;
        } else {
            echo false;exit;
        }
        break;

    case 'delall':
        # code...
        break;

    case 'mod':
        # code...
        break;
    
    default:
        echo false;die;
        break;
}
?>