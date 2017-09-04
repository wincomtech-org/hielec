<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>
        <div class="phr_per_mess">
            <div class="phr_perMess">
                <ul class="phr_perMess_ul">
                    <li><a href="home.php?mod=spacecp">设置</a></li>
                    <li class="on"><a href="home.php?mod=space&amp;do=pm">消息</a></li>
                    <li><a href="home.php?mod=space&amp;do=notice">提醒</a></li>
                    <li><a href="member.php?mod=logging&amp;action=logout&amp;formhash=<?php echo FORMHASH;?>">退出</a></li>
                </ul>
                <div class="phr_perMess_content">

                    <div class="phr_pm_content" style="display:block;">
                        <div class="phr_perMess_cl lf">
                            <div><a href="home.php?mod=space&amp;uid=<?php echo $_G['uid'];?>"><?php echo avatar($_G[uid],small);?></a></div>
                            <p><a href="home.php?mod=spacecp&amp;ac=profile"><span class="ph ph_8"></span>修改个人资料</a></p>
                        </div>
                        <div class="phr_perMess_cr lf">
                            <div class="phr_perMess_cr1"><b>我的积分：</b><a href="home.php?mod=spacecp&amp;ac=credit"><span><?php echo $_G['member']['credits'];?>分</span></a><a href="plugin.php?id=loactivity:activity">我要兑换</a></div>
                            <div class="phr_perMess_cr2"><i>钱包余额：</i><a href="home.php?mod=spacecp&amp;ac=credit&amp;op=buy"><span><?php echo $curcredits;?> <?php echo $creditname;?></span></a></div>
                            <div class="phr_perMess_cr3"><a href="home.php?mod=spacecp&amp;ac=credit&amp;op=buy">充值</a><a href="home.php?mod=spacecp&amp;ac=credit&amp;op=transfer">转账</a><a href="home.php?mod=spacecp&amp;ac=credit&amp;op=log">交易记录</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>