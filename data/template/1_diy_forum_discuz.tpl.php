<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('discuz');?><?php include template('_public/header'); ?><div id="wp">
    <!-- 论坛左侧导航 -->
    <?php include template('common/header_forum_left'); ?>    <!-- 论坛右侧部分 -->
    <div id="right">
        <!-- 顶部导航 -->
        <?php include template('_public/nav_main'); ?>        <!-- 推荐部分 -->
        <div class="fr_h">
            <div class="fr_hl lf lb">
                <div class="lo lf"><img src="<?php echo LO_PUB_IMG;?>logo.jpg"></div>
                <div class="fr_nav lf">
                    <ul  class="lf fr_nav1" >
                        <li><a href="forum.php">论坛首页</a></li>
                        <?php if(is_array($forum_hot)) foreach($forum_hot as $v) { ?>                        <li><a href="<?php echo $forum_url;?><?php echo $v['fid'];?>"><?php echo $v['name'];?></a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <!-- 搜索START -->
            <div class="fr_hr rt">
                <div class="fr_se rt">
                    <form action="search.php" method="post" autocomplete="on" name="form_research">
                        <select name="mod" class="fl" style="width: 11%;height: 41px;border: 2px solid #004d88;min-width:52px;">
                            <option value="portal">文章</option>
                            <option value="forum" selected>帖子</option>
                            <!-- <option value="group">群组</option> -->
                            <option value="user">用户</option>
                            <!-- <option value="project">项目</option> -->
                            <!-- <option value="product">商品</option> -->
                            <!-- <option value="file">文件</option> -->
                            <!-- <option value="act">活动</option> -->
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

        <div class="forum_right" id="forum_right">
            <div class="clear"></div>
            <div class="fr_hl tc lf">
                <div class="fr_hl_1 lf">
                    <div class="fr_hl_1l lf">
                        <p class="hd">本月达人</p>
                        <?php if(is_array($talent_show)) foreach($talent_show as $v) { ?>                        <div class="fr_hd clear">
                            <div class="fr_hd_1 lf"><?php echo avatar($v[uid],'small');?></div>
                            <div class="fr_hd_2 lf">
                                <p><a href="home.php?mod=space&amp;do=index&amp;uid=<?php echo $v['uid'];?>"><?php echo $v['username'];?></a></p>
                                <p>兴趣爱好：<span><?php echo $v['interest'];?></span></p>
                            </div>
                        </div><div class="clear"></div>
                        <?php } ?>
                    </div>
                    <div class="fr_hl_1r rt">
                        <p class="hd">最新主题</p>
                        <?php if(is_array($grids['newthread'])) foreach($grids['newthread'] as $thread) { ?>                        <?php if(!$thread['forumstick'] && $thread['closed'] > 1 && ($thread['isgroup'] == 1 || $thread['fid'] != $_G['fid'])) { ?>
                        <?php $thread[tid]=$thread[closed];?>                        <?php } ?>
                        <div class="fr_hdR">
                            <div class="fr_hd_1 lf"></div>
                            <div class="fr_hd_2 lf">
                                <div><a href="forum.php?mod=viewthread&amp;tid=<?php echo $thread['tid'];?>&amp;extra=<?php echo $extra;?>"<?php if($thread['highlight']) { ?> <?php echo $thread['highlight'];?><?php } if($_G['setting']['grid']['showtips']) { ?> <?php } else { ?> title="<?php echo $thread['oldsubject'];?>"<?php } if($_G['setting']['grid']['targetblank']) { ?> target="_blank"<?php } ?>><?php echo $thread['subject'];?></a></div>
                                <!-- <p>打赏金额：<span>通信工程</span></p> -->
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="fr_hl_2 lf">
                    <p class="hd">个人中心</p>
                    <p class="pr1 pr">账户：<a href="home.php?mod=space&amp;uid=<?php echo $_G['uid'];?>&amp;do=profile" target="_blank" title="访问我的空间"><?php echo $_G['member']['username'];?></a></p>
                    <p class="pr2 pr">积分：<a href="home.php?mod=spacecp&amp;ac=credit&amp;showcredit=1"><?php echo $_G['member']['credits'];?></a></p>
                    <p class="pr3 pr">用户组：<a href="home.php?mod=spacecp&amp;ac=usergroup"><?php echo $_G['group']['grouptitle'];?><?php if($_G['member']['freeze']) { ?><span>(已冻结)</span><?php } ?></a></p>
                    <p class="pr4 pr">
                        <?php $creditid=$_G['setting']['creditstrans'];?>                        <?php if($_G['setting']['extcredits'][$creditid]) { ?>
                        <?php $credit=$_G['setting']['extcredits'][$creditid];?>                        <?php if($credit['img']) { ?><?php echo $credit['img'];?><?php } ?><?php echo $credit['title'];?>：
                        <a href="home.php?mod=spacecp&amp;ac=credit&amp;showcredit=1"><?php echo getuserprofile('extcredits'.$creditid);; ?> <?php echo $credit['unit'];?></a>
                        <?php } ?>
                    </p>
                    <p class="pr5 pr">消息：<a href="home.php?mod=space&amp;do=pm" ><?php echo $_G['member']['newpm'];?></a></p>
                </div>
            </div>
            <div class="fr_hr rt">
                <div class="fr_hr_1"> <img src="<?php echo LO_PUB_IMG;?>ad.jpg"></div>
            </div>
        </div>

        <div class="clear"></div>
        <!-- <div class="clear ad">广告位1</div> -->
        <div class="ad2"></div>

        <!-- 版块列表 -->
        <?php if(is_array($catlist)) foreach($catlist as $key => $cat) { ?><!-- 类型 (group:分类 @分区) -->
        <div class="forum_main">
            <div class="forum_mh"><p><?php echo $cat['name'];?></p></div>
            <div class="forum_mm">
                <table border="0" cellspacing="0" cellpadding="0" width="100%" text-align="center">
                    <thead>
                        <tr>
                            <th class="w">板块名</th>
                            <th class="w2">主题数</th>
                            <th class="w3">贴数</th>
                            <th class="w4">版主</th>
                            <th class="w5">最后发表</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(is_array($cat['forums'])) foreach($cat['forums'] as $forumid) { ?><!-- 类型 (forum:普通论坛 @板块) -->
                        <?php $forum=$forumlist[$forumid];?>                        <?php $forumurl = !empty($forum['domain']) && !empty($_G['setting']['domain']['root']['forum']) ? 'http://'.$forum['domain'].'.'.$_G['setting']['domain']['root']['forum'] : 'forum.php?mod=forumdisplay&fid='.$forum['fid'];?>                        <?php if($cat['forumcolumns']) { ?>
                        <?php } else { ?>
                        <tr style="border-top:1px solid #ddd;">
                            <td class="w1">
                                <span class="ic1 lf"<?php if(!empty($forum['extra']['iconwidth']) && !empty($forum['icon'])) { ?> style="width:<?php echo $forum['extra']['iconwidth'];?>px;"<?php } ?>>
                                    <?php if($forum['icon']) { ?>
                                    <?php echo $forum['icon'];?>
                                    <?php } else { ?>
                                    <a  href="<?php echo $forumurl;?>" <?php if($forum['redirect']) { ?>target="_blank"<?php } ?>><img src="<?php echo IMGDIR;?>/forum<?php if($forum['folder']) { ?>_new<?php } ?>.gif" alt="<?php echo $forum['name'];?>" /></a>
                                    <?php } ?>
                                </span>
                                <div class="lf">
                                    <p class="ic4">
                                        <span>&nbsp;&nbsp;&nbsp;&nbsp;<a class="c6" href="<?php echo $forumurl;?>" <?php if($forum['redirect']) { ?>target="_blank"<?php } ?>><?php echo $forum['name'];?></a></span>
                                        <span class="ic3"><?php if($forum['todayposts'] && !$forum['redirect']) { ?><em title="今日"> (<?php echo $forum['todayposts'];?>)</em><?php } ?></span>
                                    </p>
                                    <?php if($forum['description']) { ?><p class="xg2"><?php echo $forum['description'];?></p><?php } ?>
                                    <?php if($forum['subforums']) { ?><p>子版块: <?php echo $forum['subforums'];?></p><?php } ?>
                                </div>
                            </td>
                            <td class="w2 c6"><?php echo dnumber($forum['threads']); ?></td>
                            <td class="w3 c6"><?php echo dnumber($forum['posts']); ?></td>
                            <td class="w5 c6"><?php if($forum['moderators']) { ?><p><span class="c6 aaaa"><?php echo $forum['moderators'];?></span></p><?php } ?></td>
                            <td class="w4">
                                <div>
                                    <?php if($forum['permission'] == 1) { ?>
                                    私密版块
                                    <?php } else { ?>
                                    <?php if($forum['redirect']) { ?>
                                    <a href="<?php echo $forumurl;?>" class="c6">链接到外部地址</a>
                                    <?php } elseif(is_array($forum['lastpost'])) { ?>
                                    <p class="ic4 ic6 c6"><a href="forum.php?mod=redirect&amp;tid=<?php echo $forum['lastpost']['tid'];?>&amp;goto=lastpost#lastpost" class="c6"><?php echo cutstr($forum['lastpost']['subject'], 30); ?></a></p>
                                    <p class="ic5 ic6"><cite><?php echo $forum['lastpost']['dateline'];?>&nbsp;&nbsp;<?php if($forum['lastpost']['author']) { ?><?php echo $forum['lastpost']['author'];?><?php } else { ?><?php echo $_G['setting']['anonymoustext'];?><?php } ?></cite></p>
                                    <?php } else { ?>
                                    从未
                                    <?php } ?>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- ad/intercat/ad/<?php echo $cat['fid'];?> 这才是注释 -->
        <?php } ?>

        <!-- 通用底部 -->
        <?php include template('common/footer'); ?>