<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>
<!-- 我要发布 -->
<div class="phr_dc">
    <div class="phr_pro_Content">
        <form action="<?php echo $form['action'];?>" method="post" enctype="multipart/form-data" class="phr_mycase" id="demoform">
            <h2><?php if($task['tid']) { ?>修改<?php } else { ?>发布<?php } ?></h2>
            <div>
                <span class="red">*</span>名称：
                <input type="text" name="task_title" value="<?php echo $task['task_title'];?>" placeholder="请输入项目标题" <?php echo $Validform['task_title'];?> />
            </div>
            <div>
                <span class="red">*</span>任务类型：
                <select name="task_type">
                    <?php if(is_array($arrProTaskType)) foreach($arrProTaskType as $k => $v) { ?>                    <option <?php if($task['task_type']==$k) { ?>selected<?php } ?> value="<?php echo $k;?>"><?php echo $v;?></option>
                    <?php } ?>
                </select>
            </div>
            <div>
                <span class="red">*</span>应用领域：
                <select name="indus_id">
                    <?php if(is_array($industrys)) foreach($industrys as $item) { ?>                    <option <?php if($item['cid']==$task['indus_id']) { ?>selected<?php } ?> value="<?php echo $item['cid'];?>"><?php echo $item['name'];?></option>
                    <?php } ?>
                </select>
            </div>
            <div>标签：<input type="text" name="task_tags" value="<?php echo $task['task_tags'];?>" placeholder="如有多个用英文分号（；）隔开"></div> 
            <div><span class="red">*</span>项目内容：<textarea name="task_desc" placehoolder="请输入内容"><?php echo $task['task_desc'];?></textarea></div>
            <div><span class="red">*</span>明确预算：<input type="number" name="budget" value="<?php echo $task['budget'];?>" placeholder="数字型"></div>
            <!-- <div>任务赏金：<input type="number" name="task_cash" value="<?php echo $task['task_cash'];?>" placeholder="数字型，可不填"></div> -->
            <div>
                <span class="red">*</span>开始时间：
                <input type="text" name="start_time" value="<?php echo $task['start_time'];?>" id="start_time">
            </div>
            <div>
                <span class="red">*</span>截止时间：
                <input type="text" name="end_time" value="<?php echo $task['end_time'];?>" id="end_time">
            </div>
            <div>
                <span class="red">*</span>发布区域：
                <select name="province">
                    <?php if(is_array($district_prov)) foreach($district_prov as $item) { ?>                    <option <?php if($item['id']==$task['province']) { ?>selected<?php } ?> value="<?php echo $item['id'];?>"><?php echo $item['name'];?></option>
                    <?php } ?>
                </select>
            </div>
            <div>联系方式：<textarea name="contact" placehoolder="手机，邮箱，QQ等，多个请用英文';'隔开"><?php echo $task['contact'];?></textarea></div>
            <div>
                上传附件：<input type="file" name="task_file">
                <?php if($task['task_file']) { ?><a href="<?php echo $task['attach_url'];?>"><?php echo $task['task_file'];?></a><?php } ?>
            </div>
            <div>
                <input type="hidden" name="formhash" value="<?php echo FORMHASH;?>" />
                <input type="hidden" name="ac" value="op">
                <input type="hidden" name="tid" value="<?php echo $task['tid'];?>">
                <input type="hidden" name="task_oldfile" value="<?php echo $task['task_file'];?>">
                <button type="submit" name="publishsubmit" value="true"><strong><?php echo $form['button'];?></strong></button>
            </div>
        </form>
    </div>
</div>