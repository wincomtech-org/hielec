<?php 
session_start();

// 初始化URL
// define('LO_URL', dirname('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']) . "/");
define('LO_URL', $_G['siteurl']);// 域名
// 初始化插件路径
define('LO_PUB_PATH', DISCUZ_ROOT .'source/plugin/_public/');// 公共文件位置
define('LO_PUB_CSS', LO_PUB_PATH.'css/');// 公共样式位置
define('LO_PUB_JS', LO_PUB_PATH.'js/');// 公共JS位置
define('LO_PUB_IMG', LO_PUB_PATH.'image/');// 公共图片位置
define('LO_PUB_ORG', LO_PUB_PATH.'org/');// 第三方工具位置
// 初始化文件上传位置
define('LO_UPLOAD', '/uploads/');// 文件上传位置
define('LO_PIC', 'pic/');// 图片文件位置
define('LO_PDF', 'pdf/');// PDF文件位置
define('LO_ATTACH', 'attach/');// 附件位置
/*
查看时用域名或相对路径 LO_URL . LO_UPLOAD . LO_PIC
储存时用物理路径 DISCUZ_ROOT . LO_UPLOAD . LO_PIC
dirname(DISCUZ_ROOT) 获取路径（去除文件名，尾部不保留“/”）
realpath(LO_URL) 会除去 / ./ ../ 根路径
dirname(LO_URL) . LO_UPLOAD . LO_PIC .$sheet['pic']
realpath(LO_URL) . LO_UPLOAD . LO_PDF .$sheet['pdf']
$upload_common_path = dirname(LO_URL) . LO_UPLOAD . $tpre.'/' . LO_ATTACH .$page['attach'];
$upload_common_path_op = realpath(DISCUZ_ROOT) . LO_UPLOAD . $tpre.'/' . LO_ATTACH .$page['attach'];
*/
// 时间操作 $_G['timestamp']
define('CURTIME', ($_G['timestamp']?$_G['timestamp']:$_SERVER['REQUEST_TIME']));
// 今天的开始和结束
define('STARTTIME', mktime(0,0,0,date('m'),date('d'),date('Y')));
define('ENDTIME', mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1);
// echo date('Y-m-d His',ENDTIME);



/*语言设置*/
$lang['no_resource'] = '暂缺……';
/*导航*/
$mnid = getcurrentnav();// 当前模块标志



/*系统数据*/
### 当前用户资料
$gUid = intval($_G['uid']);
$gUid = $_G['adminid']?intval($_G['adminid']):intval($_G['uid']);
$gUsername = trim($_G['username']);
$gUserInfo = $_G['member'];
### 用户金钱
$creditid = $_G['setting']['creditstrans'];// 当前使用货币ID
$extcredits_title = $_G['setting']['extcredits'][$creditid]['title'];// 货币名
$extcredits_unit = $_G['setting']['extcredits'][$creditid]['unit'];// 货币单位
$credit = $_G['setting']['extcredits'][$creditid];// 货币相关数据数组
$creditname = $credit['title'];// 货币名
$curcredits = $_G['member']['extcredits2']?$_G['member']['extcredits2']:getuserprofile('extcredits'.$creditid);// 货币数额
// $curcredits = $_G['member']['extcredits2'];// 货币数额 有时不存在
### 上次登录
$lastvisit = dgmdate($_G['member']['lastvisit']);
### 用户组 $_G['groupid']
$groupid = $_G['member']['groupid'];
// $grouptitle = $_G['member']['grouptitle'];
### 认证数据
// 实名认证
$sql = sprintf('SELECT verify1,verify2,verify3,verify4,verify5,verify6,verify7 from %s where uid=%d',DB::table('common_member_verify'),$gUid);
$gUserAuth = cache_data($sql,'member_verify'.$gUid,'fetch_first',60);
// $gUserAuth = array(
//         'auth_fabao' => $gUserAuth['verify1'],
//         'auth_jiebao' => $gUserAuth['verify2'],
//         'auth_avatar' => $gUserAuth['verify3'],
//         'auth_company' => $gUserAuth['verify4'],
//         'auth_mobile' => $gUserAuth['verify5'],
//         'auth_realname' => $gUserAuth['verify6'],
//         'auth_video' => $gUserAuth['verify7']
//     );

/*兴趣爱好*/
// $interestall = DB::fetch_first("SELECT `formtype`,`choices` FROM ".DB::table('common_member_profile_setting')." WHERE `fieldid`='interest' ");
// if ($interestall['formtype']=='checkbox') {
//  $interestall = explode("\n", $interestall['choices']);
// }
// print_r($interestall);

/*地区选择*/
$district_prov = cache_data("SELECT `id`,`name` from ".DB::table('common_district')." WHERE level=1",'district_prov');
// $prov_ids='';
// foreach ($district_prov as $key => $value) {
//  if ($key==0) {
//      $prov_ids .= $value['id'];
//  } else {
//      $prov_ids .= ','.$value['id'];
//  }
// }
// $prov_ids = explode(',', $prov_ids);
// print_r($district_prov);
// print_r($prov_ids);



/*
// 根据IP获取用户位置信息
require_once LO_PUB_PATH.'Map.class.php';
// 当前位置信息
$map = new Map();
$getIP = $map->getIP();
echo $getIP='183.160.1.120';
echo "<br>";
if ($getIP=='127.0.0.1') {
    $curaddr = '本地';
} else {
    $ll = $map->locationByIP($getIP);
    if ($ll) {
        // UTF-8转GBK
        $curaddr = mb_convert_encoding($ll['province'],'GBK','UTF-8');
        // $curaddr = $ll['province'];
        
        $GPS = $map->locationByGPS($ll['lng'], $ll['lat']);
        $sql = "SELECT `id` from ".DB::table('common_district')." WHERE name LIKE '%".$curaddr."%' ";
        $location = DB::fetch_first($sql);
    } else {
        $location['id'] = 'null';
    }
}
print_r($GPS);
define('CURIP', $getIP);
define('CURADDR', $curaddr);
*/

// require_once LO_PUB_PATH.'Map.class.php';
if ($_G['clientip']) {
    define('CURIP', $_G['clientip']);
} else {
    if (class_exists('Map')) {
        $map = new Map();
        define('CURIP', $map->getIP());
    } elseif (class_exists('plugin_common')) {
        define('CURIP', plugin_common::getIP());
    } else {
        define('CURIP', '127.0.0.1');
    } 
}



/*共用状态*/
// 状态信息
$lostatus = array('未审核','审核中','审核通过','未过','禁用','暂停','完成','未知');
//审核状态
$arrStatus = array('0' =>"未审核",'1' =>"审核中",'2' =>"审核通过",'3' =>"未过",'4' =>"禁用",'5' =>"暂停",'6' =>"完成");
// 操作类型
$lo_log_type = array(
        array('pre','view','search','zan','cai','col','down','buy','cmt','login','reg','admin'),
        array('预览','访问','搜索','点赞','踩','收藏','下载','购买','评论','登录','注册','管理员')
    );
/*外包项目状态*/
// task_status项目状态：
// $arrProTaskStatus = array(
//         '0'=>'未付款(未托管)',
//         '1'=>'待审核',
//         '2'=>'投稿中(审核通过)','21'=>'投标中p2','22'=>'竞标中d2',
//         '3'=>'任务选稿','31'=>'选标中p3',
//         '4'=>'发起投票','41'=>'工作中p4','42'=>'待托管d4',
//         '5'=>'公示中',
//         '6'=>'交付中(设为中标)',
//         '7'=>'冻结中',
//         '8'=>'结束',
//         '9'=>'失败',
//         '10'=>'审核失败',
//         '11'=>'仲裁中',
//         '13'=>'交付冻结',
//     );
$arrProTaskStatus = array(
        '0'=>'未付款',//一些预先支付的
        '1'=>'待审核',
        '2'=>'审核通过','21'=>'投标中','22'=>'竞标中',//三方
        '3'=>'任务选稿','31'=>'选标',
        '4'=>'发起投票','41'=>'工作中','42'=>'待托管',//三方
        '5'=>'公示中',//完成互评后公开展示
        '6'=>'交付中',//完成工作，等待发包方确认
        '7'=>'冻结中',//任务关闭
        '8'=>'完成',
        '9'=>'失败',
        '11'=>'仲裁中',
        '13'=>'交付冻结',
    );
// is_trust是否托管
$arrProTrustStatus = array('未托管','已托管');
// is_show 是否显示
$arrIsShow = array('否','是');
// 项目类型
$arrProTaskType = array(
        '1' => '普通',
        '2' => '诚意金',
        '3' => '直通车',
    );
// 发布时间
$arrPublishTime = array(
        '1' => '今天',
        '3' => '三天',
        '7' => '一周',
        '30' => '一个月',
        '90' => '三个月',
    );
// 赏金
$arrProTaskCash = array(
        '1'  => 3000,
        '2'  => '3000-10000',
        '3'  => '10000-30000',
        '4'  => '30000-50000',
        '5'  => '50000-100000',
        '6'  => '100000',
    );
// 会员外包类型
$arrProUserType = array(
        '1'  => '个人',
        '2'  => '企业',
        '3'  => '团体',
    );
// 投标状态
$arrBidStatus = array(
        '0' => '竞标中',
        '1' => '已中标',
        '2' => '入围',
        '3' => '淘汰'
    );
$arrBidExtStatus = array(
        '0' => '等待',
        '1' => '发起完成',
        '2' => '确认完成',
        '3' => '失败',
        '5' => '已放弃',
    );
$arrDownType = array(
        'resource_property' => '资源属性',
        'module'    => '模块',
        'software'  => '软件',
        'program_language'  => '编程语言',
        'hot_skill' => '热点技术',
        'use'   => '应用',
        'eletron_basis' => '电子基础',
    );


// 自定义查询
function cache_data($sql, $mkey, $result_type='fetch_all', $cachetime, $index_type=MYSQLI_ASSOC)
{
    // $getconfig = C::memory()->getconfig();
    $getextension = C::memory()->getextension();
    // $mkey 为空时不缓存
    if ($getextension['memcache'] && $mkey) {
        if (strlen($mkey)>32) { $mkey = md5(strrev($mkey)); } else { $mkey = strrev($mkey); }
        if (memory('get',$mkey)===false) {
            // $data = DB::$result_type($sql);// 特殊写法
            switch ($result_type) {
                // case 'fetch_first':$data=call_user_func(array('DB','fetch_first'),$sql);break;
                case 'fetch_first': $data = DB::fetch_first($sql);break;
                case 'fetch_all': $data = DB::fetch_all($sql);break;
                case 'result_first': $data = DB::result_first($sql);break;
                default: $data = DB::query($sql);break;
            }
            if (empty(DB::errno())) {
                if ($cachetime) {
                    memory('set', $mkey, $data, 0, $cachetime);// $cachetime秒失效
                } else {
                    memory('set', $mkey, $data);// 一直有效
                }
            } else {
                plugin_common::jumpgo(DB::error());
            }
        } else {
            $data = memory('get',$mkey);
            if (empty($data)) memory('rm',$mkey);
        }
        // memory('rm',$mkey);
        // C::memory()->clear();
        // C::memory()->close();
    } else {
        // 未开启Memcache缓存时的处理
        // $data = DB::$result_type($sql);// 特殊写法
        switch ($result_type) {
            case 'fetch_first': $data = DB::fetch_first($sql);break;
            case 'fetch_all': $data = DB::fetch_all($sql);break;
            case 'result_first': $data = DB::result_first($sql);break;
            default: $data = DB::query($sql);break;
        }
    }
    return $data;
}

$format_unit = array(array('60','分钟'),array('60','小时'),array('24','天'),array('30','个月'),array('3','个季度'),array('4','年'));
function UnitChange($variable=0,$unit=array())
{
    foreach ($unit as $k => $v) {
        $variable = $variable/$v[0];
        if ($variable > $v[0]) {
            continue;
        } else {
            break;
        }
    }
    $variable = round($variable,2);// 向上取
    $var = explode('.', $variable);
    // return end($var);
    if ($var[1]) {
        $pre1 = intval($unit[$k-1][0]*($var[1]/100));
        $pre2 = $unit[$k-1][1];
        $variable = ($var[0]?$var[0].$v[1]:'') . ($pre1?$pre1.$pre2:'');
    } else {
        $variable .= $v[1];
    }
    return $variable;
}
// $arr = UnitChange(4569775,$format_unit);
?>