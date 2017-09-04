<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('index_pro');?><?php include template('loproject:layout/header'); ?><link rel="stylesheet" href="<?php echo LO_PUB_CSS;?>project.css">

<section>
    <div class="lf" id="left">
        <div id="tjcx" class="f_down_l">
            <ul id='tj' class="f_down_t">
                <li class="have_m f_down_t1">
                    <span class="head lf">应用领域：</span>
                    <ul class="f_down_tc f_down_tc1">
                        <div class="f_down_tc1h">
                            <li class="lf"><a class="<?php if(is_null($aa) || $aa==-1) { ?>on<?php } ?>" href="<?php echo $jumpurl;?>&aa=-1">全部</a></li>
                            <?php if(is_array($industrys)) foreach($industrys as $v) { ?>                            <li class="lf"><a class="<?php if($aa==$v['cid']) { ?>on<?php } ?>" href="<?php echo $jumpurl;?>&aa=<?php echo $v['cid'];?>"><?php echo $v['name'];?></a></li>
                            <?php } ?>
                        </div>
                    </ul>
                    <div class="luntan_more">更多<i class="fy_downjt"></i></div>
                </li>
                <li class="have_m f_down_t1">
                    <span class="head lf">任务状态：</span>
                    <ul class="f_down_tc f_down_tc1">
                        <div class="f_down_tc1h">
                            <li class="lf"><a class="<?php if(is_null($bb) || $bb==-1) { ?>on<?php } ?>" href="<?php echo $jumpurl;?>&bb=-1">全部</a></li>
                            <?php if(is_array($arrProTaskStatus)) foreach($arrProTaskStatus as $k => $v) { ?>                            <li class="lf"><a class="<?php if($bb===$k) { ?>on<?php } ?>" href="<?php echo $jumpurl;?>&bb=<?php echo $k;?>"><?php echo $v;?></a></li>
                            <?php } ?>
                        </div>
                    </ul>
                    <div class="luntan_more">更多<i class="fy_downjt"></i></div>
                </li>
                <li class="have_m f_down_t1">
                    <span class="head lf">托管状态：</span>
                    <ul class="f_down_tc f_down_tc1">
                        <div class="f_down_tc1h">
                            <li class="lf"><a class="<?php if(is_null($cc) || $cc==-1) { ?>on<?php } ?>" href="<?php echo $jumpurl;?>&cc=-1">全部</a></li>
                            <?php if(is_array($arrProTrustStatus)) foreach($arrProTrustStatus as $k => $v) { ?>                            <li class="lf"><a class="<?php if($cc===$k) { ?>on<?php } ?>" href="<?php echo $jumpurl;?>&cc=<?php echo $k;?>"><?php echo $v;?></a></li>
                            <?php } ?>
                        </div>
                    </ul>
                    <div class="luntan_more">更多<i class="fy_downjt"></i></div>
                </li>
                <li class="have_m f_down_t1">
                    <span class="head lf">任务类型：</span>
                    <ul class="f_down_tc f_down_tc1">
                        <div class="f_down_tc1h">
                            <li class="lf"><a class="<?php if(!$dd) { ?>on<?php } ?>" href="<?php echo $jumpurl;?>&dd=">全部</a></li>
                            <?php if(is_array($arrProTaskType)) foreach($arrProTaskType as $k => $v) { ?>                            <li class="lf"><a class="<?php if($dd==$k) { ?>on<?php } ?>" href="<?php echo $jumpurl;?>&dd=<?php echo $k;?>"><?php echo $v;?></a></li>
                            <?php } ?>
                        </div>
                    </ul>
                    <div class="luntan_more">更多<i class="fy_downjt"></i></div>
                </li>
                <li class="have_m f_down_t1">
                    <span class="head lf">发布时间：</span>
                    <ul class="f_down_tc f_down_tc1">
                        <div class="f_down_tc1h">
                            <li class="lf"><a class="<?php if(!$ee) { ?>on<?php } ?>" href="<?php echo $jumpurl;?>&ee=">全部</a></li>
                            <?php if(is_array($arrPublishTime)) foreach($arrPublishTime as $k => $v) { ?>                            <li class="lf"><a class="<?php if($ee==$k) { ?>on<?php } ?>" href="<?php echo $jumpurl;?>&ee=<?php echo $k;?>"><?php echo $v;?></a></li>
                            <?php } ?>
                        </div>
                    </ul>
                    <div class="luntan_more">更多<i class="fy_downjt"></i></div>
                </li>
                <li class="have_m f_down_t1">
                    <span class="head lf">发布区域：</span>
                    <ul class="f_down_tc f_down_tc1">
                        <div class="f_down_tc1h">
                            <li class="lf"><a class="<?php if(!$ff) { ?>on<?php } ?>" href="<?php echo $jumpurl;?>&ff=">全部</a></li>
                            <?php if(is_array($district_prov)) foreach($district_prov as $v) { ?>                            <li class="lf"><a class="<?php if($ff==$v['id']) { ?>on<?php } ?>" href="<?php echo $jumpurl;?>&ff=<?php echo $v['id'];?>"><?php echo $v['name'];?></a></li>
                            <?php } ?>
                        </div>
                    </ul>
                    <div class="luntan_more">更多<i class="fy_downjt"></i></div>
                </li>
                <li class="have_m f_down_t1">
                    <span class="head lf">任务赏金：</span>
                    <ul class="f_down_tc f_down_tc1">
                        <div class="f_down_tc1h">
                            <li class="lf"><a class="<?php if(!$gg) { ?>on<?php } ?>" href="<?php echo $jumpurl;?>&gg=">全部</a></li>
                            <?php if(is_array($arrProTaskCash)) foreach($arrProTaskCash as $k => $v) { ?>                                <?php if($k==1) { ?>
                                <li class="lf"><a class="<?php if($gg==$k) { ?>on<?php } ?>" href="<?php echo $jumpurl;?>&gg=<?php echo $k;?>"><?php echo $v;?>元以内</a></li>
                                <?php } elseif($k>1 && $k<=5) { ?>
                                <li class="lf"><a class="<?php if($gg==$k) { ?>on<?php } ?>" href="<?php echo $jumpurl;?>&gg=<?php echo $k;?>"><?php echo $v;?>元</a></li>
                                <?php } else { ?>
                                <li class="lf"><a class="<?php if($gg==$k) { ?>on<?php } ?>" href="<?php echo $jumpurl;?>&gg=<?php echo $k;?>"><?php echo $v;?>元以上</a></li>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </ul>
                    <div class="luntan_more">更多<i class="fy_downjt"></i></div>
                </li>
            </ul>
            <?php include template('loproject:layout/search_form'); ?>        </div>

        <div class="clear"></div>

        <div id="list">
            <ul>
                <?php if($list) { ?>
                <?php if(is_array($list)) foreach($list as $v) { ?>                <li>
                    <div class="xmxx lf">
                        <p>
                            <?php if($v['task_cash']==0.00 && $v['budget']) { ?>
                            明确预算：<span class="price"><?php echo $v['budget'];?></span>
                            <?php } else { ?>
                            任务赏金：<span class="price"><?php echo $v['task_cash'];?></span>
                            <?php } ?>
                            <span class="xm"><a style="color:#321;" href="<?php echo $mainurl;?>&pluginop=pds&loid=<?php echo $v['tid'];?>"><b><?php echo $v['task_title'];?></b></a></span>
                            <span class="zt"><?php echo $arrProTrustStatus[$v['is_trust']];?></span>
                        </p>
                        <p>
                            <span class="ly">应用领域：<?php if($v['name']) { ?><?php echo $v['name'];?><?php } else { ?>未知<?php } ?></span>
                            <span class="rm">发包方：<?php echo $v['username'];?></span>
                            <span class="rm">发包时间：<?php echo $v['pub_time'];?></span>
                        </p>
                    </div>
                    <div class="rt">
                        <?php echo $v['bid_num'];?><span class="num">人参与 | 招标</span>
                        <span class="xbz"><?php echo $arrProTaskStatus[$v['task_status']];?></span>
                    </div>
                </li>
                <?php } ?>
                <?php } else { ?>
                <li><?php echo $lang['no_resource'];?></li>
                <?php } ?>
            </ul>
            <?php echo $multipage;?>
        </div>
    </div>
    
    <div class="rt" id="right">
        <?php include template('loproject:layout/right_column'); ?>    </div>

    <div class="clear"></div>
    <div id="fy"></div>
</section>

<script type="text/javascript">
   $(document).delegate('.luntan_more','click',function(){
            $(this).siblings().children().removeClass('f_down_tc1h');
            $(this).html('收起<i class="fy_downjt1"></i>').addClass('luntan_shouqi').removeClass('luntan_more');
        })
        $(document).delegate('.luntan_shouqi','click',function(){
            $(this).siblings('.f_down_tc').children().addClass('f_down_tc1h');
            $(this).addClass('luntan_more').html('更多<i class="fy_downjt"></i>').removeClass('luntan_shouqi');
        })
</script>

<!-- 通用底部 --><?php include template('common/footer'); ?>