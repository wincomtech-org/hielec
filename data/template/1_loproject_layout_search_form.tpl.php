<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>
<div class="clear"></div>
<div id="search">
    <span class="lf">当前搜索 ：</span>
    <div class="sear lf">
        <!-- discuz不认识get模式，原因不明 -->
        <form action="<?php echo $jumpurl;?>" method="post">
            <input class="text" type="text" name="<?php echo $srckey;?>" value="<?php echo $srcval;?>" placeholder="<?php echo $srctip;?>">
            <input class="submit rt" type="submit">
        </form>
    </div>
</div>