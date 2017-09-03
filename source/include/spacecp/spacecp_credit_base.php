<?php

/**
 *      [] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: spacecp_credit_base.php 33663 2013-07-30 05:06:43Z nemohou $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
if(empty($_GET['op']))	$_GET['op'] = 'base';
if(in_array($_GET['op'], array('transfer', 'exchange'))) {
	$taxpercent = sprintf('%1.2f', $_G['setting']['creditstax'] * 100).'%';
}
$cururl = 'home.php?mod=spacecp&ac=credit&op=';

if($_GET['op'] == 'base') {
	$loglist = $extcredits_exchange = array();
	if(!empty($_G['setting']['extcredits'])) {
		foreach($_G['setting']['extcredits'] as $key => $value) {
			if($value['allowexchangein'] || $value['allowexchangeout']) {
				$extcredits_exchange['extcredits'.$key] = array('title' => $value['title'], 'unit' => $value['unit']);
			}
		}
	}
	$count = C::t('common_credit_log')->count_by_uid($_G['uid']);
	if($count) {
		loadcache(array('magics'));
		foreach(C::t('common_credit_log')->fetch_all_by_uid($_G['uid'], 0, 10) as $log) {
			$credits = array();
			$havecredit = false;
			$maxid = $minid = 0;
			foreach($_G['setting']['extcredits'] as $id => $credit) {
				if($log['extcredits'.$id]) {
					$havecredit = true;
					if($log['operation'] == 'RPZ') {
						$credits[] = $credit['title'].lang('spacecp', 'credit_update_reward_clean');
					} else {
						$credits[] = $credit['title'].' <span class="'.($log['extcredits'.$id] > 0 ? 'xi1' : 'xg1').'">'.($log['extcredits'.$id] > 0 ? '+' : '').$log['extcredits'.$id].'</span>';
					}
					if($log['operation'] == 'CEC' && !empty($log['extcredits'.$id])) {
						if($log['extcredits'.$id] > 0) {
							$log['maxid'] = $id;
						} elseif($log['extcredits'.$id] < 0) {
							$log['minid'] = $id;
						}
					}
				}
			}
			if(!$havecredit) {
				continue;
			}
			$log['credit'] = implode('<br/>', $credits);
			if(in_array($log['operation'], array('RTC', 'RAC', 'STC', 'BTC', 'ACC', 'RCT', 'RCA', 'RCB'))) {
				$tids[$log['relatedid']] = $log['relatedid'];
			} elseif(in_array($log['operation'], array('SAC', 'BAC'))) {
				$aids[$log['relatedid']] = $log['relatedid'];
			} elseif(in_array($log['operation'], array('PRC', 'RSC'))) {
				$pids[$log['relatedid']] = $log['relatedid'];
			} elseif(in_array($log['operation'], array('TFR', 'RCV'))) {
				$uids[$log['relatedid']] = $log['relatedid'];
			} elseif($log['operation'] == 'TRC') {
				$taskids[$log['relatedid']] = $log['relatedid'];
			}

			$loglist[] = $log;
		}
		$otherinfo = getotherinfo($aids, $pids, $tids, $taskids, $uids);

	}
	$navtitle = lang('core', 'title_credit');
	$creditsformulaexp = str_replace('*', 'X', $_G['setting']['creditsformulaexp']);

} elseif ($_GET['op'] == 'buy') {
	if((!$_G['setting']['ec_ratio'] || (!$_G['setting']['ec_tenpay_opentrans_chnid'] && !$_G['setting']['ec_tenpay_bargainor']  && !$_G['setting']['ec_account'])) && !$_G['setting']['card']['open'] ) {
		showmessage('action_closed', NULL);
	}

	if(submitcheck('addfundssubmit')) {
		if(!isset($_GET['bank_type'])) {
			showmessage('memcp_credits_addfunds_msg_notype', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
		}
		$apitype = is_numeric($_GET['bank_type']) ? 'tenpay' : $_GET['bank_type'];
		if($apitype == 'card') {
			list($seccodecheck) = seccheck('card');
			if($seccodecheck) {
				if(!check_seccode($_GET['seccodeverify'], $_GET['seccodehash'])) {
					showmessage('submit_seccode_invalid', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
				}
			}

			if(!$_POST['cardid']) {
				showmessage('memcp_credits_card_msg_cardid_incorrect', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
			}
			if(!($card = C::t('common_card')->fetch($_POST['cardid']))) {
				showmessage('memcp_credits_card_msg_card_unfined', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true, 'extrajs' => '<script type="text/javascript">updateseccode("'.$_GET['sechash'].'");</script>'));
			} else {
				if($card['status'] == 2) {
					showmessage('memcp_credits_card_msg_used', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
				}
				if($card['cleardateline'] < TIMESTAMP) {
					showmessage('memcp_credits_card_msg_cleardateline_early', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
				}
				C::t('common_card')->update($card['id'], array('status' => 2, 'uid' => $_G['uid'], 'useddateline' => $_G['timestamp']));
				updatemembercount($_G[uid], array($card['extcreditskey'] => $card['extcreditsval']), true, 'CDC', 1);
				showmessage('memcp_credits_card_msg_succeed', 'home.php?mod=spacecp&ac=credit&op=base', array('extcreditstitle' => $_G['setting']['extcredits'][$card['extcreditskey']]['title'], 'extcreditsval' => $card['extcreditsval']), array('showdialog' => 1, 'alert' => 'right', 'showmsg' => true, 'locationtime' => true));
			}
		} else {
			// showmessage('提示信息：'.$_POST['handlekey'], '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
			$amount = intval($_GET['addfundamount']);// 要支付的钱
			if(!$amount) {
				showmessage('memcp_credits_addfunds_msg_incorrect', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
			}
			$language = lang('forum/misc');
			if(($_G['setting']['ec_mincredits'] && $amount < $_G['setting']['ec_mincredits']) || ($_G['setting']['ec_maxcredits'] && $amount > $_G['setting']['ec_maxcredits'])) {
				showmessage('credits_addfunds_amount_invalid', '', array('ec_maxcredits' => $_G['setting']['ec_maxcredits'], 'ec_mincredits' => $_G['setting']['ec_mincredits']), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
			}

			if($apitype == 'card' && C::t('forum_order')->count_by_search($_G['uid'], null, null, null, null, null, null, $_G['timestamp'] - 180)) {
				showmessage('credits_addfunds_ctrl', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
			}

			if($_G['setting']['ec_maxcreditspermonth']) {
				if(C::t('forum_order')->sum_amount_by_uid_submitdate_status($_G['uid'], $_G['timestamp'] - 2592000, array(2, 3)) + $amount > $_G['setting']['ec_maxcreditspermonth']) {
					showmessage('credits_addfunds_toomuch', '', array('ec_maxcreditspermonth' => $_G['setting']['ec_maxcreditspermonth']), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
				}
			}

			$price = round(($amount / $_G['setting']['ec_ratio'] * 100) / 100, 2);
			$orderid = '';// 订单号

			require_once libfile('function/trade');
			$requesturl = credit_payurl($price, $orderid, $_GET['bank_type']);

			if(C::t('forum_order')->fetch($orderid)) {
				showmessage('credits_addfunds_order_invalid', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
			}

			C::t('forum_order')->insert(array(
				'orderid' => $orderid,
				'status' => '1',
				'uid' => $_G['uid'],
				'amount' => $amount,
				'price' => $price,
				'submitdate' => $_G['timestamp'],
			));

			include template('common/header_ajax');
			echo '<form id="payform" action="'.$requesturl.'" method="post"></form><script type="text/javascript" reload="1">$(\'payform\').submit();</script>';
			include template('common/footer_ajax');
			dexit();
		}
	} else {
		if($_G['setting']['card']['open'] && $_G['setting']['seccodestatus'] & 16) {
			$seccodecheck = 1;
			$secqaacheck = 0;
		}
	}

} elseif ($_GET['op'] == 'withdraw') {

	// 提现
	// debug($_G['setting']['verify']);
	// C::memory()->clear();
	$wd_conf = withdraw_config();
	$wdval = $wd_conf[1];
	$withdraw_type = dunserialize($wdval['withdraw_type']);
	$destarr = array('未审核','已提','失败');
	if (submitcheck('outfundsubmit')) {
		list($seccodecheck) = seccheck('card');// 支付开启验证码
		if($seccodecheck) {
			if(!check_seccode($_GET['seccodeverify'], $_GET['seccodehash'])) {
				showmessage('submit_seccode_invalid', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
			}
		}
		$wdnums = DB::result_first(sprintf('SELECT count(*) from %s WHERE addtime between %d and %d AND uid=%d',DB::table('common_credit_withdraw'),STARTTIME,ENDTIME,$gUid));
		if ($wdnums==$wdval['withdraw_day']) {
			plugin_common::jumpgo('您今日次数已满'. $wdval['withdraw_day'] .'次！',$cururl.'withdraw');
		}
		$data = $_POST['withdraw'];
		$outfundamount = is_numeric($data['outfundamount'])?$data['outfundamount']:intval($data['outfundamount']);
		if ($outfundamount>$curcredits) {
			plugin_common::jumpgo('对不起，提现金额必须小于现有金额！');
		} elseif ($outfundamount<$wdval['withdraw_min']) {
			plugin_common::jumpgo('对不起，提现金额最低为'.$wdval['withdraw_min'].'！');
		} elseif ($outfundamount>$wdval['withdraw_max']) {
			plugin_common::jumpgo('对不起，提现金额最高为'.$wdval['withdraw_max'].'！');
		}
		// 分开算 最终精确到两位小数？可能取整会更合理
		$a = ($outfundamount/$_G['setting']['ec_ratio'])*100;//金钱 与 人民币 并扩大了 100 倍。
		$b = $a*$wdval['withdraw_ratio'];// 手续费比率
		$ratio = round($b)/10000;// 人民币 与 手续费 四舍五入并缩小 10000 倍。
		$out = round($outfundamount) - $ratio;// 实际提现额
		$maps = array(
				'uid'			=> $gUid,
				'uname'			=> $gUsername,
				'credit'		=> $out,
				'ratiofee'		=> $ratio,
				'account_type'	=> $data['account_type'],
				'account'		=> $data['account'],
				'account_name'	=> $data['account_name'],
				'verify_code'	=> $_POST['seccodeverify'],
				'bio'			=> $data['brief'],
				'addtime'		=> time(),
				'ip'			=> CURIP
			);
		$res = DB::insert('common_credit_withdraw',$maps);
		if ($res) {
			plugin_common::jumpgo('提交成功',$cururl.'withdraw&wdsub=log','','success');
		} else {
			plugin_common::jumpgo('提交失败');
		}
	} else {
		if ($_GET['wdsub']=='log') {
			// 条件处理
			$sta = $_REQUEST['sta']?trim($_REQUEST['sta']):'';
			$wh = ' where uid='.$gUid;
			$jumpurl = $cururl .'withdraw&wdsub=log';
			if ($sta) {
				$wh .= ' AND status='.$sta;
				$jumpurl .= '&sta='.$sta;
			}
			$order = 'ORDER BY addtime DESC';
			// 数据分页
			$pagesize = $pagesize ? $pagesize : 10;// 每页记录数
			$sql = "select count(*) from ".DB::table('common_credit_withdraw') . $wh;
			$amount = DB::result_first($sql);
			$pagecount = $amount?(($amount<$pagesize)?1:(($amount%$pagesize)?((int)($amount/$pagesize)+1):($amount/$pagesize))):0;// 计算总页数
			$page = max(1, intval($_GET['page']));
			$page = $page > $pagecount ? 1 : $page;// 取得当前页值
			$startlimit = ($page - 1) * $pagesize;// 查询起始的偏移量
			$multipage = multi($amount, $pagesize, $page, $jumpurl, $pagecount);// 显示分页
			// 数据
			$sql = sprintf('SELECT wid,uname,credit,ratiofee,account_type,account,account_name,ip,addtime,status from %s FORCE INDEX (iua) %s %s %s',DB::table('common_credit_withdraw'),$wh,$order,DB::limit($startlimit,$pagesize));
			$wdlogs = DB::fetch_all($sql);
		} else {
			// 实名认证
			$auth_realname = $gUserAuth['verify6'];
		}
	}

} elseif ($_GET['op'] == 'transfer') {
	if(!($_G['setting']['transferstatus'] && $_G['group']['allowtransfer'])) {
		showmessage('action_closed', NULL);
	}
	if(submitcheck('transfersubmit')) {
		if($_GET['to'] == $_G['username']) {
			showmessage('memcp_credits_transfer_msg_self_incorrect', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
		}
		$amount = intval($_GET['transferamount']);
		if($amount <= 0) {
			showmessage('credits_transaction_amount_invalid', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
		} elseif(getuserprofile('extcredits'.$_G['setting']['creditstransextra'][9]) - $amount < ($minbalance = $_G['setting']['transfermincredits'])) {
			showmessage('credits_transfer_balance_insufficient', '', array('title' => $_G['setting']['extcredits'][$_G['setting']['creditstransextra'][9]]['title'], 'minbalance' => $minbalance), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
		} elseif(!($netamount = floor($amount * (1 - $_G['setting']['creditstax'])))) {
			showmessage('credits_net_amount_iszero', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
		}
		$to = C::t('common_member')->fetch_by_username($_GET['to']);
		if(!$to) {
			showmessage('memcp_credits_transfer_msg_user_incorrect', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
		}

		loaducenter();
		$ucresult = uc_user_login(addslashes($_G['username']), $_GET['password']);
		list($tmp['uid']) = $ucresult;

		if($tmp['uid'] <= 0) {
			showmessage('credits_password_invalid', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
		}

		updatemembercount($_G['uid'], array($_G['setting']['creditstransextra'][9] => -$amount), 1, 'TFR', $to['uid']);
		updatemembercount($to['uid'], array($_G['setting']['creditstransextra'][9] => $netamount), 1, 'RCV', $_G['uid']);

		if(!empty($_GET['transfermessage'])) {
			$transfermessage = dhtmlspecialchars($_GET['transfermessage']);
			notification_add($to['uid'], 'credit', 'transfer', array('credit' => $_G['setting']['extcredits'][$_G['setting']['creditstransextra'][9]]['title'].' '.$netamount.' '.$_G['setting']['extcredits'][$_G['setting']['creditstransextra'][9]]['unit'], 'transfermessage' => $transfermessage));
		}
		showmessage('credits_transfer_succeed', 'home.php?mod=spacecp&ac=credit&op=transfer', array(), array('showdialog' => 1, 'showmsg' => true, 'locationtime' => true));
	}

} elseif ($_GET['op'] == 'exchange') {

	if(!$_G['setting']['exchangestatus']) {
		showmessage('action_closed', NULL);
	}
	$_CACHE['creditsettings'] = array();
	if(file_exists(DISCUZ_ROOT.'/uc_client/data/cache/creditsettings.php')) {
		include_once(DISCUZ_ROOT.'/uc_client/data/cache/creditsettings.php');
	}

	if(submitcheck('exchangesubmit')) {
		$tocredits = $_GET['tocredits'];
		$fromcredits = $_GET['fromcredits'];
		$exchangeamount = $_GET['exchangeamount'];
		$outexange = strexists($tocredits, '|');
		if($outexange && !empty($_GET['outi'])) {
			$fromcredits = $_GET['fromcredits_'.$_GET['outi']];
		}

		if($fromcredits == $tocredits) {
			showmessage('memcp_credits_exchange_msg_num_invalid', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
		}
		if($outexange) {
			$netamount = floor($exchangeamount * $_CACHE['creditsettings'][$tocredits]['ratiosrc'][$fromcredits] / $_CACHE['creditsettings'][$tocredits]['ratiodesc'][$fromcredits]);
		} else {
			if($_G['setting']['extcredits'][$tocredits]['ratio'] < $_G['setting']['extcredits'][$fromcredits]['ratio']) {
				$netamount = ceil($exchangeamount * $_G['setting']['extcredits'][$tocredits]['ratio'] / $_G['setting']['extcredits'][$fromcredits]['ratio'] * (1 + $_G['setting']['creditstax']));
			} else {
				$netamount = floor($exchangeamount * $_G['setting']['extcredits'][$tocredits]['ratio'] / $_G['setting']['extcredits'][$fromcredits]['ratio'] * (1 + $_G['setting']['creditstax']));
			}
		}
		if(!$outexange && !$_G['setting']['extcredits'][$tocredits]['ratio']) {
			showmessage('credits_exchange_invalid', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
		}
		if(!$outexange && !$_G['setting']['extcredits'][$fromcredits]['allowexchangeout']) {
			showmessage('extcredits_disallowexchangeout', '', array('credittitle' => $_G['setting']['extcredits'][$fromcredits]['title']), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
		}
		if(!$outexange && !$_G['setting']['extcredits'][$tocredits]['allowexchangein']) {
			showmessage('extcredits_disallowexchangein', '', array('credittitle' => $_G['setting']['extcredits'][$tocredits]['title']), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
		}
		if(!$netamount) {
			showmessage('memcp_credits_exchange_msg_balance_insufficient', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
		} elseif($exchangeamount <= 0) {
			showmessage('credits_transaction_amount_invalid', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
		} elseif(getuserprofile('extcredits'.$fromcredits) - $netamount < ($minbalance = $_G['setting']['exchangemincredits'])) {
			showmessage('credits_exchange_balance_insufficient', '', array('title' => $_G['setting']['extcredits'][$fromcredits]['title'], 'minbalance' => $minbalance), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
		}

		loaducenter();
		$ucresult = uc_user_login(addslashes($_G['username']), $_GET['password']);
		list($tmp['uid']) = $ucresult;

		if($tmp['uid'] <= 0) {
			showmessage('credits_password_invalid', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
		}

		if(!$outexange) {
			updatemembercount($_G['uid'], array($fromcredits => -$netamount, $tocredits => $exchangeamount), 1, 'CEC', $_G['uid']);
		} else {
			if(!array_key_exists($fromcredits, $_CACHE['creditsettings'][$tocredits]['creditsrc'])) {
				showmessage('extcredits_dataerror', NULL);
			}
			list($toappid, $tocredits) = explode('|', $tocredits);
			$ucresult = uc_credit_exchange_request($_G['uid'], $fromcredits, $tocredits, $toappid, $exchangeamount);
			if(!$ucresult) {
				showmessage('extcredits_dataerror', NULL);
			}
			updatemembercount($_G['uid'], array($fromcredits => -$netamount), 1, 'ECU', $_G['uid']);
			$netamount = $amount;
			$amount = $tocredits = 0;
		}

		showmessage('credits_transaction_succeed', 'home.php?mod=spacecp&ac=credit&op=exchange', array(), array('showdialog' => 1, 'showmsg' => true, 'locationtime' => true));
	}

} else {

	$wheresql = '';
	$list = array();
	$rid = intval($_GET['rid']);
	if($_GET['rid']) {
		$wheresql = " AND rid='$rid'";
	}
	require_once libfile('function/forumlist');
	$select = forumselect(false, 0, $_GET['fid']);
	$keys = array_keys($_G['setting']['extcredits']);
	foreach(C::t('common_credit_rule')->fetch_all_by_rid($rid) as $value) {
		if(!helper_access::check_module('doing') && $value['action'] == 'doing') {
			continue;
		} elseif(!helper_access::check_module('blog') && $value['action'] == 'publishblog') {
			continue;
		} elseif(!helper_access::check_module('wall') && in_array($value['action'], array('guestbook', 'getguestbook'))) {
			continue;
		}
		if(empty($_GET['fid']) || in_array($value['action'], array('digest', 'post', 'reply', 'getattach', 'postattach'))) {
			if(checkvalue($value, $keys)) {
				$list[$value['action']] = $value;
			}
		}
	}
	if(!empty($_GET['fid'])) {
		$_GET['fid'] = intval($_GET['fid']);
		$foruminfo = C::t('forum_forumfield')->fetch($_GET['fid']);
		$flist = dunserialize($foruminfo['creditspolicy']);
		foreach($flist as $action => $value) {
			$list[$value['action']] = $value;
		}
	}
}
include_once template("home/spacecp_credit_base");

function checkvalue($value, $creditids) {
	$havevalue = false;
	foreach($creditids as $key) {
		if($value['extcredits'.$key]) {
			$havevalue = true;
			break;
		}
	}
	return $havevalue;
}
?>