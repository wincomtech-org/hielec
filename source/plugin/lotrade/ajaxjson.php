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
            <input type="radio" style="float:left;" value="'.$value[id].'" name="address">
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
		<input type="radio" style="float:left;" value="'.$arrdefault[id].'" checked="checked" name="address">
        <div class="addr_list_l lf">
           <p class="addr_tit"><span class="addr_list_lname">姓名</span><span class="rt addr_list_ltel">'.$arrdefault[username].'</span></p>
           <p class="addr_detail">'.$arrdefault[username].'</p>
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
}
elseif ($sign=='col'){
	$data = array();
	$data['type']='col';
	$data['brief']='收藏活动';
	$data['uid']=$_G['uid'];
	$data['tid']=$_G['group'][groupid] ;
	$data['ip']=CURIP;
	$data['tradeid']=$_POST['did'];
	$data['addtime']=time();		
	$insert_id = DB::insert('trade_log',$data,true);
	if (!$insert_id) {
		echo '添加失败';
		die();
	}else{			
		echo '收藏成功';
		die();
	}
}
elseif($sign=='delcol'){

	$del=DB::delete('trade_log'," tradeid = '$_POST[did]' and uid='$_G[uid]' ");
	if($del){
		echo '取消收藏';
		die();
	}else{			
		echo '取消失败';
		die();
	}
}
// elseif($sign=="gift_content"){
// 	$arrorder = $arrRecent = DB::fetch_all(sprintf("SELECT a.id,b.gid,b.pic,b.name,b.price FROM %s as a LEFT JOIN %s as b ON a.gid=b.gid WHERE a.type='%s' AND a.uid=%d and a.is_show=1 and a.recycle='' ",DB::table('activity_log'),DB::table('activity_gift'),'exchange',$_G['uid']));
// 	foreach ($arrorder as $key => $value) {
		
// 		echo '<tr class="tr">
//                                 <td class="f_check"><input type="checkbox" class="ck"/></td>
//                                 <td class="tc">
//                                     <img src="image/user.png" />
//                                 </td>
//                                 <td>
//                                     希特新锐3TB移动硬盘
//                                 </td>
//                                 <td>
//                                     <span class="icbcost">100</span>IC币
//                                 </td>
//                                 <td>1299.00元</td>
//                                 <td>
//                                     <div class="count_content">
//                                         <span class="subtract" >-</span>
//                                         <input type="text" class="count" name="nums[]" value="1" old="1" disabled="disabled"/>
//                                         <span class="add">+</span>
//                                     </div>
//                                 </td>
//                                 <td>无优惠</td>
//                                 <td><span class="icb">100</span>IC币</td>
//                                 <td><a href="javascript:;" class="remove red" >删除</a></td>
//                             </tr>';
// 	}
// 	 die();
// }
// elseif($sign=='dellog'){
// 	$str=DB::update('activity_log', array('is_show'=>'0'), array('id'=>$_POST['did']));
// 	if($str){
// 		echo "删除成功";
// 		die();
// 	}else{
// 		echo "删除失败";
// 		die();
// 	}	
// }

?>
