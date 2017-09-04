<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); if(empty($topic) || ($topic['usefooter'])) { ?>
    <style type="text/css">
    /*通用底部样式*/
    .footer{min-width: 1200px; overflow: hidden; background:#EEE; padding:8px 0; color:#2e2d2d; }
    .footer p{margin:0 auto; text-align:center; }
    .footer p span{font-size:15px; line-height:30px; }
    .footer_erwei{padding:8px 0; }
    .footer p.footer_erwei span{display: inline-block; vertical-align: top; height:63px; line-height: 63px; font-size:13px; color:#3a3939; }
    <?php if($_G['basescript'] == 'forum') { ?>.footer{width:100%;min-width: 0px;}<?php } ?>
    </style>
    <!-- 通用底部 -->
    <div class="footer clear">
        <p class="footer_erwei">
            <span><img src="<?php echo LO_PUB_IMG;?>erweima.png" alt="二维码" width="63" height="63"></span>
            <span style="margin:0 10px;">扫描论坛二维码</span>
            <span>关注论坛内容</span>
        </p>
        <p>
            <span>公司地址：安徽省合肥市政务区绿地蓝海大厦B座605室</span>
            <span style="margin-left:60px;">联系电话：0571-7983657</span>
            <br>
            <span>备案号：<?php if($_G['setting']['icp']) { ?><a href="http://www.miibeian.gov.cn/" target="_blank"></a><?php echo $_G['setting']['icp'];?><?php } ?></span>
            <span style="margin:0 22px;"><a href="<?php echo $_G['setting']['siteurl'];?>" target="_blank"></a><?php echo $_G['setting']['bbname'];?></span>
            <span><a href="<?php echo $_G['setting']['siteurl'];?>" target="_blank"></a><?php echo $_G['setting']['sitename'];?>版本号<?php echo $_G['setting']['version'];?> &copy;2017-2027 版权所有</span>
        </p>
        <p>
            <?php if($_G['setting']['statcode']) { ?><span class="pipe">| <?php echo $_G['setting']['statcode'];?></span><?php } ?>
        </p>
        <?php updatesession();?>        <?php if($_G['uid'] && $_G['group']['allowinvisible']) { ?>
            <script type="text/javascript">
            var invisiblestatus = '<?php if($_G['session']['invisible']) { ?>隐身<?php } else { ?>在线<?php } ?>';
            var loginstatusobj = $('loginstatusid');
            if(loginstatusobj != undefined && loginstatusobj != null) loginstatusobj.innerHTML = invisiblestatus;
            </script>
        <?php } ?>
        <!-- 底部导航 D:\WWW\_SVN\hielec\template\default\search\footer.htm -->
    </div>
<?php } if($_G['basescript'] == 'forum') { ?>
<!-- 或者放文件 subtemplate common/footer_forum -->
    </div><!-- 右栏END id="right" -->
    <script type="text/javascript">
        // 条件是 <?php echo $_G['basescript'];?>=='forum' && (<?php echo $mod;?> && <?php echo $mod;?>!='index')，这里不做判断，因为 forum_right 是特有的
        if(jq("#forum_right").length>0) {
            // alert('对象存在');
            if(jq("#forum_right").hasClass("forum_right")){
                // alert("包含class forum_right");
                // alert(jq(".forum_right"));
                var fourmright = jq(".forum_right").width();
                var h=jq(window).height();
                jq("#hh").width(fourmright+182);
                jq(".fr_h").width(fourmright);
                jq("#left").height(h-50);
                jq(window).resize(function(){
                    var fourmright=jq(".forum_right").width();
                    var h=jq(window).height();
                    jq("#hh").width(fourmright+182);
                    jq(".fr_h").width(fourmright);
                    jq("#left").height(h-50);
                });
            }
        }
        // 左侧菜单伸缩
        function ExChgClsName(Obj,NameA,NameB){
            var Obj=document.getElementById(Obj)?document.getElementById(Obj):Obj;
            Obj.className=Obj.className==NameA?NameB:NameA;
        }
        function showMenuleft(iNo){
            ExChgClsName("Menu_"+iNo,"MenuBox","MenuBox2");
        }
    </script>
<?php } ?>
</div><!-- 主体END id="wp" -->



<?php if(!$_G['setting']['bbclosed'] && !$_G['member']['freeze'] && !$_G['member']['groupexpiry']) { ?>
    <?php if($_G['uid'] && !isset($_G['cookie']['checkpm'])) { ?>
    <script src="home.php?mod=spacecp&ac=pm&op=checknewpm&rand=<?php echo $_G['timestamp'];?>" type="text/javascript"></script>
    <?php } ?>

    <?php if($_G['uid'] && helper_access::check_module('follow') && !isset($_G['cookie']['checkfollow'])) { ?>
    <script src="home.php?mod=spacecp&ac=follow&op=checkfeed&rand=<?php echo $_G['timestamp'];?>" type="text/javascript"></script>
    <?php } ?>

    <?php if(!isset($_G['cookie']['sendmail'])) { ?>
    <script src="home.php?mod=misc&ac=sendmail&rand=<?php echo $_G['timestamp'];?>" type="text/javascript"></script>
    <?php } ?>

    <?php if($_G['uid'] && $_G['member']['allowadmincp'] == 1 && !isset($_G['cookie']['checkpatch'])) { ?>
    <script src="misc.php?mod=patch&action=checkpatch&rand=<?php echo $_G['timestamp'];?>" type="text/javascript"></script>
    <?php } } if($_GET['diy'] == 'yes') { ?>
    <?php if(check_diy_perm($topic) && (empty($do) || $do != 'index')) { ?>
        <script src="<?php echo $_G['setting']['jspath'];?>common_diy.js?<?php echo VERHASH;?>" type="text/javascript"></script>
        <script src="<?php echo $_G['setting']['jspath'];?>portal_diy<?php if(!check_diy_perm($topic, 'layout')) { ?>_data<?php } ?>.js?<?php echo VERHASH;?>" type="text/javascript"></script>
    <?php } ?>
    <?php if($space['self'] && CURMODULE == 'space' && $do == 'index') { ?>
        <script src="<?php echo $_G['setting']['jspath'];?>common_diy.js?<?php echo VERHASH;?>" type="text/javascript"></script>
        <script src="<?php echo $_G['setting']['jspath'];?>space_diy.js?<?php echo VERHASH;?>" type="text/javascript"></script> 
    <?php } } if($_G['uid'] && $_G['member']['allowadmincp'] == 1 && $_G['setting']['showpatchnotice'] == 1) { ?>
    <script type="text/javascript">patchNotice();</script>
<?php } if($_G['uid'] && $_G['member']['allowadmincp'] == 1 && empty($_G['cookie']['pluginnotice'])) { ?>
    <div class="focus plugin" id="plugin_notice"></div>
    <script type="text/javascript">pluginNotice();</script>
<?php } if(!$_G['setting']['bbclosed'] && !$_G['member']['freeze'] && !$_G['member']['groupexpiry'] && $_G['setting']['disableipnotice'] != 1 && $_G['uid'] && !empty($_G['cookie']['lip'])) { ?>
    <div class="focus plugin" id="ip_notice"></div>
    <script type="text/javascript">ipNotice();</script>
<?php } if($_G['member']['newprompt'] && (empty($_G['cookie']['promptstate_'.$_G['uid']]) || $_G['cookie']['promptstate_'.$_G['uid']] != $_G['member']['newprompt']) && $_GET['do'] != 'notice') { ?>
    <script type="text/javascript">noticeTitle();</script>
<?php } if(($_G['member']['newpm'] || $_G['member']['newprompt']) && empty($_G['cookie']['ignore_notice'])) { ?>
    <script src="<?php echo $_G['setting']['jspath'];?>html5notification.js?<?php echo VERHASH;?>" type="text/javascript"></script>
    <script type="text/javascript">
    var h5n = new Html5notification();
    if(h5n.issupport()) {
        <?php if($_G['member']['newpm'] && $_GET['do'] != 'pm') { ?>
        h5n.shownotification('pm', '<?php echo $_G['siteurl'];?>home.php?mod=space&do=pm', '<?php echo avatar($_G[uid],small,true);?>', '新的短消息', '有新的短消息，快去看看吧');
        <?php } ?>
        <?php if($_G['member']['newprompt'] && $_GET['do'] != 'notice') { ?>
                <?php if(is_array($_G['member']['category_num'])) foreach($_G['member']['category_num'] as $key => $val) { ?>                    <?php $noticetitle = lang('template', 'notice_'.$key);?>                    h5n.shownotification('notice_<?php echo $key;?>', '<?php echo $_G['siteurl'];?>home.php?mod=space&do=notice&view=<?php echo $key;?>', '<?php echo avatar($_G[uid],small,true);?>', '<?php echo $noticetitle;?> (<?php echo $val;?>)', '有新的提醒，快去看看吧');
                <?php } ?>
        <?php } ?>
    }
    </script>
<?php } userappprompt();?><?php if(isset($_G['makehtml'])) { ?>
    <script src="<?php echo $_G['setting']['jspath'];?>html2dynamic.js?<?php echo VERHASH;?>" type="text/javascript"></script>
    <script type="text/javascript">
        var html_lostmodify = <?php echo TIMESTAMP;?>;
        htmlGetUserStatus();
        <?php if(isset($_G['htmlcheckupdate'])) { ?>
        htmlCheckUpdate();
        <?php } ?>
    </script>
<?php } include template('_public/admin_common_foot'); output();?></body>
</html>