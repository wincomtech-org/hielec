<?php
class page{
		//获取共有多少条数据
		function get_rowCount($rowCount){
			$conn = mysql_connect('localhost','root','root') or die("链接数据库失败!");
			mysql_query("set names utf8");
			mysql_select_db('hielec',$conn);

			$res = mysql_query("SELECT count(id) from down");
			if($row = mysql_fetch_row($res)){
				$rowCount = $row[0];
			}
			@mysql_free_result($res);
			mysql_close($conn);	
			return $rowCount;
		}


		//当前所在页面
		function get_pageNow($pageNow = 1){
			if(!empty($_GET['pageNow'])){
				$pageNow = $_GET['pageNow'];
			}
			return $pageNow;
		}
}
?>