<?php
header('Content-Type:text/html;charset=UTF-8');//设置字符集

require_once './source/plugin/_public/common.php';
require_once LO_PUB_PATH.'plugin_common.function.php';
require_once LO_PUB_PATH.'plugin_common.class.php';
require_once LO_PUB_PATH.'public_uc_menu.php';



// $Iselfurl = $_SERVER['HTTP_HOST'];// hielec.wincomtech.cn
// $Iselfurl = $_SERVER['SERVER_NAME'];// hielec.wincomtech.cn
// $Iselfurl = $_SERVER['PHP_SELF'];// /home.php
// $Iselfurl = $_SERVER['REQUEST_URI'];// home.php?mod=spacecp&ac=credit&op=buy
// $Iselfurl = $_SERVER['HTTP_REFERER'];// http://hielec.wincomtech.cn/home.php?mod=spacecp&ac=credit&op=buy
// $Iselfurl = $_G['PHP_SELF'];
// echo $Iselfurl;



// echo "<pre>";
// print_r($_G);
// print_r($_G['setting']);
// print_r($_G['setting']['site_qq']);
// print_r($_G['config']);
// print_r($_G['config']['db'][1]);
// print_r($_G['config']['memory']['memcache']);
// print_r($gUserInfo);
// echo FORMHASH;
// echo formhash();
// echo TIMESTAMP;
// echo $_G['basescript'];
// echo $_G['basefilename'];
// echo '<br>';
// echo DISCUZ_ROOT;// 物理路径
// echo '<br>';
// echo dirname(__FILE__);
// print_r($_G['siteroot']);
// print_r($_G['PHP_SELF']);
// echo LO_URL;// 域名
// echo $boardurl;
// print_r($_GET['id']);
// print_r($_REQUEST);
// print_r($_SERVER);

// $mnid = getcurrentnav();
// echo $_G['mnid'];
// echo $mnid;
// echo "<br><br>navmns";
// print_r($_G['setting']['navmns']);
// echo "<br><br>navdms";
// print_r($_G['setting']['navdms']);
// echo "<br><br>navmn";
// print_r($_G['setting']['navmn']);
// echo "<br><br>navs";
// print_r($_G['setting']['navs']);
// echo "<br><br>topnavs";
// print_r($_G['setting']['topnavs']);
// echo "<br><br>menunavs";
// print_r($_G[setting][menunavs]);
// echo "</pre>";
// die;
// if($mnid==$nav[navid]){echo 'active';} elseif(stripos($nav[filename], $_G['basefilename'])){echo 'active';}
// <!--{eval if($mnid==$nav[navid]){echo 'active';} elseif(stripos($nav[filename],$_G['basefilename'])){echo 'active';}}-->
// <!--{eval if (stripos($nav['filename'],$_G['basefilename'])) {}-->active<!--{eval }}-->

// echo $_G['setting']['jspath'];
// echo $_G['setting']['csspath'];
// echo STYLEID;
// echo VERHASH;
// echo $_G['basefilename'];
// DB::query("insert ".DB::table('common_member')." (username,email) values ('test','tset@qq.com') ");
// DB::query("update ".DB::table('common_member')." set status=status+1 where username='test'");
// DB::query("delete from ".DB::table('common_member')." where username='test'");
// echo DISCUZ_ROOT;
// echo "<br>";
// echo dirname(__FILE__);
// echo $_G['setting']['creditstrans'];



      // $price = 1;
      // $orderid = '';// 订单号

      // require_once libfile('function/trade');
      // $requesturl = credit_payurl($price, $orderid, $_GET['bank_type']);

      // if(C::t('forum_order')->fetch($orderid)) {
      //  showmessage('credits_addfunds_order_invalid', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
      // }

      // C::t('forum_order')->insert(array(
      //  'orderid' => $orderid,
      //  'status' => '1',
      //  'uid' => $_G['uid'],
      //  'amount' => $amount,
      //  'price' => $price,
      //  'submitdate' => $_G['timestamp'],
      // ));

      // include template('common/header_ajax');
      // echo '<form id="payform" action="'.$requesturl.'" method="post"></form><script type="text/javascript" reload="1">$(\'payform\').submit();</script>';
      // include template('common/footer_ajax');
      // dexit();





?>