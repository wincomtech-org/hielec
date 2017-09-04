<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('header');?><?php include template('common/header'); ?><link rel="stylesheet" href="<?php echo LO_PUB_CSS;?>head_nav.css">
<script src="<?php echo LO_PUB_JS;?>jquery-1.7.2.min.js" type="text/javascript"></script>

<!-- 项目导航 --><?php include template('loproject:layout/head_nav'); ?><!-- 面包屑 -->
<?php echo $ur_here;?>