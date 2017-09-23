<?php
/*
 *
 * 统一函数
 * 状态：0未审核 1审核中 2审核通过 3未过 4禁用 5暂停 6完成
 * 操作类型：pre预览 view访问 search搜索 zan点赞 cai踩 col收藏 down下载 buy购买 cmt评论 login登录 reg注册 admin管理员
*/
// 头预处理
function looppo($sign='', $dtable='', $dkey=null, $dfield='', $dnav='', $delkey='') {
	$sign = trim($sign);
	$dtable = trim($dtable);
	global $tpre,$lang;
	switch ($sign) {
		case 'cg':
			$table = '_category';// 当前模块表
			$lokey = 'cid';// 表主键名
			$fields = 'pid,name,brief,url,pic,is_show,sort';// 待查询字段
			$head = '分类';// 当前模块名
			$del_filekey = 'pic';
			break;
		case 'firm':
			$table = '_firm'; $lokey = 'cid'; $head = '厂商';$del_filekey = 'pic';
			$fields = 'name,brief,url,pic,is_show,sort';
			break;
		case 'article':
			$table = '_article'; $lokey = 'id'; $head = '文章';$del_filekey = 'pic';
			$fields = 'name,subhead,tags,brief,details,url,pic,views,searchs,cols,supports,unsupports,status,is_show,sort';
			break;
		case 'gift':
			$table = '_gift'; $lokey = 'gid'; $head = '礼品';$del_filekey = 'pic,attach';
			$fields = 'name,subhead,index,tags,price,brief,details,url,pic,views,searchs,cols,buys,supports,unsupports,status,is_show,sort';
			break;
		case 'log':
			$table = '_log'; $lokey = 'id'; $head = '记录';$del_filekey = '';
			$fields = 'name,subhead,index,tags,price,brief,details,url,pic,views,searchs,cols,buys,supports,unsupports,status,is_show,sort';
			break;
		case 'view':
		case 'search':
		case 'col':
		case 'down':
		case 'zan':
		case 'cai':
			$table = '_log'; $lokey = 'id'; $head = '记录';$del_filekey = '';
			$fields = 'type,brief,exp,credits,cos,uid,tid,status,ip,addtime';
			break;
		default:
			$table = ''; $lokey = 'id'; $head = $lang['thead']; $fields = '*';$del_filekey = 'pic,pdf,attach';
			break;
	}
	$table = $dtable ? $dtable : $tpre.$table;
	$lokey = $dkey ? $dkey : $lokey;
	$fields = $dfield ? $dfield : $fields;
	$head_title = $dnav ? $dnav : $head;
	$del_filekey = $delkey ? $delkey : $del_filekey;
	$jumpext = $sign ? '&sign=' . $sign : '';
	return array(
		'table'			=> $table,
		'lokey'			=> $lokey,
		'fields'		=> $fields,
		'head_title'	=> $head_title,
		'del_filekey'	=> $del_filekey,
		'jumpext'		=> $jumpext
	);
}

/*查询数据缓存处理 6个设置项*/
function cache_list_table($table, $fields='*', $condition, $order, $limit='9', $IS_SQL=false)
{
	if ($IS_SQL) {
		if (is_bool($IS_SQL)) {
			$mkey = $sql = $table;
		} else {
			$sql = $table;$mkey = $IS_SQL;
		}
	} else {
		if (empty($table)) plugin_common::jumpgo('表名MISS');
		if (is_null($fields)) $fields = 'id,name,pic,brief';
		// 系统采用一个软删除 recycle
		if ($condition===null) $condition = 'recycle=\'\' and is_show=1 and (status=2||status=6)';
		$condition = $condition ? ' WHERE ' . $condition : '';
		$order = $order ? ' ORDER BY ' . $order : '';
		$limit = $limit ? ' LIMIT ' . $limit : '';
		$sql = 'SELECT '. $fields .' FROM '. DB::table($table) . $condition . $order . $limit;
		$mkey = $table . $fields . $condition . $order . $limit;
	}
	// $getconfig = C::memory()->getconfig();
	$getextension = C::memory()->getextension();
	if ($getextension['memcache']) {
		// $mkey = md5(strrev($mkey));
		if (strlen($mkey)>32) {
			// $mkey = substr($mkey,0,200);
			$mkey = md5(strrev($mkey));
		} else {
			$mkey = strrev($mkey);
		}
		if (memory('get',$mkey)===false || memory('get',$mkey)===null) {
			$data = DB::fetch_all($sql);
			if (empty(DB::errno())) {
				memory('set', $mkey, $data);// 一直有效
				// memory('set', $mkey, $data, 0, 60);// 60秒失效
			} else {
				plugin_common::jumpgo(DB::error());
			}
		} else {
			$data = memory('get',$mkey);
		}
		// memory('rm',$mkey);
		// C::memory()->clear();
		// C::memory()->close();
	} else {
		// 未开启Memcache缓存时的处理
		/*// 设置一个更新时间（重新生成条件）（待完善）
		// $cache_dir = '../_public/cache';
		$cache_dir = DISCUZ_ROOT.'./source/plugin/_public/cache/table';
		$cache_file = '/'.$table.'.txt';
		plugin_common::dir_status($cache_dir,true);
		$data = file_get_contents($cache_dir.$cache_file);
		if (empty($data)) {
			$data = DB::fetch_all($sql);
			$arr = serialize($data);
			// $json = json_encode($data);
			$byte = file_put_contents($cache_dir.$cache_file, $arr);// 返回写入的字节数
		} else {
			$data = unserialize($data);
			// $data = json_decode($data);// 需要把对象转成数组
		}*/

		// session_ob($table, $fields, $wol, $sign);
		$data = DB::fetch_all($sql);
	}
	return $data;
}

/*数据存Session，减少数据库操作，待完善*/
function session_ob($table='', $fields='*', $wol='', $sign='op')
{
	global $_G;
	$fields = $fields ? $fields : '*';
	if ($sign == 'column') {
		$skey = $table . '_attr';
		// 指定了库名table_schema 和 表名table_name
		$sql = "SELECT column_name as field,data_type as type,COLUMN_DEFAULT as def,column_comment as comment FROM information_schema.COLUMNS WHERE table_schema = '".$_G['config']['db'][1]['dbname']."' AND table_name='". DB::table($table) ."'";
	} elseif ($sign=='op') {
		$skey = $table . '_infos';
		$sql = "SELECT * FROM " . DB::table($table) . ' ' . $wol . ' ';
	}
	if ($sql) {
		$infos_all = $_SESSION[$skey]['infos_all'] ? $_SESSION[$skey]['infos_all'] : DB::fetch_all($sql);
	}

	# insert or del or update or select
	if ($sign == 'del') {
		session_unset();session_destroy();
		// unset($_SESSION);// 局部销毁,非全局
		// session_unset();// 清空session值
		// session_destroy();// 彻底销毁session
	} elseif ($fields=='*' && ($_SESSION[$skey]['wol']==$wol)) {
		$infos = $_SESSION[$skey]['infos_all'] = $infos_all;
	} elseif (($_SESSION[$skey]['fields']==$fields) && ($_SESSION[$skey]['wol']==$wol)) {
		$infos = $_SESSION[$skey]['infos'];
	} else {
		foreach ($infos_all as $k => $v) {
			if ($sign=='column') {
				if (preg_match('/'.$v['field'].'/', $fields)) {
					$infos[] = $v;
				}
			} else {
				$ak = array_keys($v);
				foreach ($ak as $val) {
					if (stripos($fields, $val)!==false) {
						$infos[$k][$val] = $v[$val];
					}
				}
			}
		}
		$_SESSION[$skey] = array(
				'table'	=> $table,
				'fields'=> $fields,
				'wol'	=> $wol,
				'infos'	=> $infos,
				'infos_all'	=> $infos_all
			);
	}
	// 过期事件？
	// return $_SESSION[$skey];
	return $infos;
}
// echo "<pre>";
// print_r(DB::fetch_all("SELECT column_name as field,data_type as type,COLUMN_DEFAULT as def,column_comment as comment FROM information_schema.COLUMNS WHERE table_name='pre_datasheet_firm'"));die;
// $cates = session_ob('download', 'cid,name,author_id', '', 'column');
// $cates = session_ob('download', '*', '', 'column');
// session_ob('', '', '', 'del');
// $cates = session_ob('datasheet_category', 'cid,name,brief', 'order by sort');
// print_r($cates);
// print_r($_SESSION);
// die;

/**
 * 文件上传
 * 没写完哦！请使用 plugin_common::upload_file()
*/
function opupload($dir='',$newname='',$max='2048',$type='jpg,png,gif,bmp,swf')
{
	if ($_FILES) {
		$upload = new discuz_upload();
		foreach ($_FILES as $key => $file) {
			$upload->init($file, 'project');
			$attach = $upload->attach;
			if (!$upload->error()) {
				$upload->save();
                if(!$upload->get_image_info($attach['target'])) {
                    @unlink($attach['target']);
                    continue;
                }
                $attach['attachment'] = dhtmlspecialchars(trim($attach['attachment']));
                // @unlink(getglobal('setting/attachdir').'./project/'.$space[$key]);
			}
		}
	}
	return $attach;
}



/**
 * +----------------------------------------------------------
 * 当前位置
 * +----------------------------------------------------------
 * $sign 模块标志
 * $class 分类ID或模块子栏目
 * $title 信息标题
 * +----------------------------------------------------------
 */
function ur_here($sign, $class = '', $title = '',$first_class=false) {
	global $_G;
	$oppo = looppo($sign);
	if ($sign == 'page') {
	// 如果是单页面，则只显示单页面名称
		$ur_here = $title;
	} elseif (!$class) {
		// 模块主页
		$ur_here = $oppo['head_title'];
	} else {
		// 模块名称
		// $main = '<a href=' . $_G['siteurl'] . '>首页</a>';
		$main = '<a href="' . LO_CURURL . '">首页</a>';
		// 如果存在分类
		if ($class) {
			$table = (stripos($oppo['table'], '_category')===false)?$oppo['table'].'_category':$oppo['table'];
			$class = json_decode($class);
			if (is_object($class)) {
				// $class = $class->0;
			} elseif (is_array($class)) {
				$class = $class[0];
			}
			if ($first_class) {
				return $class;
			}
			$cat_name = is_numeric($class) ? DB::result_first("SELECT name FROM " . DB::table($table) . " WHERE cid='$class'") : $oppo['head_title'];
			// 如果存在标题
			if ($title) {
				// http://hielec.wincomtech.cn/plugin.php?id=lodatasheet:datasheet&pluginop=page&loid=2
				// $category = '<b> > </b><a href="' . LO_CURURL . '&pluginop=page">' . $cat_name . '</a>';
				$category = '<b> > </b><span>' . $cat_name . '</span>';
			} else {
				$category = "<b> > </b>$cat_name";
			}
		}
		// 如果存在标题
		if ($title) $title = '<b> > </b>' . $title;
		$ur_here = $main . $category . $title;
	}
	return $ur_here;
}

/**
 * +----------------------------------------------------------
 * 标题
 * +----------------------------------------------------------
 * $sign 模块标志
 * $class 分类ID或模块子栏目
 * $title 信息标题
 * +----------------------------------------------------------
 */
function page_title($sign, $class = '', $title = '') {
	global $_G;
	$oppo = looppo($sign);
	// 如果是单页面，则只执行这一句
	if ($sign == 'page') {
		$titles = $title . ' | ';
	} elseif ($sign) {
		// 模块名称
		$main = $oppo['head_title'] . ' | ';
		// 如果存在分类
		if ($class) {
			$table = (stripos($oppo['table'], '_category')===false)?$oppo['table'].'_category':$oppo['table'];
			$cat_name = is_numeric($class) ?DB::result_first("SELECT name FROM " . DB::table($table) . " WHERE cid=$class") : $oppo['head_title'];
			$cat_name = $cat_name . ' | ';
		}
		// 如果存在标题
		if ($title) $title = $title . ' | ';
		$title = $title . $cat_name . $main;
	}
	$page_title = $title ? $title . $_G['setting']['bbname'] : $_G['setting']['bbtitle'];
	return $page_title;
}

/*网站SEO*/
function lo_seo($seo=array())
{
	global $_G;
	if (!isset($seo['title'])) {
		$seo['title'] = '';
	} else {
		$seo['title'] = $_G['setting']['bbname'] . ($seo['title']?' - '.$seo['title']:'');
	}

	if (!isset($seo['keywords'])) {
		$seo['keywords'] = '';
	} else {
		$seo['keywords'] = dhtmlspecialchars($seo['keywords']);
	}

	if (!isset($seo['description'])) {
		$seo['description'] = '';
	} else {
		$seo['description'] = dhtmlspecialchars($seo['description']);
	}
	return $seo;
}

/*网站广告 role= 1首页 2外包 3二手交易 4下载 5DT 6活动*/
function lo_advs($role='')
{
	$advs = DB::fetch_all('SELECT * FROM '. DB::table('common_advertisement') . ' WHERE available=1 and type=\'custom\' ORDER BY displayorder ');
	$adv_customs = array();$ii=1;
	foreach ($advs as $val) {
		$val['parameters'] = unserialize($val['parameters']);
		if ($val['parameters']['extra']['customid']==$role) {
			// $adv_customs[] = $val;
			switch ($val['parameters']['style']) {
				case 'code':
					$adv_customs[$ii] = $val['parameters']['html'];
					break;
				case 'image':
					$adv_customs[$ii] = $val['parameters']['html'];
					break;
			}
			$ii++;
		}
	}
	return $adv_customs;
}

/*
 * 信息提示
 * $msg
 * $status success/error/charge
 * $sign col/zan/cai/down/
 * $url
 * $type json/html/xml
*/

function lo_msg($msg='', $status='error', $sign, $url='', $timeout=3, $type='json')
{
	if ($type=='html') {
		plugin_common::jumpgo($msg, $url, '', $status);
	} elseif ($type=='xml') {
		# code...
	} else {
		echo json_encode( array('msg'=>$msg, 'status'=>$status, 'sign'=>$sign, 'url'=>$url, 'timeout'=>$timeout) );exit();
	}
}
?>