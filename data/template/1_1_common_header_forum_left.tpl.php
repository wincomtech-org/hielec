<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>

    <!-- 论坛左侧导航 -->
    <div id="left">
        <div class="forum_left">
            <div class="t1"><img src="<?php echo LO_PUB_IMG;?>forumnav.jpg" /></div>
            <div class="TreeWrap">
                <?php if(is_array($catlistall)) foreach($catlistall as $key => $cat) { ?><!-- 所有类型 (group:分类 @分区) -->
                <div class="MenuBox" id="Menu_<?php echo $key;?>">
                    <div class="titBox">
                        <h3 class="Fst"><a href="javascript:showMenuleft(<?php echo $key;?>);" title="<?php echo $cat['name'];?>"><?php echo $cat['name'];?></a></h3>
                    </div>
                    <div class="txtBox">
                        <ul>
                            <?php if(is_array($cat['child'])) foreach($cat['child'] as $child) { ?><!-- 所有类型 (forum:普通论坛 @板块) -->
                            <?php if($cat['forumcolumns']) { ?>
                            <?php } else { ?>
                            <li>
                                <a href="<?php echo $child['url'];?>" <?php if($child['redirect']) { ?>target="_blank"<?php } ?> <?php echo $child['cur'];?> title="<?php echo $child['name'];?>"><?php echo $child['name'];?></a>
                                <?php if($child['todayposts'] && !$child['redirect']) { ?><em class="xw0 xi1" title="!forum_todayposts!"> (<?php echo $child['todayposts'];?>)</em><?php } ?>
                            </li>
                            <?php } ?>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>