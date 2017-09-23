<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
// 在访问PHP类中的成员变量或方法时，如果被引用的变量或者方法被声明成const（定义常量）或者static（声明静态）,那么就必须使用操作符::,
// 反之如果被引用的变量或者方法没有被声明成const或者static,那么就必须使用操作符->。
// 另外，如果从类的内部访问const或者static变量或者方法,那么就必须使用自引用的self，
// 反之如果从类的内部访问不为const或者static变量或者方法,那么就必须使用自引用的$this。
// 结论 :  self与$this的功能极其相似，但二者又不相同。$this不能引用静态成员和常量。self更像类本身，而$this更像是实例本身。

class plugin_common {
	// $this->group = array();
	// const group = "123";

	// function plugin_common($value='')
	// {
	// 	# code...
	// }

	// function __construct($_G){
	// }

	// public function fc()
	// {	#test
	// 	global $_G,$tpre;
	// 	// return $tpre;
	// 	return $_G;
	// 	return self::group;
	// 	// return $G;
	// }

	/*
	 * +----------------------------------------------------------
	 * 跳转
	 * 调用类以外的全局方法？
	 * showmessage($skip['msgno'], $skip['urlno'].$skip['ext']);exit;
	 * +----------------------------------------------------------
	*/
	public static function jumpgo($msg='', $url='', $ext='', $status='error')
	{
		// $jumpurl = (function_exists('cpmsg')) ? AURLJUMP : LO_CURURL ;
		// $jumpext = $ext ?  '&pluginop='.$ext : '';
		if ($msg) {
			if (function_exists('cpmsg')) {
				// cpmsg('抱歉，您的请求来路不正确或表单验证串不符，无法提交', '', 'error');
				// cpmsg('状态更改成功！', AURLJUMP, 'success');
				cpmsg($msg, ($url?$url:AURLJUMP).$ext, $status);
			} elseif (function_exists('showmessage')) {
				// showmessage(THISPLUG.':非法操作！'.THISPLUG, LO_CURURL);// 只有提示信息时显示红色叉号；有跳转url则显示蓝色感叹号
				showmessage(THISPLUG.':'.$msg, ($url?$url:($status=='error'?'':LO_CURURL)).$ext);
			} else {
				echo '<script>'. ($msg?'alert("'.$msg.'")':'') .'location.href="'.($url?$url:LO_CURURL).$ext.'"</script>';
			}
		} else{
			header("Location:". $url . $ext);exit;
		}
	}


    /**
     * +----------------------------------------------------------
     * 标准查询时的字段处理
     * 默认给加反引号 ``
     * $fields 要处理的字段 $fields = 'id,name,sex';
     * $unite 联表标志
     * $lokey 关键key
     * +----------------------------------------------------------
	*/
	public static function create_fields_quote($fields, $unite='',$lokey)
	{
		$fields = explode(',', $fields);
		foreach ($fields as $f) {
			if ($f) {
				$fquote .= isset($fquote) ? ',' : '';
				$fquote .= ($unite) ? $unite.'.'.$f : ($f=='*'?$f:'`'.$f.'`');
			}
		}
		if ($fields!='*' && $lokey && !in_array($lokey,$fields)) {
			if ($unite) {
				$fquote = $unite.'.'.$lokey .','. $fquote;
			} else {
				$fquote = '`'.$lokey.'`,'. $fquote;
			}
		}
		return $fquote;
	}
    /**
     * +----------------------------------------------------------
     * 创建IN查询，如"IN('1','2')";
     * $Ids 要处理成IN查询的数组
     * +----------------------------------------------------------
	*/
	public static function create_sql_in($Ids, $unite=',')
	{
		if (empty($Ids)) { return ''; }
		elseif (!is_array($Ids)) { $Ids = explode($unite, $Ids); }
		foreach ($Ids as $id) {
			$idstring = (string)$id;
			$id = ctype_digit($idstring) ? $id : '\''.$id.'\'';
			$sql_in .= $sql_in ? ','.$id : $id;
		}
		return " IN ($sql_in)";
	}
    /**
     * +----------------------------------------------------------
     * 插入、更新时的字段处理
     * $fields 要处理的字段 $fields = 'id,name,sex';
     * $sign 表明是更新还是插入
     * +----------------------------------------------------------
	*/
	static function fields_quote($fields=array(),$sign='update')
	{
		if (!is_array($fields)) return;
		$joint = '';
		if ($sign=='update') {
			foreach ($fields as $key => $value) {
				$joint .= $joint ? ",$key='$value'" : "$key='$value'";
			}
			$joint = ' SET '.$joint;
		} else {
			$jointk = $jointv = '';
			foreach ($fields as $key => $value) {
				$jointk .= $jointk ? ','.$key : $key;
				$jointv .= $jointv ? ",'$value'" : "'$value'";
			}
			$joint = "({$jointk}) VALUES ({$jointv})";
		}
		return $joint;
	}
	/*
	 * 创建指定数组
	 * $arr 要处理的数组
	 * 要获取的 key 组
	*/
	public static function get_ids($arr,$ko)
	{
		foreach ($arr as $v) {
			$ids .= isset($ids) ? ','.$v[$ko] : $v[$ko];
		}
		$ids = explode(',', $ids);
		$ids = array_filter($ids);
		return $ids;
	}



    /**
     * +----------------------------------------------------------
     * 无限极分类
     * +----------------------------------------------------------
    */
    public static function infinite_category($table='', $pid)
    {
    	# code...
    }

    /**
     * +----------------------------------------------------------
     * 获取有层次的栏目分类，有几层分类就创建几维数组
     * +----------------------------------------------------------
     * $table 数据表名
     * $parent_id 默认获取一级导航
     * $current_id 当前页面栏目ID
     * +----------------------------------------------------------
     */
    public static function get_category($table, $fields='*', $order=" ORDER BY sort ASC", $parent_id=0, $current_id) {
        $category = array();
        $data = DB::fetch_all("SELECT ". $fields ." FROM ". DB::table($table) . $order);
        foreach ((array)$data as $value) {
            // $parent_id将在嵌套函数中随之变化
            if ($value['pid'] == $parent_id) {
                // $value['url'] = '';
                // $value['cur'] = $value['cid'] == $current_id ? true : false;
                foreach ($data as $child) {
                    // 筛选下级导航
                    if ($child['pid'] == $value['cid']) {
                        // 嵌套函数获取子分类
                        $value['child'] = self::get_category($table, $fields, $order, $value['cid'], $current_id);
                        break;
                    }
                }
                $category[] = $value;
            }
        }
        return $category;
    }

    /*
	 * 获取指定顶级id
	 * level 适合固定上级分类，在改变分类或删除分类需要更细致的处理子类
	 * level 暂时无法处理子类变化
    */
    public static function get_tid($table, $cid, $pid=0, $level=0){
    	if($cid==$pid){return array($cid,$level);}
        $sql = "SELECT pid FROM ". DB::table($table) ." WHERE cid=$cid ";
        $res = DB::result_first($sql);
        if($res > $pid){
        	++$level;
            return self::get_tid($table,$res,$pid,$level);
        } else {
        	++$level;
            return array($cid,$level);
        }
    }



	/*
	 * +----------------------------------------------------------
	 * 日志记录
		// $data = array(
		// 	'type' => $type,
		// 	'brief' => $brief,
		// 	'uid' => $uid,
		// 	'tid' => $tid,
		// 	'credits' => $credits,
		// 	'cos' => $cos,
		// 	'exp' => $exp,
		// 	'total' => $total,
		// 	'status' => $status
		// 	);
	 * +----------------------------------------------------------
	*/
	public static function common_log($table='', $data)
	{
		if (empty($table)) {
			self::jumpgo('表名不能为空！');
		}

		$extarr = array('ip'=>CURIP,'addtime'=>CURTIME,'modtime'=>CURTIME);
		$data = array_merge($data,$extarr);
		return DB::insert($table, $data, true);
	}

	/*
	 * 数据分页
	 *
	*/
	public static function pager($sql, $page, $jumpurl, $pagesize)
	{
        // 数据分页
        $pagesize = $pagesize?$pagesize:10;// 每页记录数
        $query = DB::query($sql);
        $amount = DB::result($query, 0);// 查询记录总数
        $pagecount = $amount?(($amount<$pagesize)?1:(($amount%$pagesize)?((int)($amount/$pagesize)+1):($amount/$pagesize))):0;// 计算总页数
        $page = empty($page) ? 1 : max(1, intval($page));
        $page = $page > $pagecount ? 1 : $page;// 取得当前页值
        $startlimit = ($page - 1) * $pagesize;// 查询起始的偏移量
        $multipage = multi($amount, $pagesize, $page, $jumpurl, $pagecount);

        $limit = DB::limit($startlimit,$pagesize);
        return array($multipage,$limit);
	}

	/* +----------------------------------------------------------
	 * 通用列表页
	 * $table 表名
	 * $where 条件
	 * $order 排序
	 * $pagesize 每页记录数
	 * $page 当前页码
	 * $join 联表查询
	 * $asitem 默认表的as
	 * AURL 常量 ($table='activity_log', $wh, $recycle='', $skip, $sign=array())
	 * +----------------------------------------------------------
	*/
	public static function common_list($table='', $pagesize=20, $page, $where=array(), $order='', $jumpext, $fields='*', $join=array(), $asitem='a')
	{
		global $lostatus;
		if (empty($table)) {
			unset($lostatus);
			self::jumpgo('表名不能为空！');
		}
		// 联表、查询字段
		$joinstr = '';
		if ($join) {
			$as = $asitem . '.';
			$joinstr = ' as ' . $asitem;
			$fields_def = explode(',', $fields);$fields='';
			foreach ($fields_def as $value) {
				$fields .= $as . $value . ',';
			}
			foreach ($join as $k => $v) {
				$joinstr .= ' LEFT JOIN ';
				$joinstr .= '`'.DB::table($v[2]).'` as '. $v[0] .' ON ' . $as.$v[3] . $v[4] . $v[0].'.'.$v[5];
				$v1 = explode(',', $v[1]);
				foreach ($v1 as $vf) {
					$v1str .= $v1str ? ',': '';
					$v1str .= $v[0] . '.' . $vf;
				}
			}
			$fields .= $v1str;
		}

		// 搜索
		// @list($start, $end, $key, $keyword, $uid, $recycle) = $where;
		// parse_str(str);// 把查询字符串解析到变量中。php.ini 文件中的 magic_quotes_gpc 设置影响该函数的输出。如果已启用，那么在 parse_str() 解析之前，变量会被 addslashes() 转换。
		extract($where);
		$wh = $url = '';
		if ($start[1]) {
			$start[1] = trim($start[1]);
			$wh .= $wh ? ' AND ': '';
			$wh .= $as . $start[2] . '>=' . strtotime($start[1]);
			$url .= '&' . $start[0] . '=' . $start[1];
		}
		if ($end[1]) {
			$end[1] = trim($end[1]);
			$endtime = $end[1] ? strtotime($end[1])+86399 : '';
			$wh .= $wh ? ' AND ': '';
			$wh .= $as . $end[2] . '<=' . $endtime;
			$url .= '&' . $end[0].'=' . $end[1];
		}
		if ($key[1]) {
			$key[1] = trim($key[1]);
			$wh .= $wh ? ' AND ': '';
			$wh .= $as . $key[2] . '=\'' . $key[1] . '\'';
			$url .= '&'.$key[0].'='.$key[1];
		}
		if ($keyword[1]) {
			$keyword[1] = trim($keyword[1]);
			$wh .= $wh ? ' AND ( ': ' ( ';
			$keyword[2] = explode(',', $keyword[2]);
			foreach ($keyword[2] as $v) {
				$keywordstr .= ($keywordstr) ? ' OR ': '';
				$keywordstr .= $as.$v . ' LIKE \'%' . $keyword[1] . '%\' ';
			}
			$wh .= $keywordstr.' ) ';
			$url .= '&' . $keyword[0] . '='.$keyword[1];
		}
		if ($uid[1]) {
			$uid[1] = trim($uid[1]);
			$wh .= $wh ? ' AND ': '';
			$wh .= "$as$uid[2]='$uid[1]'";
			$url .= '&'.$uid[0].'='.$uid[1];
		}
		if ($recycle) {
			$wh .= $wh ? ' AND ': '';
			$wh .= $as.$recycle;
		}
		$wh = $wh ? ' WHERE ' . $wh : '';

		// 排序
		if ($order) {
			$order = explode(',',$order);
			foreach ($order as $val) {
				$orderstr .= $orderstr ? ',': '';
				$orderstr .= $as.$val;
			}
			$order = ' ORDER BY ' . $orderstr;
		}

		// 数据分页跳转
		$jumpext = $jumpext ?  '&pluginop='.$jumpext : '';
		$jumpext = $url ? $jumpext.$url : $jumpext;
		$jumpurl = (function_exists('cpmsg')) ? AURL : LO_CURURL ;
		$jumpurl = $jumpurl . $jumpext;

		// 数据分页
		$pagesize = $pagesize ? $pagesize : 20;// 每页记录数
		$query = DB::query("SELECT COUNT(*) FROM " . DB::table($table) . $joinstr . $wh);
		$amount = DB::result($query, 0);// 查询记录总数
		$pagecount = $amount?(($amount<$pagesize)?1:(($amount%$pagesize)?((int)($amount/$pagesize)+1):($amount/$pagesize))):0;// 计算总页数
		$page = max(1, intval($page));
		$page = $page > $pagecount ? 1 : $page;// 取得当前页值
		$startlimit = ($page - 1) * $pagesize;// 查询起始的偏移量
		$multipage = multi($amount, $pagesize, $page, $jumpurl, $pagecount);// 显示分页

		// 查询记录集
		$list_temp = DB::fetch_all("SELECT " . $fields . " FROM " . DB::table($table) . $joinstr . $wh . $order . " LIMIT {$startlimit},{$pagesize}");
		foreach ($list_temp as $row) {
			$row['brief'] = isset($row['brief']) ? self::cutstr($row['brief'],20) : '' ;
			$row['url'] = isset($row['url']) ? self::cutstr($row['url'],20) : '' ;
			$row['pic'] = isset($row['pic']) ? self::cutstr($row['pic'],20) : '' ;
			$row['pdf'] = isset($row['pdf']) ? self::cutstr($row['pdf'],20) : '' ;
			$row['attach'] = isset($row['attach']) ? self::cutstr($row['attach'],20) : '' ;
			$row['addtime'] = isset($row['addtime']) ? dgmdate($row['addtime'],'d') : '' ;
			$row['modtime'] = isset($row['modtime']) ? dgmdate($row['modtime']) : '' ;
			if (isset($row['status'])) $row['status'] = self::replace_preg($row['status'], $lostatus);
			$row['is_show'] = isset($row['is_show']) ? '显示' : '隐藏' ;
			$list[] = $row;
		}
		unset($lostatus);
		return array('multipage'=>$multipage,'list'=>$list);
	}

	/*
	 * 通用数据提交
	 * $data	要提交的数据
	 * $table	表名
	 * del_files	删除文件
	*/
	public static function common_op($data, $table='', $del_files=array())
	{
		global $_G,$upload_common_path_op; $wh = array();
		//全局变量，用完就unset($upload_common_path_op);
		//防止数据重复提交
		if( $data['formhash']==FORMHASH ){
			/*字段过滤、初始化*/
			$loid = $data['loid'] ? intval($data['loid']) : 0;
			$id_filter = $data['id_filter'] ? trim($data['id_filter']) : '';
			if (empty($table)) {
				if ($data['table']) {
					$table =trim($data['table']);
				} else {
					self::jumpgo('表名不能为空！');
				}
			}
			$sign = trim($data['sign']);
			$url_return_ok = trim($data['url_return_ok']);
			$url_return_no = trim($data['url_return_no']);
			if (!empty($loid) and isset($data['pid']) && $loid==$data['pid']) self::jumpgo('不能与父级同类！');
			unset($data['formhash'],$data['loid'],$data['id_filter'],$data['table'],$data['sign'],$data['url_return_ok'],$data['url_return_no']);
			foreach ($data as $k => $v) {
				// $data[$k] = (is_array($v)) ? serialize($v) : $v;
				$data[$k] = (is_array($v)) ? json_encode($v) : trim($v);
				// if ($_FILES[$k]['name']) {
				// 	$data[$k] = self::upload_file($k, $upload_common_path_op . ($k=='pic' ? LO_PIC : ($k=='pdf'?LO_PDF:LO_ATTACH)));
				// }
			}

			/*字段验证*/
			if ($data['name']) {
				$name = $data['name'];
				$name_key = 'name';
			} elseif ($data['title']) {
				$name = $data['title'];
				$name_key = 'title';
			} elseif ($data['goods_name']) {
				$name = $data['goods_name'];
				$name_key = 'goods_name';
			} else {
				self::jumpgo('名称或标题不能为空！');
			}

			if ($_FILES['pic']['name']) {
				$data['pic'] = self::upload_file('pic', $upload_common_path_op . LO_PIC);
				$im_path = DB::result_first("SELECT pic FROM ".DB::table($table)." WHERE {$id_filter}={$loid}");
				$del_files = array('pic', $im_path);
			}
			if ($_FILES['pdf']['name']) {
				$data['pdf'] = self::upload_file('pdf', $upload_common_path_op . LO_PDF);
				$im_path = DB::result_first("SELECT pdf FROM ".DB::table($table)." WHERE {$id_filter}={$loid}");
				$del_files[] = array('pdf', $im_path);
			}
			if ($_FILES['attach']['name']) {
				$data['attach'] = self::upload_file('attach', $upload_common_path_op . LO_ATTACH);
				$im_path = DB::result_first("SELECT attach FROM ".DB::table($table)." WHERE {$id_filter}={$loid}");
				$del_files[] = array('attach', $im_path);
			}
			if ($_FILES['logo']['name']) {
				$data['logo'] = self::upload_file('logo', $upload_common_path_op . LO_PIC);
				$im_path = DB::result_first("SELECT logo FROM ".DB::table($table)." WHERE {$id_filter}={$loid}");
				$del_files[] = array('logo', $im_path);
			}

			/*预处理值*/
			$jumpurl = '';
			$url_return_ok = ($url_return_ok ? ($sign?'&pluginop=commonlist&sign='.$sign:$url_return_ok) : '&pluginop='.($sign?'commonlist&sign='.$sign:'')) . ($loid ? '&loid='.$loid : '');
			$url_return_no = ($url_return_no ? ($sign?'&pluginop=commonpage&sign='.$sign:$url_return_no) : '&pluginop='.($sign?'commonpage&sign='.$sign:'page')) . ($loid ? '&loid='.$loid : '');
			// 判断字段的有效性并赋值
			$fields_info = session_ob($table, '*', '', 'column');
			foreach ($fields_info as $v) {
				switch ($v['field']) {
					case 'author_id':
						$data['author_id'] = $_G['adminid'] ? $_G['adminid'] : $_G['uid'];
						break;
					case 'ip':
						$data['ip'] = CURIP;
						break;
					case 'addtime':
						$data['addtime'] = $data['addtime'] ? $data['addtime'] : CURTIME;
						break;
					case 'modtime':
						$data['modtime'] = CURTIME;
						break;
					case 'status':
						$data['status'] = $data['status'] ? $data['status'] : (AUDIT?0:2);
						break;
				}
			}
// debug($fields_info,1);

			/*新增、编辑非公共区域*/
			if ($loid) {
				$wh[$id_filter] = $loid;
			} else {
				if ($name_key) {
					$vdata = DB::result_first("SELECT ". $name_key ." FROM ". DB::table($table) ." WHERE ". $name_key . '=\'' . $name . '\'');
					if ($vdata) {
						self::jumpgo('对不起，该型号或标题已存在！');
					}
				}
			}
// debug($data,1);
			try {
				if ($loid) {
					$affected = DB::update($table,$data,$wh);
				} else {
					$affected = DB::insert($table,$data,true);
				}
				if (!$affected) throw new Exception('操作失败');
				# 删除相应的旧文件
				if (!empty($del_files) && $loid) self::standard_file_del($loid, $del_files);

				// 记录 record_log
				// $data = array();
				// DB::insert($table.'_log', $data);

				session_ob($table, '', '', 'del');
				self::jumpgo('提交成功！', $jumpurl, $url_return_ok, 'success');
				// header("Location:".$jumpurl.$url_return_ok);exit;
			} catch (Exception $e) {
				self::jumpgo($e->getMessage(), $jumpurl, $url_return_no);
			}
		} else {
			self::jumpgo('抱歉，您的请求来路不正确或表单验证串不符，无法提交');
		}
	}

	/* +----------------------------------------------------------
	 * 通用删除
	 * $table 表名
	 * $wh 条件
	 * $recycle 回收站(用户&管理员&发布者)： u&a&s
	 * $skip 跳转
	 * AURL AURLJUMP THISPLUG 常量
	 * +----------------------------------------------------------
	*/
	public static function common_taskdel($table='', $wh, $recycle='', $skip, $del_filekey='')
	{
		if (empty($table)) { self::jumpgo('表名不能为空！'); }
		/*$wh = array(
				'lokey' => 'id',
				'loids' => 1,
				'author_id' => 2
			);
		$recycle = array('u','a','s');
		$skip = array(
				'msgok' => THISPLUG.':已删除您勾选的条目！',
				'urlok' => LO_CURURL.'&pluginop=managelist',
				'msgno' => THISPLUG.':删除失败！',
				'urlno' => LO_CURURL.'&pluginop=managelist',
				'ext' => '&loid=1'
			);*/

		$condition = array();
		foreach ($wh as $k => $v) {
			$where .= $k.'='.(is_numeric($v)?$v:'\''.$v.'\'');
			if ($k=='lokey') {
				$lokey = $v;
			} elseif ($k=='loids') {
				$loids = $v;
			} else {
				$condition[$k] = $v;
			}
		}
		// if (empty($skip)) {
		// 	$skip = array('msgok'=>'删除成功！','urlok'=>'','msgno'=>'删除失败！','urlno'=>'','ext'=>'');
		// }

		if ($loids) {
			// if ($recycle) {
				foreach ((array)$loids as $val) {
					$affected_rows += self::standard_del($val, $lokey, $table, $recycle, $condition, $del_filekey);
				}
			// } else { // 下面这一步可优化
			// 	if ($del_filekey) {
			// 		global $tpre;
			// 		foreach ((array)$loids as $val) {
			// 			$files_info = DB::fetch_first("SELECT {$del_filekey} FROM ". DB::table($table) ." WHERE {$lokey}={$val}");
			// 			$del_files = array();
			// 			foreach ($del_filekey as $kp) {
			//				$del_files[$val][] = array($kp, $files_info[$kp]);
			// 			}
			// 		}
			// 	}
			// 	$sql_in = self::create_sql_in($loids);
			// 	$affected_rows = DB::query("DELETE FROM ". DB::table($table) ." WHERE {$lokey} {$sql_in}");
			// 	if (isset($del_files) && $affected_rows) {
			// 		foreach ($del_files as $key => $value) {
			// 			self::standard_file_del($key, $value);
			// 		}
			// 	}
			// }
			// 对结果的处理
			session_ob($table, '', '', 'del');
			if ($affected_rows) {
				self::jumpgo($skip['msgok'], $skip['urlok'], $skip['ext'], 'success');
			} else {
				self::jumpgo($skip['msgno'], $skip['urlno'], $skip['ext']);
			}
		} else {
			self::jumpgo('ID不能为空！');
		}
	}

    /*
     * 通用数据删除
     * $recycle 回收机制
     * $is_del='u&a&s' 默认回收标识
     * $del_filekey='pic,pdf,attach' 文件删除对应字段
    */
	private static function standard_del($loid, $lokey, $table, $recycle, $condition, $del_filekey, $is_del='u&a&s')
	{
		// if(preg_match("/^\d*$/",$loid))echo('是数字'); else echo('不是数字');
		$id = is_numeric($loid) ? $loid : "'$loid'";
		if($lokey) $condition[$lokey] = $id;
		$rescle = '';
		if (!empty($recycle)) {
			$rescle = DB::result_first("SELECT recycle FROM ".DB::table($table)." WHERE {$lokey}={$id}");
		}
		$strrpos = strrpos($rescle,$recycle);
		// $strrpos = strrpos('','');// 都为空时却返回false
		if ($strrpos === false) {
			if (mb_strlen($rescle)>=3 || empty($recycle)) {
				if ($del_filekey) {
					$files_info = DB::fetch_first("SELECT {$del_filekey} FROM ". DB::table($table) ." WHERE {$lokey}={$id}");
					$del_files = array(); $del_filekey = explode(',', $del_filekey);
					foreach ($del_filekey as $kp) {
						if ($files_info[$kp]) {
							$del_files[] = array($kp, $files_info[$kp]);
						}
					}
				}
				// $affected_rows = DB::update($table, array('recycle'=>$is_del), $condition);
				$affected_rows = DB::delete($table, $condition);
				# 删除相应文件
				if (isset($del_files) && $affected_rows) self::standard_file_del($loid, $del_files);
			} else {
				$rescle = empty($rescle) ? $recycle : $rescle . '&' . $recycle;
				$affected_rows = DB::update($table, array('recycle'=>$rescle), $condition);
			}
		}
		return $affected_rows;
	}

	/*
	 * 通用文件删除
	 * loids 多表时的loids用数组array(id1,id2,id3)，默认为单一ID
	*/
	private static function standard_file_del($loids, $del_files, $unique=true)
	{
		/*$del_files = array(
				array('根路径','文件信息'),
				array('/','pic'),
				array('/','pdf'),
				array('/','attach')
			);
		$del_files = array(
				array('表名','主键','字段'),
				array('tableA','lokey_pic','field_picpath'),
				array('tableB','lokey_pdf','field_pdfpath'),
				array('tableC','lokey_attach','field_attachpath')
			);*/
		// 删除相应文件
		foreach ((array)$loids as $val) {
			$valstring = (string)$val;
			$val = ctype_digit($valstring) ? $val : '\''. $val .'\'';
			foreach ($del_files as $v) {
				if ($unique) { // 单表直接传文件信息
					$cpath = $v[0]; $delfiles = $v[1];
				} else { // 多表情况
					// $v = array('表名','主键','字段')
					$cpath = $v[2];
					$delfiles = DB::result_first("SELECT ". $v[2] ." FROM ". DB::table($v[0]) ." WHERE ". $v[1] .'='. $val);
				}
				self::del_image($cpath, $delfiles);
			}
		}
	}

    /**
     * +----------------------------------------------------------
     * 通用文件上传的处理函数
     * +----------------------------------------------------------
     * $upfile 上传的文件域
     * $file_rename 给上传的文件重命名 true/false:原名 null:随机名 其它:原名
     * $file_dir 文件上传路径，结尾加斜杠
     * $dir_ext 上传文件目录分类方式 time:时间 type:格式 user_?:用户
     * $thumb_dir 缩略图路径（相对于$file_dir ）,结尾加斜杠，留空则跟$file_dir相同
     * $upfile_type 允许的文件格式
     * $upfile_size_max 文件大小最大值
     * +----------------------------------------------------------
     */
	public static function upload_file($upfile, $file_dir='/upload/pic/', $dir_ext='time', $file_rename=null, $thumb_dir='', $upfile_type='jpg,jpeg,gif,png,bmp,rar,zip,pdf', $upfile_size_max='2048')
	{
		$file_name = $_FILES[$upfile]['name'];
		$file_type = $_FILES[$upfile]['type'];
		$file_tmp_name = $_FILES[$upfile]['tmp_name'];
		// $file_error = $_FILES[$upfile]['error'];
		$file_size = $_FILES[$upfile]['size'];

		$ext = '';// 目录拓展
		if ($dir_ext == 'time') {
			$ext = date('Y-m-d',time()).'/';
		} elseif ($dir_ext == 'type') {
			$file_type = str_replace('/', '_', $file_type);
			$ext = $file_type.'/';
		}
		// $file_dir = basename($file_dir);
		// $file_dir = realpath($file_dir);
		$status = self::dir_status($file_dir.$ext, true);
		// if (in_array($status,array('write','mkdirok'))) {
		if (stripos('write,mkdirok',$status)===false) {
		    self::jumpgo('目录非法！');
		}

		if (@empty($file_name))
			self::jumpgo('上传的文件名'.$upfile.'是空的！');

		// 文件格式合法性的判断
		$fname = explode(".", $file_name); // 将上传前的文件以“.”分开取得文件类型
		$fcount = count($fname); // 获得截取的数量，避免后缀前面还有其他点
		$ftype = $fname[$fcount-1]; // 取得文件的类型
		if (stripos($upfile_type, $ftype) === false) {
		    self::jumpgo('上传的文件类型不支持！');
		}

		$file_size_kb = ceil($file_size / 1024);
		if ($file_size_kb > $upfile_size_max) {
			// @unlink($upfile_name);
			self::jumpgo('上传的文件大小超出 ' . $upfile_size_max . ' KB！');
		}

		// 没有命名规则默认以时间作为文件名前缀（后缀前面的），$file 写入数据库的文件名。
		if (is_bool($file_rename)) {
			$file = $file_rename = $file_name;//源文件名
		} elseif (is_null($file_rename)) {
			$file_rename = self::locrs('nlL', 6, time());// 随机名
			$file = $file_rename . '.' . $ftype;
		} else {
			$file = $file_rename . '.' . $ftype;// 自定义名
		}
		$file = $ext . $file;
		$upfile_name = $file_dir . $file; // 上传后的文件名称
		$upfile_name = self::convert($upfile_name,'','gbk');
		$upfile_ok = move_uploaded_file($file_tmp_name, $upfile_name);
		if ($upfile_ok===false) {
			self::jumpgo('上传失败！');
		}

		return $file;
	}

	public static function update_file($upfile,$oldfile='')
	{
		global $upload_common_path_op;
		// 附件处理
		if ($_FILES[$upfile]['name']) {
			if ($oldfile) {
				$fname2 = strrchr($oldfile, '/');
				// $fname2 = fileext($oldfile);
				$fname1 = str_replace($fname2,'',$oldfile);
				$fname = basename($oldfile);
				$ftype = fileext($oldfile);
				$fname = str_replace('.'.$ftype,'',$fname);
				$oldfile = $upload_common_path_op . LO_ATTACH . $oldfile;
				$fdir = dirname($oldfile);
			}
			// 如果有旧文则优先处理旧文件，否则当做新文件处理
			if ($fname) {
				$fdir .= '/';
				$fdirext = '';
			} else {
				$fdir = $upload_common_path_op . LO_ATTACH;
				$fdirext = 'time';
			}
			return ($fname1?$fname1.'/':'') . self::upload_file($upfile, $fdir, $fdirext, $fname);
		}
		return false;
	}

	public static function del_file($files='',$dir='')
	{
		global $upload_common_path_op;
		$dir = $dir ? $dir : $upload_common_path_op . LO_ATTACH;
		$files = is_array($files)?$files:explode(';', $files);
		foreach ($files as $k => $f) {
			$file = self::convert($f,'gbk');
			if (file_exists($dir.$file)) {
				$result = @unlink($dir.$file);
			}
		}
		return $result;// true or false
	}

	/**
	* +----------------------------------------------------------
	* 删除图片
	* 针对一个字段的
	* unlink成功返回true失败返回false
	* is_file file_exists unlink涉及到编码问题
	* +----------------------------------------------------------
	*/
	public static function del_image($cpath='', $delfiles) {
		global $tpre;
		// $cpath = self::convert($cpath,'gbk');
		$delfiles = explode(';', $delfiles);
		$cpath = LO_ROOT . LO_UPLOAD . $tpre .'/'. $cpath .'/';
		foreach ($delfiles as $item) {
			$img = self::convert($item,'gbk');
			if (is_file($cpath . $img)) {
				@unlink($cpath . $img);
			}
			$fext = explode(".", $img);
			$fcount = count($fext);
			$ftype = $fext[$fcount-1];
            // $ftype = fileext($oldfile);
			$img = str_replace('.'.$ftype,'',$img);
			$img_thumb = $img .'_thumb.'. $ftype;
			if (file_exists($cpath . $img_thumb)) {
				@unlink($cpath . $img_thumb);
			}
		}
	}
	/**
	 * +----------------------------------------------------------
	 * 获取去除路径和扩展名的文件名
	 * $file 图片地址
	 * +----------------------------------------------------------
	*/
	public static function get_file_name($file) {
		$basename = basename($file);
		return $file_name = substr($basename, 0, strrpos($basename, '.'));
	}
	//以下方法匹配出内容中所有图片
	static function getImgs($content, $order='ALL'){
	    $pattern="/<img.*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
	    preg_match_all($pattern, $content, $match);
	    if(isset($match[1])&&!empty($match[1])){
	        if($order==='ALL'){
	            return $match[1];
	        }
	        if(is_numeric($order)&&isset($match[1][$order])){
	            return $match[1][$order];
	        }
	    }
	    return '';
	}

	/**
	 * +----------------------------------------------------------
	 * 判断目录状态
	 * 创建目录
	 * +----------------------------------------------------------
	*/
	public static function dir_status($dir,$mkdir=false) {
		// return $dir;
		// 判断目录是否存在
		if (file_exists($dir)) {
			// 判断目录是否可写
			if ($fp = @fopen("$dir/test.txt", 'w')) {
			    @fclose($fp);
			    @unlink("$dir/test.txt");
				$status = 'write';
			} else {
				$status = 'exist';
			}
		} else {
			if ($mkdir) {
				$direxp = explode('/', $dir); $dirtemp = '';
				$n = count($direxp)-1;
				foreach ($direxp as $k => $v) {
					if ($k!=$n) {
						if (!file_exists($dirtemp.$v)) {
							$mk = mkdir($dirtemp.$v,0777);// 创建目录
							if ($mk) {
								$status = 'mkdirok';
							} else {
								$status = 'mkdirfail';
							}
						}
						$dirtemp .= $v.'/';
					}
				}
			} else {
				$status = 'no_exist';
			}
		}
		return $status;
	}

	/**
	 * +----------------------------------------------------------
	 * 删除目录及目录下所有子目录和文件
	 * +----------------------------------------------------------
	 * $dir 要删除的目录
	 * $sub_dir 只删除子目录
	 * +----------------------------------------------------------
	 */
	public static function del_dir($dir, $sub_dir = false) {
		if ($handle = @opendir($dir)) {
	       // 删除目录下子目录和文件
			while (false !== ($item=@readdir($handle))) {
				if ($item!='.' && $item!='..') {
					if (is_dir("$dir/$item")) {
						self::del_dir("$dir/$item");
					} else {
						@unlink("$dir/$item");
					}
				}
			}
			closedir($handle);
			// 删除目录本身
			if (!$sub_dir) @rmdir($dir);
		}
	}

	/*
	 * 获取文件的大小
	 * 远程
	*/
	static function get_filesize($url='') {
	    // 选用适当的方法保证获取文件大小 需要打开allow_url_fopen!
	    $header_array = get_headers($url, true);
	    if (preg_match('/200|ok/i', $header_array[0])) {
	        # [0] => HTTP/1.0 200 OK
	        $filesize = self::transByte( $header_array['Content-Length'] );
	        // $filesize = sizecount( $header_array['Content-Length'] );
	    } elseif (stripos($header_array[1],'400')) {
	        # 400错误 [1] => HTTP/1.1 400 Bad Request
	        # 可能会有编码问题
			$file = file_get_contents($url);
			$filesize = strlen($file);
			if ($filesize==0) {
				$filesize = '未知';
			} else {
				$filesize = self::transByte( $filesize );
			}
	    } else {
	        # fsockopen
	        $filesize = 0;
	    }
	    return $filesize;
	}

	/**
	* 转换字节大小
	* @param number $size
	* @return number
	*/
	static function transByte($size) {
		$arr = array("B", "KB", "MB", "GB", "TB", "EB" );
		$i = 0;
		while ( $size >= 1024 ) {
			$size /= 1024;
			$i ++;
		}
		return round($size, 2) . $arr[$i];
	}
	/*function get_format_size($bytes) {
		$units = array(
				0 => 'B',
				1 => 'kB',
				2 => 'MB',
				3 => 'GB'
			);
		$log = log($bytes, 1024);
		$power = (int)$log;
		$size = pow(1024, $log-$power);
		return round($size, 2) .' '. $units[$power];
	}*/



    /**
     * +----------------------------------------------------------
     * 给URL自动上http://
     * +----------------------------------------------------------
     * $url 网址
     * +----------------------------------------------------------
     */
    public static function auto_http($url) {
    	if (strpos($url, 'http://') !== false || strpos($url, 'https://') !== false) {
    		$url = trim($url);
    	} else {
    		$url = 'http://' . trim($url);
    	}
    	return $url;
    }

    /* 将1,2,3,4替换成a,b,c,d
	 * $sourcearr = array(0,1,2,3,4,5,6);
	   $destarr = array('未审核','审核中','审核通过','未过','禁用','暂停','完成','未知');
	   echo plugin_common::replace_preg(1, $destarr, $sourcearr);
    */
	public static function replace_preg($str, $destarr, $sourcearr){
		if (isset($sourcearr)) {
			for ($i=0; isset($sourcearr[$i]); $i++) {
				if($sourcearr[$i]==$str) $res = $destarr[$i];
			}
		} else {
			for ($i=0; $i < 99; $i++) {
				if($i==$str) $res = $destarr[$i];
			}
		}
		return $res?$res:'未知';
	}
	/*public static function replace_preg($str, $arrDest, $arrSource){
		if (isset($arrSource)) {
			for ($i=0; isset($arrSource[$i]); $i++) {
				if($arrSource[$i]==$str) $res = $arrDest[$i];
			}
		} elseif ($arrDest) {
			$dk = array_keys($arrDest);
			$dv = array_values($arrDest);
			$res = self::replace_preg($str, $dv, $dk);
		}
		return $res ? $res : '未知';
	}*/

	/*
	 * 字符串编码转换
	 * +----------------------------------------------------------
	 * $str 要转换编码的字符串,可以是文件
	 * $in_charset 原编码 原编码未知，通过mb_convert_encoding()的auto自动检测
	 * $out_charset 目标编码
	 * GBK UTF-8 大小写均可
	 * iconv() mb_convert_encoding() 区别
	 * iconv 特殊参数：iconv("UTF-8","GB2312//IGNORE",$str);// 将utf-8转为gb2312并忽略错误
				 iconv("gbk", "utf-8//ignore",$str);
				 mb_convert_encoding($str, 'utf-8', 'gbk');// 将gbk转为utf-8
	 * mb_convert_encoding 开启这个函数低版本需要开启mb扩展
		echo $str= '你好,这里是卖咖啡!';
		echo '<br />';
		echo iconv('GB2312', 'UTF-8', $str); //将字符串的编码从GB2312转到UTF-8
		echo '<br />';
		echo iconv_substr($str, 1, 1, 'UTF-8'); //按字符个数截取而非字节
		print_r(iconv_get_encoding()); //得到当前页面编码信息
		echo iconv_strlen($str, 'UTF-8'); //得到设定编码的字符串长度
		//也有这样用的
		$content = iconv("UTF-8","gbk//TRANSLIT",$content);
	 * +----------------------------------------------------------
	*/
	public static function convert($str, $out_charset='utf-8', $in_charset='auto', $extra = '//IGNORE'){
		$str = (array)$str;
		if (count($str)==1) {
			return mb_convert_encoding($str[0], $out_charset, $in_charset);
			// return iconv($in_charset, $out_charset.$extra, $v);// 不能自动识别原编码
		} else {
			foreach ($str as $v) {
				$arr[] = mb_convert_encoding($v, $out_charset, $in_charset);
			}
			return $arr;
		}
	}

	/**
	* +----------------------------------------------------------
	* 生成随机数
	* +----------------------------------------------------------
	* $type 随机字符类型
	* $length 长度
	* $prefix 前缀
	* nlL（number.letter.LETTER） 通配，去掉了容易混淆的字符oOLl和数字01
	* +----------------------------------------------------------
	*/
	public static function locrs($type='nl', $length=6, $prefix='', $custom_chars='') {
		$n = '23456789'; $l = 'abcdefghijkmnpqrstuvwxyz'; $L = 'ABCDEFGHIJKMNPQRSTUVWXYZ';
		switch ($type) {
			case 'n': $chars = '0123456789'; break;
			case 'l': $chars = 'abcdefghijklmnopqrstuvwxyz'; break;
			case 'L': $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; break;
			case 'lL': $chars = $l.$L; break;
			case 'nL': $chars = $n.$L; break;
			case 'nlL': $chars = $n.$l.$L; break;
			default: $chars = $n.$l; break;
		}
		// 如果有自定义的字符则包含进去
		$chars = $chars . $custom_chars;
		$string = '';
		for($i = 0; $i < $length; $i++) {
			$string .= $chars[mt_rand(0, strlen($chars)-1)];
		}
		return $prefix . $string;
	}

	/**
	* +----------------------------------------------------------
	* 字符截取
	* +----------------------------------------------------------
	* $string 待截取字符
	* $length 长度
	* $dot 尾巴
	* +----------------------------------------------------------
	*/
	static function cutstr($string, $length, $dot = '……') {
		if(strlen($string) <= $length) {
			return $string;
		}
		$pre = chr(1);
		$end = chr(1);
		$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);
		$strcut = '';
		if(strtolower(CHARSET) == 'utf-8') {
			$n = $tn = $noc = 0;
			while($n < strlen($string)) {
				$t = ord($string[$n]);
				if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
					$tn = 1; $n++; $noc++;
				} elseif(194 <= $t && $t <= 223) {
					$tn = 2; $n += 2; $noc += 2;
				} elseif(224 <= $t && $t <= 239) {
					$tn = 3; $n += 3; $noc += 2;
				} elseif(240 <= $t && $t <= 247) {
					$tn = 4; $n += 4; $noc += 2;
				} elseif(248 <= $t && $t <= 251) {
					$tn = 5; $n += 5; $noc += 2;
				} elseif($t == 252 || $t == 253) {
					$tn = 6; $n += 6; $noc += 2;
				} else {
					$n++;
				}
				if($noc >= $length) {
					break;
				}
			}
			if($noc > $length) {
				$n -= $tn;
			}
			$strcut = substr($string, 0, $n);
		} else {
			$_length = $length - 1;
			for($i = 0; $i < $length; $i++) {
				if(ord($string[$i]) <= 127) {
					$strcut .= $string[$i];
				} else if($i < $_length) {
					$strcut .= $string[$i].$string[++$i];
				}
			}
		}
		$strcut = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
		$pos = strrpos($strcut, chr(1));
		if($pos !== false) {
			$strcut = substr($strcut,0,$pos);
		}
		return $strcut.$dot;
	}

    /**
     * +----------------------------------------------------------
     * 对不同系统的换行符进行处理
     * +----------------------------------------------------------
     */
    static function line_break_change($str) {
        if (strtoupper(substr(PHP_OS,0,3))==='WIN') {
            if (stripos($str,"\r\n")===false) {
                $str = str_replace("\n",PHP_EOL,$str);
            }
        } else {
            if (stripos($str,"\r\n")===true) {
                $str = str_replace("\r\n",PHP_EOL,$str);
            }
        }
        return $str;
    }



	/**
	* 获取客户端ip地址
	* 注意:如果你想要把ip记录到服务器上,请在写库时先检查一下ip的数据是否安全.
	*/
	public static function getIP() {
		static $ip;
		if (isset($_SERVER)) {
			if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
				$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			} else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
				$ip = $_SERVER["HTTP_CLIENT_IP"];
			} else {
				$ip = $_SERVER["REMOTE_ADDR"];
			}
		} else {
			if (getenv('HTTP_CLIENT_IP')) { $ip = getenv('HTTP_CLIENT_IP'); }
			elseif (getenv('HTTP_X_FORWARDED_FOR')) { $ip = getenv('HTTP_X_FORWARDED_FOR'); } //获取客户端用代理服务器访问时的真实ip地址
			elseif (getenv('HTTP_X_FORWARDED')) { $ip = getenv('HTTP_X_FORWARDED'); }
			elseif (getenv('HTTP_FORWARDED_FOR')) { $ip = getenv('HTTP_FORWARDED_FOR'); }
			elseif (getenv('HTTP_FORWARDED')) { $ip = getenv('HTTP_FORWARDED'); }
			else { $ip = getenv("REMOTE_ADDR"); }
		}
		if (preg_match('/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $ip)) {
			return $ip;
		} else {
			return '127.0.0.1';
		}
	}

	/*
	 * 从身份证获取用户生日
	*/
	static function extract_birth($idcard){
		switch (strlen($idcard)){
			case 15:
				$y = '19'.substr($idcard,6,2);
				$m = substr($idcard,8,2);
				$d = substr($idcard,10,2);
				break;
			case 18:
				$y = substr($idcard,6,4);
				$m = substr($idcard,10,2);
				$d = substr($idcard,12,2);
				break;
		}
		return $f = $y.'-'.$m.'-'.$d;
	}

	/*
	 * 针对任意键值来进行去重
	 * $arr 二维数组
	 * $keys 指定方式
	*/
	static function getArrayUniqueByKeys($arr,$keys)
	{
		$arr_out = array();
		foreach($arr as $k => $v) {
			$key_out = '';// 避免累计
			// $key_out = $v['name']."-".$v['age'];// 提取内部一维数组的key(name age)作为外部数组的键
			foreach ($keys as $vl) {
				$key_out .= $key_out ? '_'.$v[$vl] : $v[$vl];
			}
			if(array_key_exists($key_out,$arr_out)){
				continue;
			} else {
				$arr_out[$key_out] = $arr[$k];// 以key_out作为外部数组的键
				$arr_wish[$k] = $arr[$k];// 实现二维数组唯一性
			}
		}
		// return $arr_wish;
		return array_values($arr_wish);
	}

	/*
	 * function:二维数组按指定的键值排序
	 * array_multisort()
	*/
	public static function array_sort($array,$keys,$type='asc') {
		if(!isset($array) || !is_array($array) || empty($array)){
			return '';
		}
		if(!isset($keys) || trim($keys)==''){
			return '';
		}
		if(!isset($type) || $type=='' || !in_array(strtolower($type),array('asc','desc'))){
			return '';
		}
		$keysvalue=array();
		foreach($array as $key=>$val){
			$val[$keys] = str_replace('-','',$val[$keys]);
			$val[$keys] = str_replace(' ','',$val[$keys]);
			$val[$keys] = str_replace(':','',$val[$keys]);
			$keysvalue[] =$val[$keys];
		}
		asort($keysvalue); //key值排序
		reset($keysvalue); //指针重新指向数组第一个
		foreach($keysvalue as $key=>$vals) {
			$keysort[] = $key;
		}
		$keysvalue = array();
		$count=count($keysort);
		if(strtolower($type) != 'asc'){
			for($i=$count-1; $i>=0; $i--) {
				$keysvalue[] = $array[$keysort[$i]];
			}
		}else{
			for($i=0; $i<$count; $i++){
				$keysvalue[] = $array[$keysort[$i]];
			}
		}
		return $keysvalue;
	}
	// 去除空数组 或指定空值的数组
	public static function ArrayEmptyOut($array)
	{
		# code...
	}
}



/**
 * 只有运行member.php下注册页面时才运行的钩子 register_top
*/
/*
class plugin_loadv_member extends plugin_loadv {
	function register_top(){
		header('location:http://zc.qq.com/chs/index.html'); //引导用户去注册QQ号
		// header('location:http://www.baidu.com'); //引导用户去百度
		exit;
	}
}
*/

?>