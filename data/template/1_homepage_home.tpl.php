<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>
<!DOCTYPE html>
<html lang="zh_CN">
<head>
<meta charset="UTF-8">
<title>官网首页</title>
<link rel="stylesheet" type="text/css" href="<?php echo LO_PUB_CSS;?>common.css">
<link rel="stylesheet" type="text/css" href="<?php echo LO_PUB_CSS;?>home.css">
<script src="<?php echo LO_PUB_JS;?>jquery-1.12.1.min.js" type="text/javascript"></script>
<!-- <base href="<?php echo $_G['siteurl'];?>"> -->
</head>

<body>
<div class="header">
<div class="h_cont">
欢迎访问<?php echo $navtitle;?>
<?php if($_G['uid']) { ?>
<a href="member.php?mod=logging&amp;action=logout&amp;formhash=<?php echo FORMHASH;?>">退出</a>
<strong class="vwmy<?php if($_G['setting']['connect']['allow'] && $_G['member']['conisbind']) { ?> qq<?php } ?>"><a href="home.php?mod=space&amp;uid=<?php echo $_G['uid'];?>" target="_blank" title="访问我的空间"><?php echo $_G['member']['username'];?></a></strong>
<?php } else { ?>
<a href="member.php?mod=register">注册</a>
<a href="member.php?mod=logging&amp;action=login" class="a_bor">登录</a>
<?php } ?>
</div>
</div>

<div class="container">
<?php echo $adv_customs['1'];?>
<div class="logo_ad">
<?php if(!isset($_G['setting']['navlogos'][$mnid])) { ?><a class="logo_home_a" href="<?php if($_G['setting']['domain']['app']['default']) { ?>http://<?php echo $_G['setting']['domain']['app']['default'];?>/<?php } else { ?>./<?php } ?>" title="<?php echo $_G['setting']['bbname'];?>"><?php echo $_G['style']['boardlogo'];?></a><?php } else { ?><?php echo $_G['setting']['navlogos'][$mnid];?><?php } ?>
<?php echo $adv_customs['2'];?>
<?php echo $adv_customs['3'];?>
</div>
<!-- 首页主导航 -->
<div class="menu">
<ul><?php if(is_array($_G['setting']['navs'])) foreach($_G['setting']['navs'] as $nav) { if($nav['available'] && (!$nav['level'] || ($nav['level'] == 1 && $_G['uid']) || ($nav['level'] == 2 && $_G['adminid'] > 0) || ($nav['level'] == 3 && $_G['adminid'] == 1))) { ?>
<li class="nav <?php if($mnid==$nav['navid']) { ?>current<?php } ?>" <?php echo $nav['nav'];?>></li>
<?php } } ?>
</ul>
</div>



<!-- 主体 -->
<div class="main_cont">
<!-- 左栏内容 -->
<div class="left_cont fl">
<div class="l_zone">热门版区：
<ul>
<?php if($forum_hot) { if(is_array($forum_hot)) foreach($forum_hot as $fhot) { ?><li><a href="<?php echo $thread_url;?><?php echo $fhot['fid'];?>"><?php echo cutstr($fhot[name],12)?></a></li>
<?php } } else { ?>
<li><?php echo $lang['no_resource'];?></li>
<?php } ?>
</ul>
</div>
<div class="l_cf">
<?php echo $adv_customs['4'];?>
</div>
<div class="l_cf">
<div class="cf_head">
<img src="<?php echo LO_PUB_IMG;?>ico1_yh.png" height="24">
<span>十大精华帖</span>
</div>
<div class="cf_cont">
<ul>
<?php if($thread_digest) { if(is_array($thread_digest)) foreach($thread_digest as $tk => $tdig) { ?><li>
<span class="ico<?php if($tk<3) { ?><?php echo $tk;?><?php } ?>"><?php echo $tk+1?></span>
<a <?php if($tk<3) { ?>class="top"<?php } ?> href="<?php echo $post_url;?><?php echo $tdig['tid'];?>"><?php echo $tdig['subject'];?></a>
</li>
<?php } } else { ?>
<li><?php echo $lang['no_resource'];?></li>
<?php } ?>
</ul>
</div>
</div>
<div class="l_cf">
<div class="cf_head">
<img src="<?php echo LO_PUB_IMG;?>ico2_yh.png" height="24">
<span>十大热门帖</span>
</div>
<div class="cf_cont">
<ul class="circle">
<?php if($thread_hots) { if(is_array($thread_hots)) foreach($thread_hots as $thot) { ?><li><a href="<?php echo $post_url;?><?php echo $thot['tid'];?>"><?php echo $thot['subject'];?></a></li>
<?php } } else { ?>
<li><?php echo $lang['no_resource'];?></li>
<?php } ?>
</ul>
</div>
</div>
<div class="l_cf">
<div class="cf_head">
<img src="<?php echo LO_PUB_IMG;?>ico1_yh.png" height="24">
<span>十大新鲜帖</span>
</div>
<div class="cf_cont">
<ul>
<?php if($thread_news) { if(is_array($thread_news)) foreach($thread_news as $tk => $tnew) { ?><li>
<span class="ico<?php if($tk<3) { ?><?php echo $tk;?><?php } ?>"><?php echo $tk+1?></span>
<a <?php if($tk<3) { ?>class="top"<?php } ?> href="<?php echo $post_url;?><?php echo $tnew['tid'];?>"><?php echo $tnew['subject'];?></a>
</li>
<?php } } else { ?>
<li><?php echo $lang['no_resource'];?></li>
<?php } ?>
</ul>
</div>
</div>
<div class="home_ad">
<img src="/source/plugin/_public/image/ad2.png" alt="广告位">
</div>

</div>


<!-- 右栏内容 -->
<div class="right_cont fr">
<!-- 搜索 -->
<div class="r_bq">
<form action="search.php" method="post" autocomplete="on" name="form_research" class="form_research">
<select name="mod" class="fl">
<option value="portal">文章</option>
<option value="forum" selected>帖子</option>
<!-- <option value="group">群组</option> -->
<option value="user">用户</option>
<!-- <option value="project">项目</option>
<option value="product">商品</option>
<option value="file">文件</option>
<option value="act">活动</option> -->
</select>
<input type="text" name="srchtxt" class="keywords" value="请输入关键词" onfocus="if(this.value=='请输入关键词'){this.value=''}" onblur="if(this.value==''){this.value='请输入关键词'}">
<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
<input type="hidden" name="searchsubmit" value="yes">
<input type="submit" class="research fr" value="搜索">
</form>
</div>
<!-- 是否登录注册 -->
<div class="r_bq">
<?php if($_G['uid']) { ?>
<div class="form_login">
<div class="lf"><a href="home.php?mod=space&amp;uid=<?php echo $_G['uid'];?>"><?php echo avatar($_G[uid],small);?></a></div>
<p class="lf" style="margin-left:10px;font-size:14px;">欢迎回来，<a href="home.php?mod=space&amp;uid=<?php echo $_G['uid'];?>&amp;do=profile" target="_blank" title="访问我的空间"><?php echo $_G['member']['username'];?></a>！<a class="close" href="member.php?mod=logging&amp;action=logout&amp;formhash=<?php echo FORMHASH;?>">退出</a></p><div class="clear"></div>
<div class="lg1"><a href="home.php?mod=spacecp&amp;ac=credit&amp;showcredit=1">余额：<?php if($curcredits) { ?><?php echo $curcredits;?> <?php echo $creditname;?><?php } ?></a></div>
<div class="lg2"><a href="home.php?mod=spacecp&amp;ac=usergroup">用户组: <?php echo $_G['group']['grouptitle'];?><?php if($_G['member']['freeze']) { ?><span>(已冻结)</span><?php } ?></a></div>
<div class="lg3">上次登录：<?php echo $lastvisit;?></div>
</div>
<?php } else { ?>
<form action="member.php?mod=logging&amp;action=login&amp;loginsubmit=yes&amp;infloat=yes&amp;lssubmit=yes" method="post" name="form_login" class="form_login">
<p>登录</p>
<!-- <select name="fastloginfield">
<option value="username">用户名</option>
<?php if(getglobal('setting/uidlogin')) { ?>
<option value="uid">UID</option>
<?php } ?>
<option value="email">Email</option>
</select> -->
<div>
<input type="text" class="username" name="username" value="用户名/Email" onfocus="$('.username').css('color','#616c7b');if(this.value=='用户名/Email'){this.value='';}" onblur="if(this.value==''){$('.username').css('color','#b2b2b2');this.value='用户名/Email';}">
</div>
<div>
<input type="text" class="password" name="password" placeholder="密码">
</div>
<div class="l_row">
<label><input type="checkbox" name="cookietime" value="2592000" >自动登录</label>
<a href="member.php?mod=logging&amp;action=login&amp;viewlostpw=1" class="fr">找回密码</a>
</div>
<div class="clear">
<input type="submit" class="fl login" name="login" value="登录">
<input type="button" class="fr register" id="Is_register" name="register" value="注册">
</div>
<input type="hidden" name="quickforward" value="yes">
<input type="hidden" name="handlekey" value="ls">
</form>
<?php } ?>
</div>
<!-- 网站公告 -->
<div class="r_bq">
<div class="r_bd">
<p>网站公告：</p>
<ul>
<?php if($announcements) { if(is_array($announcements)) foreach($announcements as $item) { ?><li><a href="forum.php?mod=announcement&amp;id=<?php echo $item['id'];?>"><?php echo $item['subject'];?></a></li>
<?php } } else { ?>
<li><?php echo $lang['no_resource'];?></li>
<?php } ?>
</ul>
</div>
</div>
<!-- 推荐型号 -->
<div class="r_bq b_w">
<p class="r_zq">原厂专区</p>
<ul class="r_link">
<?php if($mfg_part) { if(is_array($mfg_part)) foreach($mfg_part as $item) { ?><li><a href="<?php echo $sheet_url;?>&pluginop=page&loid=<?php echo $item['id'];?>"><?php echo cutstr($item[name],12)?></a></li>
<?php } } else { ?>
<li><?php echo $lang['no_resource'];?></li>
<?php } ?>
</ul>
</div>
<div class="r_bq">
<?php echo $adv_customs['7'];?>
</div>


<!-- 网站达人 -->
<!-- <div class="r_bq last">
<div class="r_bd">
<div class="r_row">
<a href="#">
<img class="fl" src="<?php echo LO_PUB_IMG;?>rh.png" alt="产品图片" width="127" height="90">
</a>
<span class="fr">什么是集成电路</span><a class="fr" href="#">无损耗的奇迹......[详细]</a>
</div>
<p>放点什么好：达人、排行榜还是？</p>
<ul>
<li><a href="#">联网技术</a></li>
</ul>
</div>
</div> -->
</div>
<div class="clear"></div>
<div class="l_wid">
<div class="lf">
<div class="bq_head">
<span class="bq_title">论　坛</span><span>FORUM</span>
<a href="forum.php" class="more fr">查看更多》</a>
</div>
<div class="bk_cont">
<ul class="tab_fk"><?php if(is_array($forum_index)) foreach($forum_index as $forum) { ?><li><?php echo $forum['name'];?></li>
<?php } ?>
</ul><?php if(is_array($threadlist)) foreach($threadlist as $fk => $forumfid) { ?><ul class="tab_fk_cont circle2" <?php if($fk) { ?>style="display:none;"<?php } ?>>
<?php if($forumfid) { if(is_array($forumfid)) foreach($forumfid as $thread) { ?><li><a href="<?php echo $post_url;?><?php echo $thread['tid'];?>"><?php echo $thread['subject'];?></a></li>
<?php } } else { ?>
<li><?php echo $lang['no_resource'];?></li>
<?php } ?>
</ul>
<?php } ?>
<script>
$(".tab_fk li").mouseover(function(){
$(this).addClass("current");
$(this).siblings().removeClass("current");
var souyin=$(this).index();
$(".tab_fk_cont").eq(souyin).show().siblings(".tab_fk_cont").hide();
});
</script>
</div>
</div>
<!-- 活动栏目 -->
<div class="r_bq rt act_width">
<div class="r_bd"><?php if(is_array($activitys)) foreach($activitys as $k => $item) { if($k==0) { ?>
<div class="r_row">
<a href="<?php echo $activity_url;?>&pluginop=page&loid=<?php echo $item['id'];?>">
<img class="fl" src="<?php if($item['pic']) { echo $upload_common_path.'activity/'.LO_PIC.$item[pic]?><?php } else { } ?>" alt="<?php echo $no_pic_alt;?>" width="127" height="90">
</a>
<span class="fr"><?php echo cutstr($item[name],12)?></span>
<a class="fr" href="<?php echo $activity_url;?>&pluginop=page&loid=<?php echo $item['id'];?>"><?php echo cutstr($item[brief],10)?>[详细]</a>
</div>
<?php } else { if($k==1) { ?>
<p>更多活动：</p>
<ul>
<?php } ?>
<li><a href="<?php echo $activity_url;?>&pluginop=page&loid=<?php echo $item['id'];?>"><?php echo cutstr($item[name],12)?></a></li>
<?php } } ?>
</ul>
</div>
</div>
<div class="clear"></div>
<div class="bq_cont">
<?php echo $adv_customs['5'];?>
<?php echo $adv_customs['6'];?>
</div>
</div>
<div class="l_wid">
<div class="bq_cont lf" id="bq1">
<div class="bq_head">
<span class="bq_title">下　载</span><span>DOWNLOAD</span>
<a href="<?php echo $down_url;?>" class="fr more"> 查看更多》</a>
</div>
<ul class="tab_head"><?php if(is_array($down_nav)) foreach($down_nav as $k => $nav) { ?><li <?php if($k==0) { ?>class="current"<?php } ?>><?php echo $nav;?><span class="triangle <?php if($k<>0) { ?>hide<?php } ?>"></span></li>
<?php } ?>
</ul><?php if(is_array($downlist)) foreach($downlist as $k => $list) { ?><ul class="tab_cont" <?php if($k!=0) { ?>style="display:none"<?php } ?>>
<?php if($list) { if(is_array($list)) foreach($list as $lk => $third) { if($k==0) { ?>
<!-- 热门下载 --><?php $downnum=(count($list)-1);?><li class="down_data <?php if($lk==$downnum) { ?>last<?php } ?>"><a href="<?php echo $down_url;?>&pluginop=page&loid=<?php echo $third['id'];?>" title="<?php echo $third['name'];?>"><?php echo cutstr($third[name],30,'……');?></a></li>
<?php } elseif($k==1) { ?>
<!-- 下载排行 -->
<li class="down <?php if($lk==2) { ?>last<?php } ?>">
<p><?php echo $third['0'];?></p>
<ul class="down_top"><?php $downnum=(count($third[1])-1);?><?php if(is_array($third['1'])) foreach($third['1'] as $tk => $v) { ?><li <?php if($tk==$downnum) { ?>class="last"<?php } ?>>
<span <?php if($tk<=2) { ?>class="topico"<?php } ?>><?php echo $tk+1?></span>
<a href="<?php echo $down_url;?>&pluginop=page&loid=<?php echo $v['id'];?>" title="<?php echo $v['name'];?>"><!-- 【<?php echo $downnum;?>】 --><?php echo cutstr($v[name],36,'……');?></a>
</li>
<?php } ?>
</ul>
</li>
<?php } elseif($k==2) { ?>
<!-- DATASHEET下载 --><?php $downnum=(count($list)-1);?><li class="down_datasheet <?php if($lk==$downnum) { ?>last<?php } ?>">
<a href="<?php echo $sheet_url;?>&pluginop=page&loid=<?php echo $third['id'];?>" title="<?php echo $third['name'];?>"><?php echo cutstr($third[name],30,'……');?></a>
</li>
<?php } else { ?>
<li></li>
<?php } } } else { ?>
<li><?php echo $lang['no_resource'];?></li>
<?php } ?>
</ul>
<?php } ?>
</div>
<!-- 活动栏目 -->
<div class="r_bq rt act_width">
<div class="r_bd"><?php if(is_array($activitys)) foreach($activitys as $k => $item) { if($k==0) { ?>
<div class="r_row">
<a href="<?php echo $activity_url;?>&pluginop=page&loid=<?php echo $item['id'];?>">
<img class="fl" src="<?php if($item['pic']) { echo $upload_common_path.'activity/'.LO_PIC.$item[pic]?><?php } else { } ?>" alt="<?php echo $no_pic_alt;?>" width="127" height="90">
</a>
<span class="fr"><?php echo cutstr($item[name],12)?></span>
<a class="fr" href="<?php echo $activity_url;?>&pluginop=page&loid=<?php echo $item['id'];?>"><?php echo cutstr($item[brief],10)?>[详细]</a>
</div>
<?php } else { if($k==1) { ?>
<p>更多活动：</p>
<ul>
<?php } ?>
<li><a href="<?php echo $activity_url;?>&pluginop=page&loid=<?php echo $item['id'];?>"><?php echo cutstr($item[name],12)?></a></li>
<?php } } ?>
</ul>
</div>
</div>
</div>
<div class="clear"></div>
<div class="l_wid">
<div class="bq_cont lf" id="bq2">
<div class="bq_head">
<span class="bq_title">外　包</span><span>PROJECT</span>
<a href="<?php echo $pro_url;?>" class="fr more">查看更多》</a>
</div>
<ul class="tab_head"><?php if(is_array($pro_nav)) foreach($pro_nav as $k => $nav) { ?><li <?php if($k==0) { ?>class="current"<?php } ?>><?php echo $nav;?><span class="triangle <?php if($k<>0) { ?>hide<?php } ?>"></span></li>
<?php } ?>
</ul><?php if(is_array($prolist)) foreach($prolist as $k => $list) { ?><ul class="tab_cont circle" <?php if($k!=0) { ?>style="display:none"<?php } ?>>
<?php if($list) { if(is_array($list)) foreach($list as $lk => $c) { ?><li>
<a href="<?php echo $pro_url;?>&pluginop=page&loid=<?php echo $c['id'];?>">
<span class="pro_name"><?php echo cutstr($c[task_title],50)?></span>
<span class="pro_price"><?php echo $c['task_cash'];?></span>
<span class="pro_date"><?php echo dgmdate($c[start_time])?></span>
</a>
</li>
<?php } } else { ?>
<li><?php echo $lang['no_resource'];?></li>
<?php } ?>
</ul>
<?php } ?>
</div>
<!-- 活动栏目 -->
<div class="r_bq rt act_width">
<div class="r_bd"><?php if(is_array($activitys)) foreach($activitys as $k => $item) { if($k==0) { ?>
<div class="r_row">
<a href="<?php echo $activity_url;?>&pluginop=page&loid=<?php echo $item['id'];?>">
<img class="fl" src="<?php if($item['pic']) { echo $upload_common_path.'activity/'.LO_PIC.$item[pic]?><?php } else { } ?>" alt="<?php echo $no_pic_alt;?>" width="127" height="90">
</a>
<span class="fr"><?php echo cutstr($item[name],12)?></span>
<a class="fr" href="<?php echo $activity_url;?>&pluginop=page&loid=<?php echo $item['id'];?>"><?php echo cutstr($item[brief],10)?>[详细]</a>
</div>
<?php } else { if($k==1) { ?>
<p>更多活动22：</p>
<ul>
<?php } ?>
<li><a href="<?php echo $activity_url;?>&pluginop=page&loid=<?php echo $item['id'];?>"><?php echo cutstr($item[name],12)?></a></li>
<?php } } ?>
</ul>
</div>
</div>
</div>
<div class="clear"></div>
<div class="l_wid">
<div class="bq_cont lf" id="bq3">
<div class="bq_head">
<span class="bq_title">二手交易</span><span>TRADING</span>
<a href="<?php echo $trade_url;?>" class="fr more">查看更多》</a>
</div>
<ul class="tab_head"><?php if(is_array($trade_nav)) foreach($trade_nav as $k => $nav) { ?><li <?php if($k==0) { ?>class="current"<?php } ?>><?php echo $nav;?><span class="triangle <?php if($k<>0) { ?>hide<?php } ?>"></span></li>
<?php } ?>
</ul><?php if(is_array($tradelist)) foreach($tradelist as $k => $list) { ?><ul class="tab_cont" <?php if($k!=0) { ?>style="display:none"<?php } ?>>
<?php if($list) { if($k==0) { if(is_array($list)) foreach($list as $lk => $c) { ?><li <?php if($lk%3==2) { ?>class="last"<?php } ?>>
<a href="<?php echo $trade_url;?>&pluginop=page&loid=<?php echo $c['id'];?>"><img src="<?php if($c['pic']) { echo $upload_common_path.($k==2?'trade/':'trade/').LO_PIC.$c['pic']?><?php } else { } ?>" alt="<?php echo $no_pic_alt;?>" width="278px" height="188px"></a>
<p class="trad_row1"><?php echo cutstr($c['name'],12)?></p>
<p class="trad_row2"><span>上架时间：<?php if($c['on_time']) { ?><?php echo $c['on_time'];?><?php } else { ?>未知<?php } ?></span><span class="last">发布者：<?php if($c['username']) { ?><?php echo $c['username'];?><?php } else { ?>未知<?php } ?></span></p>
<p class="trad_row3">
<span class="price"><?php echo intval($c['shop_price'])?><?php echo $c['price_unit'];?>元</span><span>市场价:<?php echo intval($c['market_price'])?><?php echo $c['price_unit'];?>元</span><span class="price_ico">超值</span>
</p>
</li>
<?php } } elseif($k==1) { if(is_array($list)) foreach($list as $lk => $c) { ?><li class="trade2">
<span><b><?php echo $c['username'];?></b>购买了：</span>
<a href="<?php echo $trade_url;?>&pluginop=page&loid=<?php echo $c['id'];?>"><?php echo cutstr($c['name'],20,'……');?></a>
</li>
<?php } } } else { ?>
<li><?php echo $lang['no_resource'];?></li>
<?php } ?>
</ul>
<?php } ?>
<ul >
</ul>
</div>
<!-- 活动栏目 -->
<div class="r_bq rt act_width">
<div class="r_bd"><?php if(is_array($activitys)) foreach($activitys as $k => $item) { if($k==0) { ?>
<div class="r_row">
<a href="<?php echo $activity_url;?>&pluginop=page&loid=<?php echo $item['id'];?>">
<img class="fl" src="<?php if($item['pic']) { echo $upload_common_path.'activity/'.LO_PIC.$item[pic]?><?php } else { } ?>" alt="<?php echo $no_pic_alt;?>" width="127" height="90">
</a>
<span class="fr"><?php echo cutstr($item[name],12)?></span>
<a class="fr" href="<?php echo $activity_url;?>&pluginop=page&loid=<?php echo $item['id'];?>"><?php echo cutstr($item[brief],10)?>[详细]</a>
</div>
<?php } else { if($k==1) { ?>
<p>更多活动：</p>
<ul>
<?php } ?>
<li><a href="<?php echo $activity_url;?>&pluginop=page&loid=<?php echo $item['id'];?>"><?php echo cutstr($item[name],12)?></a></li>
<?php } } ?>
</ul>
</div>
<div class="r_bd">
<p>站点帮助：</p>
<ul>
<?php if($faqs) { if(is_array($faqs)) foreach($faqs as $item) { ?><li><a href="misc.php?mod=faq&amp;action=faq&amp;id=<?php echo $item['fpid'];?>&amp;messageid=<?php echo $item['id'];?>"><?php echo $item['title'];?></a></li>
<?php } } else { ?>
<li><?php echo $lang['no_resource'];?></li>
<?php } ?>
</ul>
</div>
</div>

<div class="clear"></div>
</div>
<script>
$("#bq1 .tab_head li").mouseover(function(){
$(this).addClass('current');
$(this).siblings().removeClass('current');
$("#bq1>.tab_head>li>span").addClass('hide')
var souyin=$(this).index(); 
$("#bq1>.tab_head>li>span").eq(souyin).removeClass('hide')
$("#bq1 .tab_cont").eq(souyin).show().siblings("#bq1 .tab_cont").hide();
});
$("#bq2 .tab_head li").mouseover(function(){
$(this).addClass('current');
$(this).siblings().removeClass('current');
$("#bq2>.tab_head>li>span").addClass('hide')
var souyin=$(this).index(); 
$("#bq2>.tab_head>li>span").eq(souyin).removeClass('hide')
$("#bq2 .tab_cont").eq(souyin).show().siblings("#bq2 .tab_cont").hide();
});
$("#bq3 .tab_head li").mouseover(function(){
$(this).addClass('current');
$(this).siblings().removeClass('current');
$("#bq3>.tab_head>li>span").addClass('hide')
var souyin=$(this).index(); 
$("#bq3>.tab_head>li>span").eq(souyin).removeClass('hide')
$("#bq3 .tab_cont").eq(souyin).show().siblings("#bq3 .tab_cont").hide();
});
</script>
</div>
<script type="text/javascript">
$('#Is_register').click(function(){
// name = $(this).attr('name');
location.href = 'member.php?mod=register';
})
</script>


<!-- 底部栏目 -->
<div class="user_img">
<p class="bk_head">用户自拍</p>
<ul>
<?php if($usrinfos) { if(is_array($usrinfos)) foreach($usrinfos as $ufo) { ?><li>
<a href="home.php?mod=space&amp;uid=<?php echo $ufo['uid'];?>"><?php echo avatar($ufo[uid],big);?></a>
<p><?php echo cutstr($ufo[bio],75)?></p>
</li>
<?php } } else { ?>
<li><?php echo $lang['no_resource'];?></li>
<?php } ?>
</ul>
</div>

<div class="friend_links clear">
<p>友情链接</p>
<ul><?php if(is_array($friendlinks)) foreach($friendlinks as $link) { ?><li><a href="<?php echo $link['url'];?>"><?php if($link['logo']) { ?><img src="<?php echo $link['logo'];?>" title="<?php echo $link['name'];?>"><?php } else { ?><?php echo $link['name'];?><?php } ?></a></li>
<?php } ?>
</ul>
</div>
</div>
</div>

<!-- 通用底部 --><?php include template('common/footer'); ?>