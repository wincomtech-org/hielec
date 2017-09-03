<?php
	
	if(IS_GET){
		$id = $_REQUEST['loid'];
		$first = cache_list_table('down','id,cid,name,size,tags,views,price,brief,author_id,buys,addtime','recycle=\'\' and is_show=1 and (status=2 || status=6)','id asc',20);

	}

	include template(THISPLUG.':index');
?>