<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>
        <div class="zl">
            <p>总项目数量：<span class="on"><?php echo $pros['sum'];?></span></p>
            <p>今日新增项目：<span class="on"><?php echo $pros['sum_today'];?></span></p>
        </div>
        <a href="<?php echo $extra['url'];?>"><span class="fb"><?php echo $extra['name'];?></span></a>
        <p class="newest">新成交项目</p>
        <ul id="r_list">
            <?php if($newProTrade) { ?>
            <?php if(is_array($newProTrade)) foreach($newProTrade as $v) { ?>            <li>
                <p class="lf"><?php echo avatar($v[uid],'small');?></p>
                <div class="rt">
                    <p>
                        <a href="home.php?mod=space&amp;uid=<?php echo $v['uid'];?>"><span class="name"><?php echo $v['username'];?></span></a>
                        <span>&nbsp;&nbsp;&nbsp;项目领域:<i class="on"><?php echo $v['name'];?></i></span>
                        <span class="rt"><?php echo $v['pub_time'];?></span>
                    </p>
                    <p>
                        <a href="<?php echo $mainurl;?>&pluginop=pds&loid=<?php echo $v['tid'];?>"><?php echo $v['task_title'];?></a>
                        <span class="on"><?php echo $v['task_cash'];?>元 </span>
                    </p>
                </div>  
            </li>
            <?php } ?>
            <?php } else { ?>
            <li><?php echo $lang['no_resource'];?></li>
            <?php } ?>
        </ul>
