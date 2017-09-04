<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>
<section>
    <div class="title">
        <ul class="nav_t">
            <li class="lf <?php if($ahr['cur']=='talent') { ?>on<?php } ?>"><a href="<?php echo $mainurl;?>&pluginop=talent">人才大厅</a></li>
            <li class="lf <?php if($ahr['cur']=='pro') { ?>on<?php } ?>"><a href="<?php echo $mainurl;?>">项目市场</a></li>
            <!-- <li class="lf <?php if($ahr['cur']=='help') { ?>on<?php } ?>"><a href="<?php echo $mainurl;?>&pluginop=help">帮助</a></li> -->
        </ul>
    </div>
</section>