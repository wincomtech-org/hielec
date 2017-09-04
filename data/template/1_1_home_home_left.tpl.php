<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>

<!-- 外部样式文件会优先加载进来 -->
<link rel="stylesheet" href="<?php echo LO_PUB_CSS;?>talents.css"/>
<style type="text/css">
    .uc_active{color: orange!important}
    /*a{color: orange}*/
</style>
<!-- 重新引入JQ -->
<script src="<?php echo LO_PUB_JS;?>jquery-1.11.3.min.js" type="text/javascript"></script>
<!-- 引入其他js -->
<script src="<?php echo LO_PUB_JS;?>common.js" type="text/javascript"></script>
<!-- 这里可能写的是第二遍~~ 必须依赖于jq，然第一次jq标识符释放了-->
<link type="text/css" rel="stylesheet" href="/source/plugin/_public/org/validform/validform.css">
<script src="/source/plugin/_public/org/validform/Validform_v5.3.2_min.js" type="text/javascript" type="text/javascript"></script>

<!-- 论坛左侧导航 -->
<div class="person_homel lf">
    <?php if(is_array($arrUCMenu)) foreach($arrUCMenu as $k => $menu) { ?>    <div class="phl_ectocyst phl_t">
        <h2><span class="ph ph_<?php echo $menu['ico'];?>"></span><?php echo $menu['name'];?></h2>
        <ul class="phl_ectocyst_ul phl_tul">
            <?php if(is_array($menu['sub'])) foreach($menu['sub'] as $s => $sub) { ?>            <li><a href="<?php echo $sub['href'];?>" <?php if(strpos($sub['href'],$jumpurl)!==false) { ?>class="uc_active"<?php } ?> <?php echo $sub['attr'];?>><?php echo $sub['name'];?></a></li>
            <?php } ?>
        </ul>
    </div>
    <?php } ?>
</div>