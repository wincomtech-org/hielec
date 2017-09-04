<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>
<style type="text/css">
/* nav_main 主导航*/
#hh{ top:0;left:0; width:100%;background:#fff;}
.forum_head{width:100%;background:#2d3e50;height:50px;}
.forum_h{width:100%;margin:0 auto;overflow:hidden;}
#forum_nav li{padding:0 25px;color:#d2d0d0;line-height:50px;float:left;}
#forum_nav li a{color:#d2d0d0;font-size:14px;line-height: 50px;}
#forum_nav .active a{color:#ba212b;}
.forum_h span{color:#d2d0d0;}
.forum_h span a{color:#d2d0d0;font-size:14px;line-height:50px;}
/*图样式*/
#logo{width:1200px;margin:30px auto 25px;overflow:hidden;height:65px;}
.logo{width:230px;}
.logo img{width:100%;}
.ad{ /* background: #0C9; */ width: 50%; margin: 0 auto; overflow: hidden;}
.ad img{/*width:600px;height:40px;*/}
.forum_con{  }
.forum_left{ width:163px;border-size:border-box;overflow: hidden; }
.xi2, .xi2 a, .xi3 a{ color: #369; }
.xg1, .xg1 a{ color: #999 !important; }
<?php if(CURSCRIPT != 'forum') { ?>
#hh{min-width: 1200px;}
.forum_h{width:1200px;}
<?php } ?>
</style>
<!-- 顶部导航 -->
<div id="hh" style="">
    <div class="forum_head">
        <div class="forum_h">
            <ul id="forum_nav" class="lf" >
                <?php if(is_array($_G['setting']['navs'])) foreach($_G['setting']['navs'] as $nav) { ?>                <?php if($nav['available'] && (!$nav['level'] || ($nav['level'] == 1 && $_G['uid']) || ($nav['level'] == 2 && $_G['adminid'] > 0) || ($nav['level'] == 3 && $_G['adminid'] == 1))) { ?><li class="<?php if($mnid==$nav['navid']) { ?>active<?php } ?> <?php if ($_G['basefilename']!='plugin.php' && stripos($nav['filename'],$_G['basefilename'])!==false) {?>active<?php }?>" <?php echo $nav['nav'];?>></li><?php } ?>
                <?php } ?>
            </ul>
            <?php if($_G['uid']) { ?>
            <span class="rt">
                <!-- <a href="home.php?mod=uc&amp;do=base&amp;uid=1">用户名</a>&nbsp;丨&nbsp; -->
                <a href="home.php?mod=space&amp;do=profile&amp;uid=<?php echo $_G['uid'];?>"><?php echo $_G['member']['username'];?></a>&nbsp;丨&nbsp;
                <a href="member.php?mod=logging&amp;action=logout&amp;formhash=<?php echo FORMHASH;?>">退出</a>&nbsp;&nbsp;&nbsp;
            </span>
            <?php } else { ?>
            <span class="rt"><a href="member.php?mod=logging&amp;action=login">登录</a>&nbsp;丨&nbsp;<a href="member.php?mod=register">注册</a>&nbsp;&nbsp;&nbsp;</span> 
            <?php } ?>
        </div>
    </div>
    <?php if($_G['basescript']!='forum') { ?>
    <div id="logo">
        <div class="lf logo" ><?php if(!isset($_G['setting']['navlogos'][$mnid])) { ?><a href="<?php if($_G['setting']['domain']['app']['default']) { ?>http://<?php echo $_G['setting']['domain']['app']['default'];?>/<?php } else { ?>./<?php } ?>" title="<?php echo $_G['setting']['bbname'];?>"><?php echo $_G['style']['boardlogo'];?></a><?php } else { ?><?php echo $_G['setting']['navlogos'][$mnid];?><?php } ?></div>
        <div class="rt ad" style="margin-right:100px;">文字广告</div>
    </div>
    <?php } ?>
</div>