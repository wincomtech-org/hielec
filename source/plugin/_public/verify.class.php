<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
class Verify {
    var $images_dir;
    var $thumb_dir;
    var $upfile_type; // 上传的类型，默认为：jpg gif png rar zip
    var $upfile_size_max; // 上传大小限制，单位是“KB”，默认为：2048KB
    var $to_file = true; // $this->to_file设定为false时将以原图文件名创建缩略图
    // const group = "";// 静态常量
    
    /**
     * +----------------------------------------------------------
     * 构造函数
     * $images_dir 文件上传路径
     * $thumb_dir 缩略图路径
     * +----------------------------------------------------------
     */
    function Verify() {
        $this->images_dir = $images_dir; // 文件上传路径 结尾加斜杠
        $this->thumb_dir = $thumb_dir; // 缩略图路径（相对于$images_dir） 结尾加斜杠，留空则跟$images_dir相同
        $this->upfile_type = $upfile_type;
        $this->upfile_size_max = $upfile_size_max;
    }

}
?>