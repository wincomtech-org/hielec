<?php
if(!defined('IN_DISCUZ')) { exit('Access Denied'); }
require_once dirname(__FILE__).'/common.php';

$jk=3;// 用于 类似：6位的：var% => %var% 的模糊搜索
$pluginop = $_REQUEST['pluginop'];

/*
 * 分支
*/
switch ($pluginop) {
case 'ajax':
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
        $TodayCount = DB::result_first(sprintf("SELECT count(*) FROM %s WHERE type='%s' AND uid=%d AND tid=%d AND status=1 AND addtime>%d AND addtime<=%d",DB::table($table),$sign,$uid,$loid,STARTTIME,ENDTIME));
        // 历史操作次数（一次付费终生享用）
        $sql = sprintf("SELECT count(*) FROM %s WHERE type='%s' AND uid=%d AND tid=%d AND status=1",DB::table($table),$sign,$uid,$loid);
        $TistoryTotal = cache_data($sql, 'dt_TistoryTotal_'.$sign.$uid.$loid, 'result_first');
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
                        $error = '对不起，您的'. $brief .'积分不足！,还需要：'. bcsub($price,$curcredits_per) . $brief .'积分';
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
	// include_once LO_PUB_PATH .'ajaxjson.php';
	break;

case 'search':
    // set_time_limit(60);// 超时时间
    // 预设table表格
    $formsearch['action'] = LO_CURURL.'&pluginop=search';
    $formsearch['keyword'] = 'srctxt';
	$tab['th'] = array('型号','参考价格','分类','制造商','销量','发布时间','操作');
	$tab['td'] = array('part','price','cat_name','mfg_name','num_sales','createtime');
	$tab['operator'] = array(
			array(LO_CURURL.'&pluginop=page&loid=', '查看', 'edit')
		);
	if ($tab['ajax']) $tab['operator'] = array_merge($tab['ajax'], $tab['operator']);
    $SEO['title'] = 'Datasheet';
    $SEO = lo_seo($SEO);

	// 数据查询预处理
	$srctxt = $_REQUEST['srctxt']?trim($_REQUEST['srctxt']):'';// 查询字段 没有考虑长字符串带来的慢查询
	$index = $_REQUEST['index']?trim($_REQUEST['index']):'';// 器件索引放这里了^_*
	$cid = $_REQUEST['cid']?intval($_REQUEST['cid']):'';// 器件分类放这里了^_*
	$mid = $_REQUEST['mid']?intval($_REQUEST['mid']):'';// 厂商相关器件放这里了^_*
    $srctype = $_POST['srctype']?intval($_POST['srctype']):1;// 1精确 2模糊
	$lokey = 'id';// 搜索列表用到
	// 数据分页跳转
	$jumpurl = LO_CURURL.'&pluginop=search';
	$jumpurl2 = LO_CURURL.'&pluginop=page';
	// 数据查询处理
	// $strWhere = '';
	$strWhere2 = '';
    if ($index) {
    	// $strWhere .= ' AND `part` like \''.$index.'%\' ';
    	$strWhere2 .= ' AND a.part like \''.$index.'%\' ';
    	$jumpurl .= '&index='.$index;
    }
    if ($cid) {
    	// $strWhere .= ' AND locate(\'"'.$cid.'"\',`cat_id`)>0 ';
    	$strWhere2 .= ' AND a.cat_id=\''.$cid.'\' ';
    	$jumpurl .= '&cid='.$cid;
    } 
    if ($mid) {
    	$strWhere2 .= ' AND a.mfg_id=\''.$mid.'\' ';
    	$jumpurl .= '&mfg_id='.$mid;
    }
    if ($srctxt) {
        $formsearch['keyval'] = $srctxt;
        $jumpurl .= '&srctxt='.$srctxt;
        // $strWhere .= sprintf(' AND (`part`=\'%s%\' OR locate(\'%s\',`part`)>0) ', $srctxt, $srctxt);
        // $isecond = intval($_POST['isecond']);// 是否为相同关键词的二次查询
        if (!in_array($srctype, array(1,2))) {
            $srctxt = cutstr($srctxt,6,'');
        }
        $struct1 = ' AND a.part = \''.$srctxt.'\' ';
        $struct2 = ' AND a.part LIKE \''.$srctxt.'%\' ';
        $struct3 = ' AND a.part LIKE \'%'.$srctxt.'%\' ';
        // 判断所有情况
        if ($srctype==1) {
            $strWhere2 .= $struct1;
        } elseif ($srctype==2) {
            $strWhere2 .= $struct3;
        } else {
            if ($_SESSION['srctxt']==$srctxt && empty($_GET['page'])) {
                # 二次查询 且 page 为空
                $strWhere2 .= $struct3;// 对结果不满意，再搜
                $_SESSION['srctxt_ext'] = '%%';$jk=2;
            } elseif ($_SESSION['srctxt']==$srctxt && !empty($_GET['page'])) {
                # 二次查询 且 page 不为空
                if ($_SESSION['srctxt_ext']=='%%') {
                    $strWhere2 .= $struct3;// 对结果不满意，再搜
                    $jk=2;
                } else {
                    $srctxt = cutstr($srctxt,6,'');
                    $strWhere2 .= $struct2;
                }
            } elseif ($_SESSION['srctxt']!=$srctxt && empty($_GET['page'])) {
                # 首次查询 且 page 为空
                $srctxt = cutstr($srctxt,6,'');
                $strWhere2 .= $struct2;
                unset($_SESSION['srctxt_ext']);
            } elseif ($_SESSION['srctxt']!=$srctxt && !empty($_GET['page'])) {
                # 首次查询 且 page 不为空
                # 告诉你，这丫不存在！
            } else {
                # 呵，何来 else ！
                // $strWhere2 .= $struct1;
            }
        }
        $_SESSION['srctxt'] = $srctxt;
    }
    if (!$srctxt) {
        unset($_SESSION['srctxt']);
    }
    // $fields = 'id,cid,fid,name,subhead,tags,price,brief,url,pic,views,searchs,cols,downs,buys,supports,unsupports,author_id,addtime,modtime,ip';
    // $fields = plugin_common::create_fields_quote($fields,'a');
    $pagesize = $pagesize ? $pagesize : 20;// 每页记录数

    // 待组装SQL
    $sqla = sprintf('SELECT count(*) as cnt from %s.ic_product as a ',$IS_DB3);
    $sqlb = sprintf(
            'INNER JOIN %s.ic_manufacturer_lang as b ON a.mfg_id=b.mfg_id '.
            'INNER JOIN %s.ic_category_lang as c ON a.cat_id=c.cat_id ',
            $IS_DB3,$IS_DB3
        );
    $sqlc = sprintf('WHERE b.lang_id=1 AND c.lang_id=1 %s',$strWhere2);
    $sqld = sprintf('SELECT a.id,a.cat_id,a.part,a.price,a.num_sales,a.createtime,b.mfg_name,c.cat_name from %s.ic_product as a ',$IS_DB3);
    $sqle = 'GROUP BY a.part ORDER BY a.num_sales DESC ';
    // 解决 limit offset很大时引起的慢查询
    // select * from ibmng where id >=(
    //     select id from ibmng order by id limit 1000000,1
    // ) limit 10;
    $mkey = 'dtlist_'.$index.$cid.$mid.$strWhere2;
    // 数据分页
    $sql = $sqla . $sqlb . $sqlc;
    // 5s查询，加上 'RIGHT JOIN %s.ic_product_lang as d ON a.id=d.prod_id '. 和 AND d.lang_id=1 和 ,d.intro 变成 59s
    $amount = cache_data($sql,'count'.$mkey,'result_first');
    $pagecount = $amount?(($amount<$pagesize)?1:(($amount%$pagesize)?((int)($amount/$pagesize)+1):($amount/$pagesize))):0;// 计算总页数
    $page = max(1, intval($_GET['page']));
    $page = $page > $pagecount ? 1 : $page;// 取得当前页值
    $startlimit = ($page - 1) * $pagesize;// 查询起始的偏移量
    $multipage = multi($amount, $pagesize, $page, $jumpurl, $pagecount, 10, false, true, false);// 显示分页
    // $multipage =  multi($num, $perpage, $curpage, $mpurl, $maxpages = 0, $page = 10, $autogoto = FALSE, $simple = FALSE, $jsfunc = FALSE);
    // 查询记录集
    // 秒查，加上 'RIGHT JOIN %s.ic_product_lang as d ON a.id=d.prod_id '. 和 AND d.lang_id=1 和 ,d.intro 仍然秒查
    $sql = $sqld . $sqlb . $sqlc . DB::limit($startlimit,$pagesize);
    $mkey .= $startlimit.$pagesize;
    // $temps = cache_list_table($sql,'','','','',true);
    $temps = cache_data($sql,$mkey);
    foreach ($temps as $row) {
    	if (isset($row['part'])) $row['part'] = '<a href="'. $jumpurl2 .'&loid='. $row['id'] .'">'. $row['part'] .'</a>';
    	if (isset($row['createtime'])) $row['createtime'] = dgmdate($row['createtime']);
    	$arrSearch[] = $row;
    }
	include template('_public/common_search');
	break;

case 'firm':
    // 厂商数据
    // set_time_limit(60);
	// 预设table表格
    $formsearch['action'] = LO_CURURL.'&pluginop=firm';
    $formsearch['keyword'] = 'mfg_name';
	$tab['th'] = array('制造商','简介','主营产品','代理信息','联系方式','操作');
	$tab['td'] = array('mfg_name','mfg_intro','main_prod','agent_info','contact');
	/*$ajax = json_decode('{"col":"收藏","zan":"点赞","cai":"踩"}');
	foreach ((array)$ajax as $k => $v) {
		$tab['ajax'][] = array(LO_CURURL.'&pluginop=ajax&sign='.$k.'&loid=', $v, $k);
	}*/
	$tab['operator'] = array(
			array(LO_CURURL.'&pluginop=search&mid=', '相关器件', 'edit')
		);
	if ($tab['ajax']) $tab['operator'] = array_merge($tab['ajax'], $tab['operator']);
    $SEO['title'] = '制造商';
    $SEO = lo_seo($SEO);

    // 数据查询预处理
	// $loid = $_GET['loid']?intval($_GET['loid']):0;
    $index = $_REQUEST['index']?trim($_REQUEST['index']):'';
	$mfg_name = $_REQUEST['mfg_name']?trim($_REQUEST['mfg_name']):'';
    $srctype = $_POST['srctype']?intval($_POST['srctype']):1;// 1精确 2模糊
	// $table = 'datasheet_firm';
	$lokey = 'mfg_id';// 搜索列表用到
	// 数据分页跳转
	$jumpurl = (function_exists('cpmsg')) ? AURL : LO_CURURL ;
	$jumpurl = $jumpurl . '&pluginop=firm';
	// 数据查询处理
    $wh = '';
    if ($index) {
        $wh .= ' AND b.mfg_name LIKE \''. $index .'%\' ';
        $jumpurl .= '&index='.$index;
    }
	if ($mfg_name) {
        $formsearch['keyval'] = $mfg_name;
        $jumpurl .= '&mfg_name='.$mfg_name;
        // 判断所有情况
        if (!in_array($srctype, array(1,2))) {
            $mfg_name = cutstr($mfg_name,6,'');
        }
        $struct1 = ' AND b.mfg_name = \''. $mfg_name .'\' ';
        $struct2 = ' AND b.mfg_name LIKE \''. $mfg_name .'%\' ';
        $struct3 = ' AND b.mfg_name LIKE \'%'. $mfg_name .'%\' ';
        // 判断所有情况
        if ($srctype==1) {
            $wh .= $struct1;
        } elseif ($srctype==2) {
            $wh .= $struct3;
        } else {
            if ($_SESSION['mfg_name']==$mfg_name && empty($_GET['page'])) {
                # 二次查询 且 page 为空
                $wh .= $struct3;
                $_SESSION['mfg_name_ext'] = '%%';$jk=2;
            } elseif ($_SESSION['mfg_name']==$mfg_name && !empty($_GET['page'])) {
                # 二次查询 且 page 不为空
                if ($_SESSION['mfg_name_ext']=='%%') {
                    $wh .= $struct3;$jk=2;
                } else {
                    $wh .= $struct2;
                }
            } elseif ($_SESSION['mfg_name']!=$mfg_name && empty($_GET['page'])) {
                # 首次查询 且 page 为空
                $wh .= $struct2;
                unset($_SESSION['mfg_name_ext']);
            }  
        }
        $_SESSION['mfg_name'] = $mfg_name;
	} else {
        unset($_SESSION['mfg_name']);
    }
	// 数据分页
	$pagesize = $pagesize ? $pagesize : 20;
    $mkey = 'fmlist_'.$index.$srctype.$mfg_name;
	$sql = sprintf(
			'SELECT count(*) as cnt from %s.ic_manufacturer as a '.
			'INNER JOIN %s.ic_manufacturer_lang as b ON a.id=b.mfg_id '.
			'WHERE b.lang_id=1 %s',
			$IS_DB3,$IS_DB3,$wh
		);
	$amount = cache_data($sql,'count'.$mkey,'result_first');
	$pagecount = $amount?(($amount<$pagesize)?1:(($amount%$pagesize)?((int)($amount/$pagesize)+1):($amount/$pagesize))):0;
	$page = max(1, intval($_GET['page']));
	$page = $page > $pagecount ? 1 : $page;
	$startlimit = ($page - 1) * $pagesize;
	$multipage = multi($amount, $pagesize, $page, $jumpurl, $pagecount);
	// 查询记录集
	$sql = sprintf(
			'SELECT b.mfg_id,b.mfg_name,b.mfg_intro,b.main_prod,b.agent_info,b.contact from %s.ic_manufacturer as a '.
			'INNER JOIN %s.ic_manufacturer_lang as b ON a.id=b.mfg_id '.
			'WHERE b.lang_id=1 %s'.
			// 'GROUP BY a.mfg_id '.
			// 'ORDER BY a.mfg_id DESC '.
			' %s;',
			$IS_DB3,$IS_DB3,$wh,DB::limit($startlimit,$pagesize)
		);
    $mkey .= $startlimit.$pagesize;
    $arrSearch = cache_data($sql,$mkey);
	include template('_public/common_search');
	break;

case 'op':
	plugin_common::common_op($_POST);
	break;

case 'del':
	$wh = array();
	if ($_GET['loid']) {
		$wh['loids'] = $loid = intval($_GET['loid']);
	} elseif ($_POST['loids']) {
		$wh['loids'] = $loid= $_POST['loids'];
	} else {
		cpmsg('非法操作！', '', 'error');
	}
	$oppo = looppo($_REQUEST['sign'], $_REQUEST['table']);
	$table = $oppo['table'];
	$wh['lokey'] = $oppo['lokey'];
	$del_filekey = $oppo['del_filekey'];
	if ($oppo['jumpext']) {
		$jumpext = '&pluginop=commonlist' . $oppo['jumpext'];
	}
	$skip = array(
			'msgok' => '已删除您所选的数据！',
			'urlok' => '',
			'msgno' => '删除失败！',
			'urlno' => '',
			'ext' => $jumpext
		);
	plugin_common::common_taskdel($table, $wh, 'u', $skip, $del_filekey);
	break;

case 'page':
	$loid = $_GET['loid']?intval($_GET['loid']):0;
	$leftmenu = 'page';
    $formsearch['action'] = LO_CURURL.'&pluginop=search';
    $formsearch['keyword'] = 'srctxt';
	$formcheck['url'] = LO_CURURL.'&pluginop=op';
	$head_title = $lang['thead'];
	$table = 'datasheet';
	$lokey = 'id';// 搜索列表用到
	$SEO['title'] = 'Datasheet详情页';
	$SEO = lo_seo($SEO);

	// EXPLAIN 排除语言包？lang_id=1为简体中文
	$sqlpre = sprintf(
		'SELECT a.id,a.mfg_id,a.cat_id,a.part as `name`,a.price,a.img_url as pic,a.pdf_url as pdfhref,a.num_sales as downs,a.createtime as addtime,b.mfg_name,c.cat_name,d.intro,d.`desc` from %s.ic_product as a '.
		'INNER JOIN %s.ic_manufacturer_lang as b ON a.mfg_id=b.mfg_id '.
        'INNER JOIN %s.ic_category_lang as c ON a.cat_id=c.cat_id '.
		'INNER JOIN %s.ic_product_lang as d ON a.id=d.prod_id '.
		'WHERE b.lang_id=1 AND c.lang_id=1 AND d.lang_id=1 ',
		$IS_DB3,$IS_DB3,$IS_DB3,$IS_DB3
	);
	$pagesize = $pagesize ? $pagesize : 20;
	$limit = DB::limit(0,$pagesize);

	// 数据查询、处理
// C::memory()->clear();// 清除缓存
	// $arrTags = '';// 热门标签
	$sql = $sqlpre . 'AND a.id='.$loid;
// debug($sql,1);
	$page = cache_data($sql,'dt_page'.$loid,'fetch_first');
	if (empty($page)) {
		plugin_common::jumpgo('数据源丢失',LO_CURURL);
	}
// debug($page,1);
// $mkey = 'datasheet_page'.$loid;
// if (strlen($mkey)>32) { $mkey = md5(strrev($mkey)); } else { $mkey = strrev($mkey); }
// $dst = memory('get',$mkey);
// debug($dst,1);
    // $page['price'] = $page['price'] ? dnumber($page['price'],2): '0.00';
	$page['price'] = '0.00';
	$page['pdf'] = basename($page['pdfhref'],'.pdf');
    // 获取文件的大小
    // $header_array = get_headers($page['pdfhref'], true);
    $page['pdfsize'] = plugin_common::get_filesize($page['pdfhref']);
	$page['addtime'] = $page['addtime'] ? dgmdate($page['addtime']) : $lang['no_resource'];
	$page['downs'] = rand(0,999);

	// $ur_here = ur_here($_REQUEST['sign'],$page['cid'],$page['name']);
    $ur_here = '<a href="' . LO_CURURL . '">首页</a><b> > </b><span>' . $page['cat_name'] . '</span>';

	$t_cid = intval($page['cat_id']);
	// 相关推荐 按标签？["2","13"]
	// $sql = $sqlpre . sprintf('AND a.id!=%s and a.cat_id=%s %s',$loid,$t_cid,$limit);// 30秒查询
	// $sql = $sqlpre . sprintf('AND a.cat_id=%s order by a.id %s',$t_cid,$limit);// 30秒查询
    $sql = "SELECT id,part as name FROM {$IS_DB3}.ic_product WHERE cat_id={$t_cid} ORDER BY id ASC LIMIT 20;";// 没有聚簇索引13.764秒查询，有索引秒查
	// $sql = "SELECT id,part as name FROM {$IS_DB3}.ic_product WHERE cat_id={$t_cid} LIMIT 20;";// 秒查
	$arrTuijian = cache_list_table($sql,'','','','',true);

	// $arrShare = '';// 该用户分享的
	// 该文件最近下载记录
    $sql = sprintf("SELECT a.uid,a.addtime,a.ip,b.username FROM %s as a LEFT JOIN %s as b ON a.uid=b.uid WHERE a.type='%s' AND a.tid=%d AND a.status=1 GROUP BY a.uid",DB::table($tpre.'_log'),DB::table('common_member'),'down',$loid);
    $arrRecent = cache_data($sql, 'arrRecent'.$loid, 'fetch_all', 60);
	// 热门下载
	$sql = $sqlpre . 'order by a.num_sales desc '.$limit;
	$arrHotDown = cache_list_table($sql,'','','','',true);
	// 最新上传
	$sql = $sqlpre . 'order by a.createtime desc '.$limit;
	$arrNewUp = cache_list_table($sql,'','','','',true);
	// 附加操作
	// DB::update($table,array('views'=>$page['views']+1),array('id'=>$loid));

	include template(THISPLUG.':index_op');
	// include template('_public/common_op');
	break;

default:
// C::memory()->clear();// 清除缓存
	// 初始化变量
	$leftmenu = 'list';
    $formsearch['action'] = LO_CURURL.'&pluginop=search';
    $formsearch['keyword'] = 'srctxt';
	// $formcheck = '';
	$table = 'datasheet';
	$lokey = 'id';// 搜索列表用到
	$SEO['title'] = 'Datasheet';
	$SEO = lo_seo($SEO);
	$ur_here = ur_here($_REQUEST['sign']);

	// 热门搜索
	$sql = sprintf('SELECT id,part as name from %s.ic_product order by id DESC limit 30',$IS_DB3);
	$hot_searchs = cache_list_table($sql,'','','','',true);

	// 热门下载：GROUP BY 会建立临时表,ORDER BY 联表效率低下。都需要建立适当聚簇索引。
	$sql = sprintf(
			'SELECT a.id,a.part as name,a.num_sales,b.mfg_name,c.cat_name from %s.ic_product as a '.
			'INNER JOIN %s.ic_manufacturer_lang as b ON a.mfg_id=b.mfg_id '.
			'INNER JOIN %s.ic_category_lang as c ON a.cat_id=c.cat_id '.
			'WHERE b.lang_id=1 AND c.lang_id=1 '.
			// 'GROUP BY a.part '.
			// 'ORDER BY a.num_sales DESC '.
			'LIMIT 30;',
			$IS_DB3,$IS_DB3,$IS_DB3
		);
// debug($sql);// 打印
	$hot_downs = cache_list_table($sql,'','','','',true);
	$hot_downs = plugin_common::getArrayUniqueByKeys($hot_downs,array('name'));
	$hot_downs = plugin_common::array_sort($hot_downs,'num_sales','desc');

	// 厂商索引
	$firm_indexs_str = '*0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $firm_indexs = str_split($firm_indexs_str);// 这个diao
	// for ($i=0; $i < strlen($firm_indexs_str); $i++) { 
	// 	$firm_indexs[] = $firm_indexs_str[$i];
	// }
	// 器件索引
	$sheet_indexs = $firm_indexs;

// debug($firm_indexs);
	include template(THISPLUG.':index_list');
	break;
}
?>