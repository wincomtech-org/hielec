<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); 
0
|| checktplrefresh('./template/default/common/header.htm', './template/default/common/header_common.htm', 1504324687, '1', './data/template/1_1_common_header_home_trade.tpl.php', './template/default', 'common/header_home_trade')
|| checktplrefresh('./template/default/common/header.htm', './template/default/common/header_style.htm', 1504324687, '1', './data/template/1_1_common_header_home_trade.tpl.php', './template/default', 'common/header_home_trade')
|| checktplrefresh('./template/default/common/header.htm', './template/default/common/header_forum.htm', 1504324687, '1', './data/template/1_1_common_header_home_trade.tpl.php', './template/default', 'common/header_home_trade')
|| checktplrefresh('./template/default/common/header.htm', './template/default/common/header_qmenu.htm', 1504324687, '1', './data/template/1_1_common_header_home_trade.tpl.php', './template/default', 'common/header_home_trade')
|| checktplrefresh('./template/default/common/header.htm', './template/default/common/pubsearchform.htm', 1504324687, '1', './data/template/1_1_common_header_home_trade.tpl.php', './template/default', 'common/header_home_trade')
;?>
<!-- 如果含有系统原有HTML 则使用 该头 --><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>" />
<?php if($_G['config']['output']['iecompatible']) { ?><meta http-equiv="X-UA-Compatible" content="IE=EmulateIE<?php echo $_G['config']['output']['iecompatible'];?>" /><?php } ?>
<title><?php if(!empty($SEO['title'])) { ?><?php echo $SEO['title'];?> - <?php } elseif(!empty($navtitle)) { ?><?php echo $navtitle;?> - <?php } if(empty($nobbname)) { ?> <?php echo $_G['setting']['bbname'];?> - <?php } ?></title>
<?php echo $_G['setting']['seohead'];?>

<meta name="keywords" content="<?php if($metakeywords) { echo dhtmlspecialchars($metakeywords); } ?>" />
<meta name="description" content="<?php if($metadescription) { ?> <?php echo dhtmlspecialchars($metadescription); ?> <?php } if(empty($nobbname)) { ?> ,<?php echo $_G['setting']['bbname'];?> <?php } ?>" />
<meta name="generator" content="Lothar! <?php echo $_G['setting']['version'];?>" />
<meta name="author" content="Lothar! Team and Wincomtech UI Team" />
<meta name="copyright" content="2016-2030 Wincomtech Inc." />
<meta name="MSSmartTagsPreventParsing" content="True" />
<meta http-equiv="MSThemeCompatible" content="Yes" />
<base href="<?php echo $_G['siteurl'];?>" /><link rel="stylesheet" type="text/css" href="data/cache/style_1_common.css?<?php echo VERHASH;?>" /><?php if($_G['uid'] && isset($_G['cookie']['extstyle']) && strpos($_G['cookie']['extstyle'], TPLDIR) !== false) { ?><link rel="stylesheet" id="css_extstyle" type="text/css" href="<?php echo $_G['cookie']['extstyle'];?>/style.css" /><?php } elseif($_G['style']['defaultextstyle']) { ?><link rel="stylesheet" id="css_extstyle" type="text/css" href="<?php echo $_G['style']['defaultextstyle'];?>/style.css" /><?php } ?><script type="text/javascript">
// 定义总变量
var STYLEID = '<?php echo STYLEID;?>',
STATICURL = '<?php echo STATICURL;?>',
IMGDIR = '<?php echo IMGDIR;?>', 
VERHASH = '<?php echo VERHASH;?>', 
charset = '<?php echo CHARSET;?>', 
discuz_uid = '<?php echo $_G['uid'];?>', 
cookiepre = '<?php echo $_G['config']['cookie']['cookiepre'];?>', 
cookiedomain = '<?php echo $_G['config']['cookie']['cookiedomain'];?>', 
cookiepath = '<?php echo $_G['config']['cookie']['cookiepath'];?>', 
showusercard = '<?php echo $_G['setting']['showusercard'];?>', 
attackevasive = '<?php echo $_G['config']['security']['attackevasive'];?>', 
disallowfloat = '<?php echo $_G['setting']['disallowfloat'];?>', 
creditnotice = '<?php if($_G['setting']['creditnotice']) { ?><?php echo $_G['setting']['creditnames'];?><?php } ?>', 
defaultstyle = '<?php echo $_G['style']['defaultextstyle'];?>', 
REPORTURL = '<?php echo $_G['currenturl_encode'];?>', 
SITEURL = '<?php echo $_G['siteurl'];?>', 
JSPATH = '<?php echo $_G['setting']['jspath'];?>', 
CSSPATH = '<?php echo $_G['setting']['csspath'];?>', 
DYNAMICURL = '<?php echo $_G['dynamicurl'];?>';
</script>
<script src="<?php echo $_G['setting']['jspath'];?>common.js?<?php echo VERHASH;?>" type="text/javascript"></script>
<?php if(empty($_GET['diy'])) { $_GET['diy'] = '';?><?php } if(!isset($topic)) { $topic = array();?><?php } ?>
    <meta name="application-name" content="<?php echo $_G['setting']['bbname'];?>" />
    <meta name="msapplication-tooltip" content="<?php echo $_G['setting']['bbname'];?>" />
    <?php if($_G['setting']['portalstatus']) { ?><meta name="msapplication-task" content="name=<?php echo $_G['setting']['navs']['1']['navname'];?>;action-uri=<?php echo !empty($_G['setting']['domain']['app']['portal']) ? 'http://'.$_G['setting']['domain']['app']['portal'] : $_G['siteurl'].'portal.php'; ?>;icon-uri=<?php echo $_G['siteurl'];?><?php echo IMGDIR;?>/portal.ico" /><?php } ?>
    <meta name="msapplication-task" content="name=<?php echo $_G['setting']['navs']['2']['navname'];?>;action-uri=<?php echo !empty($_G['setting']['domain']['app']['forum']) ? 'http://'.$_G['setting']['domain']['app']['forum'] : $_G['siteurl'].'forum.php'; ?>;icon-uri=<?php echo $_G['siteurl'];?><?php echo IMGDIR;?>/bbs.ico" />
    <?php if($_G['setting']['groupstatus']) { ?><meta name="msapplication-task" content="name=<?php echo $_G['setting']['navs']['3']['navname'];?>;action-uri=<?php echo !empty($_G['setting']['domain']['app']['group']) ? 'http://'.$_G['setting']['domain']['app']['group'] : $_G['siteurl'].'group.php'; ?>;icon-uri=<?php echo $_G['siteurl'];?><?php echo IMGDIR;?>/group.ico" /><?php } ?>
    <?php if(helper_access::check_module('feed')) { ?><meta name="msapplication-task" content="name=<?php echo $_G['setting']['navs']['4']['navname'];?>;action-uri=<?php echo !empty($_G['setting']['domain']['app']['home']) ? 'http://'.$_G['setting']['domain']['app']['home'] : $_G['siteurl'].'home.php'; ?>;icon-uri=<?php echo $_G['siteurl'];?><?php echo IMGDIR;?>/home.ico" /><?php } ?>
    <?php if($_G['basescript']=='forum' && $_G['setting']['archiver']) { ?>
        <link rel="archives" title="<?php echo $_G['setting']['bbname'];?>" href="<?php echo $_G['siteurl'];?>archiver/" />
    <?php } ?>
    <?php if(!empty($rsshead)) { ?><?php echo $rsshead;?><?php } ?>
    <?php if(widthauto()) { ?>
        <link rel="stylesheet" id="css_widthauto" type="text/css" href="<?php echo $_G['setting']['csspath'];?><?php echo STYLEID;?>_widthauto.css?<?php echo VERHASH;?>" />
        <script type="text/javascript">HTMLNODE.className += ' widthauto'</script>
    <?php } ?>
    <?php if(in_array($_G['basescript'],array('forum','group'))) { ?>
        <script src="<?php echo $_G['setting']['jspath'];?>forum.js?<?php echo VERHASH;?>" type="text/javascript"></script>
    <?php } elseif(in_array($_G['basescript'],array('home','userapp'))) { ?>
        <script src="<?php echo $_G['setting']['jspath'];?>home.js?<?php echo VERHASH;?>" type="text/javascript"></script>
    <?php } elseif($_G['basescript']=='portal') { ?>
        <script src="<?php echo $_G['setting']['jspath'];?>portal.js?<?php echo VERHASH;?>" type="text/javascript"></script>
    <?php } ?>
    <?php if($_G['basescript']!='portal' && $_GET['diy']=='yes' && check_diy_perm($topic)) { ?>
        <script src="<?php echo $_G['setting']['jspath'];?>portal.js?<?php echo VERHASH;?>" type="text/javascript"></script>
    <?php } ?>
    <?php if($_GET['diy']=='yes' && check_diy_perm($topic)) { ?>
        <link rel="stylesheet" type="text/css" id="diy_common" href="<?php echo $_G['setting']['csspath'];?><?php echo STYLEID;?>_css_diy.css?<?php echo VERHASH;?>" />
    <?php } ?>

    <!-- 以下是自己加的 -->
    <?php if($_G['basescript']=='forum') { ?>
        <link rel="stylesheet" type="text/css" href="<?php echo LO_PUB_CSS;?>common.css">
    <?php } ?>
    <?php if($_G['basescript']=='home') { ?>
        <?php if($mod=='space') { ?>
        <link rel="stylesheet" type="text/css" href="/template/default/_public/cache/style_1_home_space.css">
        <?php } ?>
    <?php } ?>
    <?php if(in_array($_G['basescript'],array('forum'))) { ?>
        <?php if($mod=='forumdisplay') { ?>
            <link rel="stylesheet" type="text/css" href="/template/default/_public/cache/style_1_forum_forumdisplay.css">
        <?php } elseif($mod=='viewthread') { ?>
            <link rel="stylesheet" type="text/css" href="/template/default/_public/cache/style_1_forum_viewthread.css">
        <?php } elseif($mod=='post') { ?>
            <link rel="stylesheet" type="text/css" href="/template/default/_public/cache/style_1_forum_post.css">
        <?php } ?>
        <!-- 下面的 jquery-1.12.1.min.js 会导致系统自带的富文本编辑器上小按钮失效 -->
        <script src="<?php echo LO_PUB_JS;?>jquery-1.12.1.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            var jq = jQuery.noConflict();// noConflict() 方法会释放会 $ 标识符的控制，这样其他脚本就可以使用它了。
        </script>
    <?php } ?>
    <!-- 省得麻烦，就写在这里共用了 如果遇到与系统冲突的请用 jq符 替换 <?php echo $符;?>-->
    <link type="text/css" rel="stylesheet" href="<?php echo LO_PUB_ORG;?>validform/validform.css">
    <script src="<?php echo LO_PUB_ORG;?>validform/Validform_v5.3.2_min.js" type="text/javascript"></script>
</head>
<body id="nv_<?php echo $_G['basescript'];?>" class="pg_<?php echo CURMODULE;?><?php if($_G['basescript']==='portal' && CURMODULE==='list' && !empty($cat)) { ?> <?php echo $cat['bodycss'];?><?php } ?>" onkeydown="if(event.keyCode==27) return false;">
<div id="append_parent"></div><div id="ajaxwaitid"></div>
<?php if($_G['basescript'] == 'forum') { ?>
<div id="wp">
    <!-- 论坛页专用头 -->
<!-- 外部样式文件会优先加载进来 -->
<link rel="stylesheet" type="text/css" href="<?php echo LO_PUB_CSS;?>forum.css"/>
<!-- 论坛左侧导航 --><?php include template('common/header_forum_left'); ?><!-- 论坛右侧部分 -->
<div id="right">
    <!-- 顶部导航 -->
    <?php include template('_public/nav_main'); ?>    <!-- 推荐部分 -->
    <div class="fr_h">
        <div class="fr_hl lf lb">
            <div class="lo lf"><img src="<?php echo LO_PUB_IMG;?>logo.jpg"></div>
            <div class="fr_nav lf">
                <ul  class="lf fr_nav1" >
                    <?php if(is_array($forum_hot)) foreach($forum_hot as $v) { ?>                    <li ><a href="<?php echo $forum_url;?><?php echo $v['fid'];?>"><?php echo $v['name'];?></a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <!-- 搜索END -->
        <div class="fr_hr rt">
            <div class="fr_se rt">
                <form action="search.php" method="post" autocomplete="on" name="form_research">
                    <select name="mod" class="fl" style="width: 11%;height: 41px;border: 2px solid #004d88;min-width:52px;padding:0px;">
                        <option value="portal">文章</option>
                        <option value="forum" selected>帖子</option>
                        <!-- <option value="group">群组</option> -->
                        <option value="user">用户</option>
                        <!-- <option value="project">项目</option>
                        <option value="product">商品</option>
                        <option value="file">文件</option>
                        <option value="act">活动</option> -->
                    </select>
                    <input type="text" name="srchtxt" placeholder="请输入关键词" class="se1">
                    <input type="submit" class="se2" value="搜索">
                    <input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
                    <input type="hidden" name="searchsubmit" value="yes">
                    
                </form>
            </div>
        </div>
        <!-- 搜索END -->
    </div>

    <div class="forum_right " id="forum_right">
    <?php if($_G['basescript']=='forum' && $gid) { ?>
        <div class="clear"></div>
        <div class="fr_hl tc lf">
            <div class="fr_hl_1 lf">
                <div class="fr_hl_1l lf">
                    <p class="hd">本月达人</p>
                    <?php if(is_array($talent_show)) foreach($talent_show as $v) { ?>                    <div class="fr_hd clear">
                        <div class="fr_hd_1 lf"><?php echo avatar($v[uid],'small');?></div>
                        <div class="fr_hd_2 lf">
                            <p><a href="home.php?mod=space&amp;uid=<?php echo $v['uid'];?>"><?php echo $v['username'];?></a></p>
                            <p>兴趣爱好：<span><?php echo $v['interest'];?></span></p>
                        </div>
                    </div><div class="clear"></div>
                    <?php } ?>
                </div>
                <div class="fr_hl_1r rt">
                    <p class="hd">最新主题</p>
                    <?php if(is_array($grids['newthread'])) foreach($grids['newthread'] as $thread) { ?>                    <?php if(!$thread['forumstick'] && $thread['closed'] > 1 && ($thread['isgroup'] == 1 || $thread['fid'] != $_G['fid'])) { ?>
                    <?php $thread[tid]=$thread[closed];?>                    <?php } ?>
                    <div class="fr_hdR">
                        <div class="fr_hd_1 lf"></div>
                        <div class="fr_hd_2 lf">
                            <div><a href="forum.php?mod=viewthread&amp;tid=<?php echo $thread['tid'];?>&amp;extra=<?php echo $extra;?>"<?php if($thread['highlight']) { ?> <?php echo $thread['highlight'];?><?php } if($_G['setting']['grid']['showtips']) { ?> <?php } else { ?> title="<?php echo $thread['oldsubject'];?>"<?php } if($_G['setting']['grid']['targetblank']) { ?> target="_blank"<?php } ?>><?php echo $thread['subject'];?></a></div>
                            <!-- <p>打赏金额：<span>通信工程</span></p> -->
                        </div>
                    </div><div class="clear"></div>
                    <?php } ?>
                </div>
            </div>
            <div class="fr_hl_2 lf">
                <p class="hd">个人中心</p>
                <p class=" pr"><a href="home.php?mod=space&amp;uid=<?php echo $_G['uid'];?>&amp;do=profile" target="_blank" title="访问我的空间">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $_G['member']['username'];?></a></p>
                <p class="pr2 pr">积分：<a href="home.php?mod=spacecp&amp;ac=credit&amp;showcredit=1"><?php echo $_G['member']['credits'];?></a></p>
                <p class="pr3 pr">用户组：<a href="home.php?mod=spacecp&amp;ac=usergroup"><?php echo $_G['group']['grouptitle'];?><?php if($_G['member']['freeze']) { ?><span>(已冻结)</span><?php } ?></a></p>
                <p class="pr4 pr">
                    <?php $creditid=$_G['setting']['creditstrans'];?>                    <?php if($_G['setting']['extcredits'][$creditid]) { ?>
                    <?php $credit=$_G['setting']['extcredits'][$creditid];?>                    <em><?php if($credit['img']) { ?><?php echo $credit['img'];?><?php } ?><?php echo $credit['title'];?>：</em>
                    <a href="home.php?mod=spacecp&amp;ac=credit&amp;showcredit=1"><?php echo getuserprofile('extcredits'.$creditid);; ?> <?php echo $credit['unit'];?></a>
                    <?php } ?>
                </p>
                <p class="pr5 pr">消息：<a href="home.php?mod=space&amp;do=pm" ><?php echo $_G['member']['newpm'];?></a></p>
            </div>
        </div>
        <div class="fr_hr rt">
            <div class="fr_hr_1"> <img src="<?php echo LO_PUB_IMG;?>ad.jpg"></div>
        </div>
    <?php } ?>
    </div>

    <div class="clear"></div>
    <div class="ad2"></div><?php } else { ?>
    <?php if(!$switch_header) { ?>
        <!-- 新头 -->
    <?php include template('_public/nav_main'); ?>    	<div id="wp" class="wp">
    <?php } else { ?>
        <!-- 系统默认头 -->
        <?php if($_GET['diy'] == 'yes' && check_diy_perm($topic)) { ?>
            <?php include template('common/header_diy'); ?>        <?php } ?>
        <?php if(check_diy_perm($topic)) { ?>
            <?php include template('common/header_diynav'); ?>        <?php } ?>
        <?php if(CURMODULE == 'topic' && $topic && empty($topic['useheader']) && check_diy_perm($topic)) { ?>
            <?php echo $diynav;?>
        <?php } ?>
        <?php if(empty($topic) || $topic['useheader']) { ?>
            <?php if($_G['setting']['mobile']['allowmobile'] && (!$_G['setting']['cacheindexlife'] && !$_G['setting']['cachethreadon'] || $_G['uid']) && ($_GET['diy'] != 'yes' || !$_GET['inajax']) && ($_G['mobile'] != '' && $_G['cookie']['mobile'] == '' && $_GET['mobile'] != 'no')) { ?>
                <div class="xi1 bm bm_c">
                    请选择 <a href="<?php echo $_G['siteurl'];?>forum.php?mobile=yes">进入手机版</a> <span class="xg1">|</span> <a href="<?php echo $_G['setting']['mobile']['nomobileurl'];?>">继续访问电脑版</a>
                </div>
            <?php } ?>
            <?php if($_G['setting']['shortcut'] && $_G['member']['credits'] >= $_G['setting']['shortcut']) { ?>
                <div id="shortcut">
                    <span><a href="javascript:;" id="shortcutcloseid" title="关闭">关闭</a></span>
                    您经常访问 <?php echo $_G['setting']['bbname'];?>，试试添加到桌面，访问更方便！
                    <a href="javascript:;" id="shortcuttip">添加 <?php echo $_G['setting']['bbname'];?> 到桌面</a>
                </div>
                <script type="text/javascript">setTimeout(setShortcut, 2000);</script>
            <?php } ?>
            <div id="toptb" class="cl">
                <?php if(!empty($_G['setting']['pluginhooks']['global_cpnav_top'])) echo $_G['setting']['pluginhooks']['global_cpnav_top'];?>
                <!-- 顶部 -->
                <div class="wp">
                    <div class="z">
                        <?php if(is_array($_G['setting']['topnavs']['0'])) foreach($_G['setting']['topnavs']['0'] as $nav) { ?>                            <?php if($nav['available'] && (!$nav['level'] || ($nav['level'] == 1 && $_G['uid']) || ($nav['level'] == 2 && $_G['adminid'] > 0) || ($nav['level'] == 3 && $_G['adminid'] == 1))) { ?><?php echo $nav['code'];?><?php } ?>
                        <?php } ?>
                        <?php if(!empty($_G['setting']['pluginhooks']['global_cpnav_extra1'])) echo $_G['setting']['pluginhooks']['global_cpnav_extra1'];?>
                    </div>
                    <div class="y">
                        <a id="switchblind" href="javascript:void(0);" onclick="toggleBlind(this)" title="开启辅助访问" class="switchblind">开启辅助访问</a>
                        <?php if(!empty($_G['setting']['pluginhooks']['global_cpnav_extra2'])) echo $_G['setting']['pluginhooks']['global_cpnav_extra2'];?>
                        <?php if(is_array($_G['setting']['topnavs']['1'])) foreach($_G['setting']['topnavs']['1'] as $nav) { ?>                            <?php if($nav['available'] && (!$nav['level'] || ($nav['level'] == 1 && $_G['uid']) || ($nav['level'] == 2 && $_G['adminid'] > 0) || ($nav['level'] == 3 && $_G['adminid'] == 1))) { ?><?php echo $nav['code'];?><?php } ?>
                        <?php } ?>
                        <?php if(empty($_G['disabledwidthauto']) && $_G['setting']['switchwidthauto']) { ?>
                            <a href="javascript:;" id="switchwidth" onclick="widthauto(this)" title="<?php if(widthauto()) { ?>切换到窄版<?php } else { ?>切换到宽版<?php } ?>" class="switchwidth"><?php if(widthauto()) { ?>切换到窄版<?php } else { ?>切换到宽版<?php } ?></a>
                        <?php } ?>
                        <?php if($_G['uid'] && !empty($_G['style']['extstyle'])) { ?><a id="sslct" href="javascript:;" onmouseover="delayShow(this, function() {showMenu({'ctrlid':'sslct','pos':'34!'})});">切换风格</a><?php } ?>
                        <?php if(check_diy_perm($topic)) { ?>
                            <?php echo $diynav;?>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <?php if(!IS_ROBOT) { ?>
                <?php if($_G['uid']) { ?>
                <ul id="myprompt_menu" class="p_pop" style="display: none;">
                    <li><a href="home.php?mod=space&amp;do=pm" id="pm_ntc" style="background-repeat: no-repeat; background-position: 0 50%;"><em class="prompt_news<?php if(empty($_G['member']['newpm'])) { ?>_0<?php } ?>"></em>消息</a></li>
                    <li><a href="home.php?mod=follow&amp;do=follower"><em class="prompt_follower<?php if(empty($_G['member']['newprompt_num']['follower'])) { ?>_0<?php } ?>"></em>新听众<?php if($_G['member']['newprompt_num']['follower']) { ?>(<?php echo $_G['member']['newprompt_num']['follower'];?>)<?php } ?></a></li>
                    <?php if($_G['member']['newprompt'] && $_G['member']['newprompt_num']['follow']) { ?>
                        <li><a href="home.php?mod=follow"><em class="prompt_concern"></em>我关注的(<?php echo $_G['member']['newprompt_num']['follow'];?>)</a></li>
                    <?php } ?>
                    <?php if($_G['member']['newprompt']) { ?>
                        <?php if(is_array($_G['member']['category_num'])) foreach($_G['member']['category_num'] as $key => $val) { ?>                            <li><a href="home.php?mod=space&amp;do=notice&amp;view=<?php echo $key;?>"><em class="notice_<?php echo $key;?>"></em><?php echo lang('template', 'notice_'.$key); ?>(<span class="rq"><?php echo $val;?></span>)</a></li>
                        <?php } ?>
                    <?php } ?>
                    <?php if(empty($_G['cookie']['ignore_notice'])) { ?>
                        <li class="ignore_noticeli"><a href="javascript:;" onclick="setcookie('ignore_notice', 1);hideMenu('myprompt_menu')" title="暂不提醒"><em class="ignore_notice"></em></a></li>
                    <?php } ?>
                </ul>
                <?php } ?>
                <?php if($_G['uid'] && !empty($_G['style']['extstyle'])) { ?>
                    <div id="sslct_menu" class="cl p_pop" style="display: none;">
                        <?php if(!$_G['style']['defaultextstyle']) { ?><span class="sslct_btn" onclick="extstyle('')" title="默认"><i></i></span><?php } ?>
                        <?php if(is_array($_G['style']['extstyle'])) foreach($_G['style']['extstyle'] as $extstyle) { ?>                            <span class="sslct_btn" onclick="extstyle('<?php echo $extstyle['0'];?>')" title="<?php echo $extstyle['1'];?>"><i style='background:<?php echo $extstyle['2'];?>'></i></span>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if($_G['uid']) { ?>
                    <ul id="myitem_menu" class="p_pop" style="display: none;">
                        <li><a href="forum.php?mod=guide&amp;view=my">帖子</a></li>
                        <li><a href="home.php?mod=space&amp;do=favorite&amp;view=me">收藏</a></li>
                        <li><a href="home.php?mod=space&amp;do=friend">好友</a></li>
                        <?php if(!empty($_G['setting']['pluginhooks']['global_myitem_extra'])) echo $_G['setting']['pluginhooks']['global_myitem_extra'];?>
                    </ul>
                <?php } ?>
                <div id="qmenu_menu" class="p_pop <?php if(!$_G['uid']) { ?>blk<?php } ?>" style="display: none;">
<?php if(!empty($_G['setting']['pluginhooks']['global_qmenu_top'])) echo $_G['setting']['pluginhooks']['global_qmenu_top'];?>
<?php if($_G['uid']) { ?>
<ul class="cl nav"><?php if(is_array($_G['setting']['mynavs'])) foreach($_G['setting']['mynavs'] as $nav) { if($nav['available'] && (!$nav['level'] || ($nav['level'] == 1 && $_G['uid']) || ($nav['level'] == 2 && $_G['adminid'] > 0) || ($nav['level'] == 3 && $_G['adminid'] == 1))) { ?>
<li><?php echo $nav['code'];?></li>
<?php } } ?>
</ul>
<?php } elseif($_G['connectguest']) { ?>
<div class="ptm pbw hm">
请先<br /><a class="xi2" href="member.php?mod=connect"><strong>完善帐号信息</strong></a> 或 <a href="member.php?mod=connect&amp;ac=bind" class="xi2 xw1"><strong>绑定已有帐号</strong></a><br />后使用快捷导航
</div>
<?php } else { ?>
<div class="ptm pbw hm">
请 <a href="javascript:;" class="xi2" onclick="lsSubmit()"><strong>登录</strong></a> 后使用快捷导航<br />没有帐号？<a href="member.php?mod=<?php echo $_G['setting']['regname'];?>" class="xi2 xw1"><?php echo $_G['setting']['reglinkname'];?></a>
</div>
<?php } if($_G['setting']['showfjump']) { ?><div id="fjump_menu" class="btda"></div><?php } ?>
<?php if(!empty($_G['setting']['pluginhooks']['global_qmenu_bottom'])) echo $_G['setting']['pluginhooks']['global_qmenu_bottom'];?>
</div>            <?php } ?>

            <?php echo adshow("headerbanner/wp a_h");?>            <div id="hd">
                <div class="wp">
                    <div class="hdc cl">
                        <!-- 网站logo -->
                        <?php $mnid = getcurrentnav();?>                        <h2><?php if(!isset($_G['setting']['navlogos'][$mnid])) { ?><a href="<?php if($_G['setting']['domain']['app']['default']) { ?>http://<?php echo $_G['setting']['domain']['app']['default'];?>/<?php } else { ?>./<?php } ?>" title="<?php echo $_G['setting']['bbname'];?>"><?php echo $_G['style']['boardlogo'];?></a><?php } else { ?><?php echo $_G['setting']['navlogos'][$mnid];?><?php } ?></h2>
                        <!-- 用户信息栏组合 -->
                        <?php include template('common/header_userstatus'); ?>                    </div>
                    <!-- 导航 -->
                    <div id="nv">
                        <!-- 快捷导航 -->
                        <a href="javascript:;" id="qmenu" onmouseover="delayShow(this, function () {showMenu({'ctrlid':'qmenu','pos':'34!','ctrlclass':'a','duration':2});showForummenu(<?php echo $_G['fid'];?>);})">快捷导航</a>
                        <!-- 主导航 -->
                        <ul>
                            <?php if(is_array($_G['setting']['navs'])) foreach($_G['setting']['navs'] as $nav) { ?>                                <?php if($nav['available'] && (!$nav['level'] || ($nav['level'] == 1 && $_G['uid']) || ($nav['level'] == 2 && $_G['adminid'] > 0) || ($nav['level'] == 3 && $_G['adminid'] == 1))) { ?><li <?php if($mnid == $nav['navid']) { ?>class="a" <?php } ?><?php echo $nav['nav'];?>></li><?php } ?>
                            <?php } ?>
                        </ul>
                        <?php if(!empty($_G['setting']['pluginhooks']['global_nav_extra'])) echo $_G['setting']['pluginhooks']['global_nav_extra'];?>
                    </div>
                    <?php if(!empty($_G['setting']['plugins']['jsmenu'])) { ?>
                        <ul class="p_pop h_pop" id="plugin_menu" style="display: none">
                        <?php if(is_array($_G['setting']['plugins']['jsmenu'])) foreach($_G['setting']['plugins']['jsmenu'] as $module) { ?>                             <?php if(!$module['adminid'] || ($module['adminid'] && $_G['adminid'] > 0 && $module['adminid'] >= $_G['adminid'])) { ?>
                             <li><?php echo $module['url'];?></li>
                             <?php } ?>
                        <?php } ?>
                        </ul>
                    <?php } ?>
                    <?php echo $_G['setting']['menunavs'];?>
                    <div id="mu" class="cl">
                    <?php if($_G['setting']['subnavs']) { ?>
                        <?php if(is_array($_G['setting']['subnavs'])) foreach($_G['setting']['subnavs'] as $navid => $subnav) { ?>                            <?php if($_G['setting']['navsubhover'] || $mnid == $navid) { ?>
                            <ul class="cl <?php if($mnid == $navid) { ?>current<?php } ?>" id="snav_<?php echo $navid;?>" style="display:<?php if($mnid != $navid) { ?>none<?php } ?>">
                            <?php echo $subnav;?>
                            </ul>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                    </div>
                    <?php echo adshow("subnavbanner/a_mu");?>                    <?php if($_G['setting']['search']) { $slist = array();?><?php if($_G['fid'] && $_G['forum']['status'] != 3 && $mod != 'group') { ?><?php
$slist[forumfid] = <<<EOF
<li><a href="javascript:;" rel="curforum" fid="{$_G['fid']}" >本版</a></li>
EOF;
?><?php } if($_G['setting']['portalstatus'] && $_G['setting']['search']['portal']['status'] && ($_G['group']['allowsearch'] & 1 || $_G['adminid'] == 1)) { ?><?php
$slist[portal] = <<<EOF
<li><a href="javascript:;" rel="article">文章</a></li>
EOF;
?><?php } if($_G['setting']['search']['forum']['status'] && ($_G['group']['allowsearch'] & 2 || $_G['adminid'] == 1)) { ?><?php
$slist[forum] = <<<EOF
<li><a href="javascript:;" rel="forum" class="curtype">帖子</a></li>
EOF;
?><?php } if(helper_access::check_module('blog') && $_G['setting']['search']['blog']['status'] && ($_G['group']['allowsearch'] & 4 || $_G['adminid'] == 1)) { ?><?php
$slist[blog] = <<<EOF
<li><a href="javascript:;" rel="blog">日志</a></li>
EOF;
?><?php } if(helper_access::check_module('album') && $_G['setting']['search']['album']['status'] && ($_G['group']['allowsearch'] & 8 || $_G['adminid'] == 1)) { ?><?php
$slist[album] = <<<EOF
<li><a href="javascript:;" rel="album">相册</a></li>
EOF;
?><?php } if($_G['setting']['groupstatus'] && $_G['setting']['search']['group']['status'] && ($_G['group']['allowsearch'] & 16 || $_G['adminid'] == 1)) { ?><?php
$slist[group] = <<<EOF
<li><a href="javascript:;" rel="group">{$_G['setting']['navs']['3']['navname']}</a></li>
EOF;
?><?php } ?><?php
$slist[user] = <<<EOF
<li><a href="javascript:;" rel="user">用户</a></li>
EOF;
?>
<?php } if($_G['setting']['search'] && $slist) { ?>
<div id="scbar" class="<?php if($_G['setting']['srchhotkeywords'] && count($_G['setting']['srchhotkeywords']) > 5) { ?>scbar_narrow <?php } ?>cl">
<form id="scbar_form" method="<?php if($_G['fid'] && !empty($searchparams['url'])) { ?>get<?php } else { ?>post<?php } ?>" autocomplete="off" onsubmit="searchFocus($('scbar_txt'))" action="<?php if($_G['fid'] && !empty($searchparams['url'])) { ?><?php echo $searchparams['url'];?><?php } else { ?>search.php?searchsubmit=yes<?php } ?>" target="_blank">
<input type="hidden" name="mod" id="scbar_mod" value="search" />
<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>" />
<input type="hidden" name="srchtype" value="title" />
<input type="hidden" name="srhfid" value="<?php echo $_G['fid'];?>" />
<input type="hidden" name="srhlocality" value="<?php echo $_G['basescript'];?>::<?php echo CURMODULE;?>" />
<?php if(!empty($searchparams['params'])) { if(is_array($searchparams['params'])) foreach($searchparams['params'] as $key => $value) { $srchotquery .= '&' . $key . '=' . rawurlencode($value);?><input type="hidden" name="<?php echo $key;?>" value="<?php echo $value;?>" />
<?php } ?>
<input type="hidden" name="source" value="discuz" />
<input type="hidden" name="fId" id="srchFId" value="<?php echo $_G['fid'];?>" />
<input type="hidden" name="q" id="cloudsearchquery" value="" />

<style>
#scbar { overflow: visible; position: relative; }
#sg{ background: #FFF; width:456px; border: 1px solid #B2C7DA; }
.scbar_narrow #sg { width: 316px; }
#sg li { padding:0 8px; line-height:30px; font-size:14px; }
#sg li span { color:#999; }
.sml { background:#FFF; cursor:default; }
.smo { background:#E5EDF2; cursor:default; }
            </style>
            <div style="display: none; position: absolute; top:37px; left:44px;" id="sg">
                <div id="st_box" cellpadding="2" cellspacing="0"></div>
            </div>
<?php } ?>
<table cellspacing="0" cellpadding="0">
<tr>
<td class="scbar_icon_td"></td>
<td class="scbar_txt_td"><input type="text" name="srchtxt" id="scbar_txt" value="请输入搜索内容" autocomplete="off" x-webkit-speech speech /></td>
<td class="scbar_type_td"><a href="javascript:;" id="scbar_type" class="xg1" onclick="showMenu(this.id)" hidefocus="true">搜索</a></td>
<td class="scbar_btn_td"><button type="submit" name="searchsubmit" id="scbar_btn" sc="1" class="pn pnc" value="true"><strong class="xi2">搜索</strong></button></td>
<td class="scbar_hot_td">
<div id="scbar_hot">
<?php if($_G['setting']['srchhotkeywords']) { ?>
<strong class="xw1">热搜: </strong><?php if(is_array($_G['setting']['srchhotkeywords'])) foreach($_G['setting']['srchhotkeywords'] as $val) { if($val=trim($val)) { $valenc=rawurlencode($val);?><?php
$__FORMHASH = FORMHASH;$srchhotkeywords[] = <<<EOF


EOF;
 if(!empty($searchparams['url'])) { 
$srchhotkeywords[] .= <<<EOF

<a href="{$searchparams['url']}?q={$valenc}&source=hotsearch{$srchotquery}" target="_blank" class="xi2" sc="1">{$val}</a>

EOF;
 } else { 
$srchhotkeywords[] .= <<<EOF

<a href="search.php?mod=forum&amp;srchtxt={$valenc}&amp;formhash={$__FORMHASH}&amp;searchsubmit=true&amp;source=hotsearch" target="_blank" class="xi2" sc="1">{$val}</a>

EOF;
 } 
$srchhotkeywords[] .= <<<EOF


EOF;
?>
<?php } } echo implode('', $srchhotkeywords);; } ?>
</div>
</td>
</tr>
</table>
</form>
</div>
<ul id="scbar_type_menu" class="p_pop" style="display: none;"><?php echo implode('', $slist);; ?></ul>
<script type="text/javascript">
initSearchmenu('scbar', '<?php echo $searchparams['url'];?>');
</script>
<?php } ?>                </div>
            </div>

            <?php if(!empty($_G['setting']['pluginhooks']['global_header'])) echo $_G['setting']['pluginhooks']['global_header'];?>
        <?php } ?>
        <div id="wp" class="wp">
    <?php } } ?>