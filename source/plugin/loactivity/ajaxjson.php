<?php
 if(!defined('IN_DISCUZ'))
{
	exit('Access Denied');
}
$sign=$_POST['sign'];
$fields = '*';
if($sign=='addaddress'){
	if ($_POST['username']=="") {
		echo "收件人姓名不能为空！";
		die();
	}elseif($_POST['address']==""){
		echo "收件地址不能为空！";
		die();
	}elseif ($_POST['phone']=="") {
		echo "收件人电话不能为空！";
		die();
	}
	unset($_POST['sign']);
	unset($_POST['pluginop']);
	$_POST['uid']=$_G['uid'];
	// print_r($_POST);
	$insert_id = DB::insert('address',$_POST,true);
	if ($insert_id) {
		echo '添加成功';
		die();
	}	
}

elseif($sign=='arraddress'){
	$arraddress = DB::fetch_all("select ". $fields ." from ".DB::table('address')." where uid=".$_G['uid']." and is_default=0 and is_show=1 and recycle='' ");
	

	foreach ($arraddress as $key => $value) {
		echo '
            <li class="addr_list">
            <input type="radio" style="float:left;" name="address" value="'.$value[id].'">
            <div class="addr_list_l lf">
	            <p class="addr_tit"><span class="addr_list_lname">姓名</span><span class="rt addr_list_ltel">'.$value[username].'</span></p>
	            <p class="addr_detail">'.$value[address].'</p>
            </div>
            <div class="addr_list_r rt">
	            <span class="reset_moren" style="cursor:pointer;" onclick="defadd('.$value[id].')">设为默认地址</span>
	            <span class="add_remove" style="cursor:pointer;" onclick="delarr('.$value[id].')">
	            删除            
	            </span>
            </div>
            </li>';
        }
        die();

}elseif ($sign=='arrdefault') {	
	$arrdefault = DB::fetch_first("select ". $fields ." from ".DB::table('address')." where uid=".$_G['uid']." and is_default=1 and is_show=1 and recycle='' ");	
		echo '
		<li class="addr_list">
		<input type="radio" style="float:left; value="'.$arrdefault[id].'" checked="checked" name="address">
        <div class="addr_list_l lf">
           <p class="addr_tit"><span class="addr_list_lname">姓名</span><span class="rt addr_list_ltel">'.$arrdefault[username].'</span></p>
           <p class="addr_detail">'.$arrdefault[address].'</p>
        </div>
        <div class="addr_list_r rt">
            <span class="add_remove" style="cursor:pointer;" onclick="delarr('.$arrdefault[id].')">
            删除            
            </span>
        </div>
        </li>';
	
	die();
}elseif ($sign=='delarr') {
	
	$str=DB::update('address', array('is_show'=>'0'), array('id'=>$_POST['did']));
	if($str){
		echo "删除成功";
		die();
	}else{
		echo "删除失败";
		die();
	}
}elseif ($sign=='defadd') {
	DB::update('address', array('is_default'=>'0'), array());
	$str=DB::update('address', array('is_default'=>'1'), array('id'=>$_POST['did']));
	if($str){
		echo "修改成功";
		die();
	}else{
		echo "修改失败";
		die();
	}
}elseif ($sign=='addnum') {
	$str=DB::update('activity_log', array('number'=>$_POST['num']), array('id'=>$_POST['orderid']));
	die();
}elseif ($sign=='minusnum') {
	$str=DB::update('activity_log', array('number'=>$_POST['num']), array('id'=>$_POST['orderid']));
	die();
}elseif ($sign=="col") {
	# code...
	
	// $pag = DB::fetch_first("select * from ".DB::table('activity_log')." where gid=".$_POST['did']." and uid=".$_G['uid']." and type ='col' ");
	$pag = DB::result_first(sprintf("SELECT count(*) FROM %s WHERE type='%s' AND uid=%d AND gid=%d ",DB::table('activity_log'),'col',$_G[uid],$_POST['did']));
	if($pag){
		echo '您已收藏该活动';
		die();
	}
	else{
		$data['type']='col';
		$data['brief']='收藏活动';
		$data['uid']=$_G['uid'];
		$data['tid']=$_G['group'][groupid] ;
		$data['ip']=CURIP;
		$data['gid']=$_POST['did'];
		$data['addtime']=time();		
		$insert_id = DB::insert('activity_log',$data,true);
		if (!$insert_id) {
			echo '添加失败';
			die();
		}else{			
			echo '收藏成功';
			die();
		}
	// break;
	}
}

?>
