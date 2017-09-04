<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('trade_list');?><?php include template('common/header'); ?><div class="person_home">
    <!-- 个人中心左侧导航 -->
    <?php include template('home/home_left'); ?>    <style>
        /* m_zlxg */
        .m_zlxg{ width:93px; height:30px; line-height:30px;cursor:pointer;float:left;margin:0 10px 0 0;display:inline;background:url(../../image/zlxg2.jpg) no-repeat;}
        .m_zlxg p{ width:71px; padding-left:10px; overflow:hidden; line-height:30px; color:#333333; font-size:12px; font-family:"微软雅黑";text-overflow:ellipsis; white-space:nowrap;}
        .m_zlxg2{ position:absolute; top:29px; border:1px solid #ded3c1;background:#fff; width:91px; display:none; max-height:224px;-height:224px; overflow-x:hidden; overflow-y:auto;white-space:nowrap;}
        .m_zlxg2 li{line-height:28px;white-space:nowrap; padding-left:10px;font-family:"微软雅黑";color:#333333; font-size:12px;}
        .m_zlxg2 li:hover{ color:#7a5a21;}
        .clear{both:clear;}
    </style>

    <div class="person_homer rt">
        <?php include template('home/home_righton'); ?>        <div class="phr_content">
            <!-- <div class="phr_bordermess">写点什么……</div> -->
            <div class="phr_MyReward">
                <ul class=" phr_tab">
                    <li  class="phr_active">闲置品列表</li>
                    <li >我要发布</li>
                </ul>
                <div class="phr_tab_content">
                    <div class="phr_dc" style="display:block;">
                    <ul class="talents_d talents_pro_case">
                            <?php if($list) { ?>
                            <?php if(is_array($list)) foreach($list as $nav) { ?>                            <li>
                                <div class="goods1">
                                    <div class="talents_pro_case_pic lf">
                                        <a href=""><img src="/uploads/trade/pic/<?php echo $nav['pic'];?>"></a>
                                    </div>
                                    <div class="project_case_content lf">
                                        <h4><a href="plugin.php?id=lotrade:trade&amp;pluginop=page&amp;loid=<?php echo $nav['id'];?>" class="orange"><?php echo $nav['name'];?></a></h4>
                                        <ul class="phr_goods">
                                            <li>地区：<span><?php echo $nav['area'];?></span></li>
                                            <li>成色：<span>全新</span></li>
                                            <li>数量：<span class="num"><?php echo $nav['store_count'];?></span></li>
                                            <li>价格：<span class="money">￥<?php echo $nav['shop_price'];?></span></li>
                                        </ul>                                       
                                        <p class="phr_sg phr_sg_txt">
                                            <?php echo $nav['brief'];?>
                                        </p>
                                        <p class="gray">
                                            <button type="text" style="cursor: pointer;" onclick="window.location.href='home.php?mod=trade&do=del&id=<?php echo $nav['id'];?>&url=list&tab=trade'" class="rt phr_ig_del" >删除</button>
                                        </p>
                                    </div>
                                </div>
                                <div class="goods2" style="display:none;background-color:red;"></div>
                                <div class="both"></div>
                            </li>
                            <?php } ?>
                            <?php } else { ?>
                            <li>暂无信息</li>
                            <?php } ?>
                        </ul>
                        <?php echo $multipage;?>
                    </div>

                    <div class="phr_dc" style="display:none;">
                        <div class="phr_ig_form">
                            <form action="home.php?mod=trade&amp;do=list_post" method="post" enctype="multipart/form-data" id="demoform">
                                <h2>我要发布</h2>
                                <ul class="phr_ig_form_ul">
                                    <li>
                                        <span class="lf">产品标题：</span>
                                        <div class="lf phr_ig_fu_content "><input name="name" type="text" placeholder="请输入商品名称" <?php echo $Validform['name'];?>/></div>
                                        <div class="clear"></div>
                                    </li>
                                    <li>
                                        <span class="lf">产品数量：</span>
                                        <div class="lf phr_ig_fu_content "><input name="store_count" type="text" placeholder="请输入商品数量" <?php echo $Validform['store_count'];?>/></div>
                                        <div class="clear"></div>
                                    </li>
                                    <li>
                                        <span class="lf">产品价格：</span>
                                        <div class="lf phr_ig_fu_content ">
                                            <input name="shop_price" type="text" placeholder="请输入商品价格" <?php echo $Validform['shop_price'];?>/>
                                            <!-- onchange="this.value=price_format(this.value)" -->
                                        </div>
                                        <div class="clear"></div>
                                    </li>
                                    <li>
                                        <span class="lf">产品地区：</span>
                                        <div class="lf phr_ig_fu_content ">
                                            <select name="trade_type2">
                                                <?php if(is_array($district)) foreach($district as $m) { ?>                                                <option value="<?php echo $m['id'];?>" <?php if($page['trade_type2']==$m['id']) { ?>selected<?php } ?>><?php echo $m['name'];?></option>
                                                <?php } ?>
                                            </select>
                                            <div id="sjld" style="width:520px;position:relative;">
                                                <!-- <div class="m_zlxg" id="shenfen">
                                                    <p title="">选择省份</p>
                                                    <div class="m_zlxg2">
                                                        <ul></ul>
                                                    </div>
                                                </div> -->
                                               <!--  <div class="m_zlxg" id="chengshi">
                                                    <p title="">选择城市</p>
                                                    <div class="m_zlxg2">
                                                        <ul></ul>
                                                    </div>
                                                </div> -->
                                                <!-- <div class="m_zlxg" id="quyu">
                                                    <p title="">选择区域</p>
                                                    <div class="m_zlxg2">
                                                        <ul></ul>
                                                    </div>
                                                </div> -->
                                                <input id="sfdq_num" type="hidden" value="" />
                                                <input id="csdq_num" type="hidden" value="" />
                                                <input id="sfdq_tj" type="hidden" value="" />
                                                <input id="csdq_tj" type="hidden" value="" />
                                                <input id="qydq_tj" type="hidden" value="" />
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </li>
                                    <li><span class="lf">交易类型：</span>
                                        <div class="lf phr_ig_fu_content ">
                                            <div class="fy_select">                 
                                                <select name="trade_type1">
                                                    <?php if(is_array($trade_type1)) foreach($trade_type1 as $m) { ?>                                                    <option value="<?php echo $m['id'];?>" <?php if($page['trade_type1']==$m['id']) { ?>selected<?php } ?>><?php echo $m['name'];?>
                                                    </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </li>
                                    <li>
                                        <span class="lf">分类：</span>
                                        <select name="cat_id" <?php echo $Validform['cat_id'];?>>
                                            <option <?php if(!$page['cat_id']) { ?>selected<?php } ?> value="">-->无极<--</option>
                                            <?php if(is_array($cates)) foreach($cates as $p) { ?>                                            <option <?php if($page['cat_id']==$p['cid']) { ?>selected<?php } ?> value="<?php echo $p['cid'];?>"><?php echo $p['name'];?></option>
                                            <?php if(is_array($p['child'])) foreach($p['child'] as $c) { ?>                                            <option <?php if($page['cat_id']==$c['cid']) { ?>selected<?php } ?> value="<?php echo $c['cid'];?>"><?php echo str_repeat('&nbsp;',4).$c[name]?></option>
                                            <?php if(is_array($c['child'])) foreach($c['child'] as $t) { ?>                                            <option <?php if($page['cat_id']==$t['cid']) { ?>selected<?php } ?> value="<?php echo $t['cid'];?>"><?php echo str_repeat('&nbsp;',8).$t[name]?></option>
                                            <?php } ?>
                                            <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </li>
                                    <li>
                                        <span class="lf">品牌：</span>
                                        <?php if(is_array($brands)) foreach($brands as $k => $v) { ?>                                        <label><input type="radio" style="width:20px;" name="brand_id" value="<?php echo $v['id'];?>" <?php if($k==0) { ?>checked <?php echo $Validform['brand_id'];?><?php } ?> /><?php echo $v['name'];?></label>
                                        <?php } ?>
                                    </li>
                                    <li>
                                        <span class="lf">产品简介：</span>
                                        <div class="lf phr_ig_fu_content ">
                                            <input type="text" name="brief" placeholder="请输入商品简介" min-height="60" <?php echo $Validform['brief'];?>/>
                                        </div>
                                        <div class="clear"></div>
                                    </li>
                                    <li>
                                        <span>产品详情：</span>
                                        <?php include template('_public/ueditor'); ?>                                    </li>
                                    <li style="overflow:hidden;">
                                        <span class="lf">原始图片：</span>
                                        <input type="file" name="pic"  <?php echo $Validform['pic'];?> />
                                    </li>
                                    <li><button style="cursor: pointer;" type="submit">我要发布</button></li>
                                </ul>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="<?php echo LO_PUB_JS;?>common.js" type="text/javascript"></script>
        <script src="<?php echo LO_PUB_JS;?>address.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(function(){
                $("#sjld").sjld("#shenfen","#chengshi","#quyu");
            });
           function price_format(s){
                if(/[^0-9\.]/.test(s)){ 
                    return "";
                }
                s=s.replace(/^(\d*)$/,"$1.");
                s=(s+"00").replace(/(\d*\.\d\d)\d*/,"$1");
                s=s.replace(".",",");
                var re=/(\d)(\d{3},)/;
                while(re.test(s))
                        s=s.replace(re,"$1,$2");
                s=s.replace(/,(\d\d)$/,".$1");
                return  s.replace(/^\./,"0.")
            }
        </script>
    </div>
</div>

<!-- 通用底部 --><?php include template('common/footer'); ?>