<?php
	
	if(IS_GET){
		$id = $_REQUEST['goods_ID'];
		$where = "recycle='' and is_show=1 and (status=2 || status=6) and id= '$id' ";
		$second = cache_list_table('down','id,cid,name,size,tags,pic,views,price,brief,author_id,buys,addtime',$where,'id asc',20);
		var_dump($second);die;
		foreach($second as $k=>$v){
			if($v['id'] == $id){
				$goods = $v;
			}
		}
	}

	
?>