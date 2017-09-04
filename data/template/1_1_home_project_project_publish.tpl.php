<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('project_publish');?><?php include template('common/header'); ?><div class="person_home">
    <!-- 个人中心左侧导航 -->
    <?php include template('home/home_left'); ?>    <script src="<?php echo LO_PUB_ORG;?>laydate/laydate.js" type="text/javascript"></script>
    <div class="person_homer rt">
        <?php include template('home/home_righton'); ?>        <div class="phr_content">
            <div class="phr_bordermess"></div>
            <div class="phr_MyReward">
                <ul class="phr_tab">
                    <li <?php if($ac=='list') { ?>class="phr_active"<?php } ?>><a href="<?php echo $jumpurl;?>">我发布的项目</a></li>
                    <li <?php if($ac=='page') { ?>class="phr_active"<?php } ?>><a href="<?php echo $jumpurl;?>&ac=page"><?php if($task['tid']) { ?>修改项目<?php } else { ?>我要发布<?php } ?></a></li>
                </ul>
                <div class="phr_tab_content">
                    <?php if($ac=='list') { ?>
                        <!-- 我的项目列表 -->
                        <?php include template('home/project/project_publish_list'); ?>                    <?php } elseif($ac=='page') { ?>
                        <!-- 我要发布 -->
                        <?php include template('home/project/project_publish_page'); ?>                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    !function(){
        laydate.skin('molv');//切换皮肤，请查看skins下面皮肤库
        laydate({elem: '#demo'});//绑定元素
    }();
    // 当前日期 laydate.now()
    //日期范围限制
    var start = {
        elem: '#start_time',
        format: 'YYYY-MM-DD',
        min: laydate.now(), //设定最小日期为当前日期
        max: '2050-00-00', //最大日期
        istime: true,
        istoday: false,
        choose: function(datas){
            end.min = datas; //开始日选好后，重置结束日的最小日期
            end.start = datas //将结束日的初始值设定为开始日
        }
    };
    laydate(start);
    var end = {
        elem: '#end_time',
        format: 'YYYY-MM-DD',
        min: laydate.now(),
        max: '2050-00-00',
        istime: true,
        istoday: false,
        choose: function(datas){
            start.max = datas; //结束日选好后，充值开始日的最大日期
        }
    };
    laydate(end);
</script>
<!-- 通用底部 --><?php include template('common/footer'); ?>