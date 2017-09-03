<?php 
    if(!defined('IN_DISCUZ')) { exit('Access Denied'); }

    $loid = $_REQUEST['loid'] ? intval($_REQUEST['loid']) : '';
    $uid = $_G['uid'] ? intval($_G['uid']) : '';
    $sign = $_REQUEST['sign'] ? trim($_REQUEST['sign']) : '';// 标志
    $price = $_REQUEST['price'] ? trim($_REQUEST['price']) : '0.00';// 单价
    $vars = $_REQUEST['vars'] ? intval($_REQUEST['vars']) : 0;// 历史操作总数
    $href = $_REQUEST['href'] ? trim($_REQUEST['href']) : '';// 附件下载链接

    if ($loid && $uid) {
        $oppo = looppo($sign);
        $table = $oppo['table'];
        $lokey = $oppo['lokey'];
        // 今日操作次数（适合每次下载都需要收费的情况）
        $TodayCount = DB::result_first(sprintf("SELECT count(*) FROM %s WHERE type='%s' AND uid=%d AND tid=%d AND status=%d AND addtime>%d AND addtime<=%d",DB::table($table),$sign,$uid,$loid,1,STARTTIME,ENDTIME));
        // 历史操作次数（一次付费终生享用）
        $TistoryTotal = DB::result_first(sprintf("SELECT count(*) FROM %s WHERE type='%s' AND uid=%d AND tid=%d AND status=%d",DB::table($table),$sign,$uid,$loid,1));
        // $curcredits = DB::result_first("select extcredits2 from ".DB::table('common_member_count')." where uid=$uid ");
        $curcredits = $_G['member']['extcredits2'];
        $curcredits_per = $curcredits*$loper;
        // print_r(array($loid,$uid,$sign,$price,$vars));
        $sta = 'error';$cos = 0;
        switch ($sign) {
            case 'col':
                $brief = '收藏';
                if (empty($uid)) {
                    lo_msg( '请登录后操作', $sta, $sign, 'member.php?mod=logging&action=login' );
                }
                if ($TistoryTotal) {
                    lo_msg( '您已'.$brief.'过', $sta, $sign, $href );
                }
                DB::update($tpre,array('cols'=>$vars+1),array('id'=>$loid));
                break;
            case 'zan':
                $brief = '点赞';
                if ($TistoryTotal) {
                    lo_msg( '您已'.$brief.'过', $sta, $sign, $href );
                }
                if ($TodayCount>99) {
                    $error = '今日'. $brief . $TodayCount .'次';
                }
                DB::update($tpre,array('supports'=>$vars+1),array('id'=>$loid));
                break;
            case 'cai':
                $brief = '踩';
                if ($TistoryTotal) {
                    lo_msg( '您已'.$brief.'过', $sta, $sign, $href );
                }
                DB::update($tpre,array('unsupports'=>$vars+1),array('id'=>$loid));
                break;
            case 'down':
                $brief = '下载';
                if ($TistoryTotal) {
                    lo_msg( '您已'.$brief.'过', $sta, $sign, $href );
                }
                if ($TodayCount>5) {
                    lo_msg( '今日'. $brief.$TodayCount .'次', $sta, $sign );
                }
                // 异常抛出 bc高精度计算
                bcscale(2);// 默认精确到2位小数
                $price_per = bcdiv($price, $loper);
                // $curcredits_per = bcmul($curcredits,$loper);
                try {
                    if ($price>0 && bccomp($curcredits_per, $price)==-1) {
                        $error = '对不起，您的'. $brief .'积分不足！,还需要：'. ($price-$curcredits_per) . $brief .'积分';
                        $sta = 'charge';
                        throw new Exception($error);
                    } else {
                        $curcredits = bcsub($curcredits,$price_per);
                        $cos = $price;
                        $resnum = DB::update('common_member_count', array('extcredits2'=>$curcredits), array('uid'=>$uid));
                        if (!$resnum) {
                            $error = '用户余额变动失败！';
                            throw new Exception($error);
                        }
                        DB::update($tpre,array('downs'=>$vars+1),array('id'=>$loid));
                    }
                } catch (Exception $e) {
                    lo_msg( $e->getMessage(), $sta, $sign );
                }
                break;
        }
        // 添加操作记录
        if ($brief) {
            /*if ($total && (empty($cos) || $cos==$price)) {
                $total = $total+1;
                $resnum = DB::update($table, array('total'=>$total,'modtime'=>CURTIME),array('id'=>$logid));
            } else {
                $resnum = plugin_common::common_log($table, $sign, $brief, $uid, $loid, $price);
            }*/
            $data = array(
                'type' => $sign,
                'brief' => $brief,
                'uid' => $uid,
                'tid' => $loid,
                'cos' => $cos,
                'status' => 1
                );
            $resnum = plugin_common::common_log($table, $data);
            if ($sign=='down') {
                if ($resnum && $href) {
                    lo_msg( '您已'.$brief.'过', $sta, $sign, $href );
                } else {
                    lo_msg( $brief.'失败！', 'error', $sign, $href );
                }
            } else {
                if ($resnum) lo_msg( $brief.'成功！', 'success', $sign, $href );
                else lo_msg($brief.'失败！', 'error', $sign, $href);
            }
        } else { 
            lo_msg('没事瞎点啥！', 'error', $sign, $href);
        }
    } else {
        plugin_common::jumpgo('数据已过期或关闭或非法');
    }

?>