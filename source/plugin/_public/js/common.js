/*****论坛个人页面tab切换****/
$(function(){
    window.onload=function(){
        var $li=$(".phr_tab li");
        var $div=$(".phr_tab_content .phr_dc");
        $li.click(function(){
            var $this=$(this);
            var $t=$this.index();
            $li.removeClass();
            $this.addClass('phr_active');
            $div.css('display','none');
            $div.eq($t).css('display','block');
        })
    }
})

/****selec下拉公共样式****/
function stopPropagation(e) {
    if (e.stopPropagation)
        e.stopPropagation();//停止冒泡  非ie
    else
        e.cancelBubble = true;//停止冒泡 ie
}
$(document).bind('click',function(){
    $('.fy_options').css('display','none');
});
$('.fy_select').bind('click',function(e){
    //写要执行的内容....吥啦不啦
    $(this).find('.fy_options').css('display','block');
    $('.fy_options li').click(function(){
        var txt=$(this).text();
        $('.fy_select h3').text(txt);

    })
    stopPropagation(e);//调用停止冒泡方法,阻止document方法的执行
});

/***出售闲置品的上传图片预览***/
//下面用于多图片上传预览功能 以下两个函数可合并
function setImagePreviews1(avalue) {
    var docObj1 = document.getElementById("doc1");
    var dd1 = document.getElementById("dd1");
    dd1.innerHTML = "";
    var Sys={};
    var userAgent = window.navigator.userAgent.toLowerCase();
    if (window.ActiveXObject) {
        Sys.ie = userAgent.match(/msie ([\d.]+)/)[1];
    }
    if(Sys.ie==9.0||Sys.ie==8.0||Sys.ie==7.0||Sys.ie==6.0){
           alert("您的浏览器版本过低，不支持图片预览，请升级版本");
    }else{
        var fileList1 = docObj1.files;
        for (var j = 0; j < fileList1.length; j++) {
            dd1.innerHTML += '<div style="float:left"><img id="img1'+j+'"/></div>';
            var imgObjPreview1 = document.getElementById("img1"+j);
            if (docObj1.files && docObj1.files[j]) {
                //火狐下，直接设img属性
                imgObjPreview1.style.display = 'block';
                imgObjPreview1.style.width = '600px';
                //imgObjPreview1.style.height = '180px';
                //imgObjPreview.src = docObj.files[0].getAsDataURL();
                //火狐7以上版本不能用上面的getAsDataURL()方式获取，需要一下方式
                imgObjPreview1.src = window.URL.createObjectURL(docObj1.files[j]);
            } else {
                //IE下，使用滤镜
                docObj1.select();
                var imgSrc1 = document.selection.createRange().text;
                var localImagId1 = document.getElementById("imgL" + j);
                //必须设置初始大小
                localImagId.style.width = "600px";
                //localImagId.style.height = "500px";
                //图片异常的捕捉，防止用户修改后缀来伪造图片
                try {
                    localImagId.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale)";
                    localImagId.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = imgSrc1;
                } catch (e) {
                    alert("您上传的图片格式不正确，请重新选择!");
                    return false;
                }
                imgObjPreview1.style.display = 'none';
                document.selection.empty();
            }
        }
    }
    return true;
}

function setImagePreviews(avalue) {
    var docObj = document.getElementById("doc");
    var dd = document.getElementById("dd");
    dd.innerHTML = "";
    var Sys={};
    var userAgent = window.navigator.userAgent.toLowerCase();
    console.log(userAgent);
    if (window.ActiveXObject) {
        Sys.ie = userAgent.match(/msie ([\d.]+)/)[1];
    }
    if(Sys.ie==9.0||Sys.ie==8.0||Sys.ie==7.0||Sys.ie==6.0){
        alert("您的浏览器版本过低，不支持图片预览，请升级版本");
    }else{
        var fileList = docObj.files;
        for (var i = 0; i < fileList.length; i++) {
            dd.innerHTML += "<div style='float:left' > <img id='img" + i + "'  /> </div>";
            var imgObjPreview = document.getElementById("img"+i);
            if (docObj.files && docObj.files[i]) {
                //火狐下，直接设img属性
                imgObjPreview.style.display = 'block';
                imgObjPreview.style.width = '60px';
                imgObjPreview.style.height = '80px';
                //imgObjPreview.src = docObj.files[0].getAsDataURL();
                //火狐7以上版本不能用上面的getAsDataURL()方式获取，需要一下方式
                imgObjPreview.src = window.URL.createObjectURL(docObj.files[i]);
            } else {
                //IE下，使用滤镜
                docObj.select();
                var imgSrc = document.selection.createRange().text;
                var localImagId = document.getElementById("img" + i);
                //必须设置初始大小
                localImagId.style.width = "80px";
                localImagId.style.height = "120px";
                //图片异常的捕捉，防止用户修改后缀来伪造图片
                try {
                    localImagId.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale)";
                    localImagId.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = imgSrc;
                } catch (e) {
                    alert("您上传的图片格式不正确，请重新选择!");
                    return false;
                }
                imgObjPreview.style.display = 'none';
                document.selection.empty();
            }
        }
    }
    return true;
}

/***************/
$('.outsourcing_remove,.talents_remove,.my_case_remove').click(function(){
    $(this).parent().parent().parent().remove();
});
$('.phr_project_remove,.phr_PS_remove,.phr_PR_remove').click(function(){
    $(this).parent().parent().remove();
});
$('.mpi_edit').click(function(){
    $(this).parent().parent().remove();
})

/******二手闲置区******/
$('.phr_tran').click(function(){
    $(this).parent().parent().remove();
});
$('.phr_ig_del').click(function(){
    $(this).parent().parent().parent().remove();
})

/*****参与的活动删除按钮****/
$('.mycollect_remove,.phr_PA_remove,.cgb_remove').click(function(){
    $(this).parent().parent().remove();
})

$('.data_upload input.Date_tags').blur(function(){
    $x=$('.Date_tags').val();
    if($x.length>=1){
        $('.sb').css('display','none');
    }else{
        $('.sb').css('display','inline-block');
    }
})
$('.data_upload input[type="number"]').blur(function(){
    $x=$('.Date_jifen').val();
    if($x.length>=1){
        $('.sb1').css('display','none');
    }else{
        $('.sb1').css('display','inline-block');
    }
})

/*****我的自拍input*******/
$('#zp_info').focus(function(){
    $(this).parent().find('.zp_info').css('display','none');
    $(this).parent().find('.zp_info2').css('display','none');
    $(this).parent().find('.zp_info1').css('display','block');
})

function maxlength(field, countfield, maxlimit) {
    if (field.value.length > maxlimit)
        field.value = field.value.substring(0,maxlimit);
    else
        countfield.value = maxlimit - field.value.length;
}

/*******我的收藏鼠标放上去会显示字*******/
$('.phr_mysc').mouseover(function(){
    $txt=$('.phr_mysc_content').text();
    console.log($txt);
    $('.phr_mysc_span').css('display','block').html($txt);
})
$('.phr_mysc').mouseout(function(){
    $('.phr_mysc_span').css('display','none');
})
$('#MS_input').click(function(){
    $('body').addClass('')
})

/*******我发布的项目********/
function getRow(obj){
    var i = 0;
    while(obj.tagName.toLowerCase()!="tr"){
        obj = obj.parentNode;
        if(obj.tagName.toLowerCase()=="table") return null;
    }
    return obj;
}
function delRow(obj,id){
    var tr = getRow(obj);
    if(tr != null) {
        var table = $('.mpi_remove').attr('data-table');
        // console.log(table);console.log(id);
        $.ajax({
            url: '/ajaxopera.php',
            type: 'POST',
            // dataType: 'json',
            data: {tag:'del',loid:id,table:table},
            success:function(data) {
                // console.log(data);console.log(tr);
                if (data) {
                    tr.parentNode.removeChild(tr);// 移除当前tr
                } else {
                    return;
                }
            }
        });
    } else {
        throw new Error("the given object is not contained by the table");
    }
}

function pingjia(obj,loid){
    var fy= '<form action="plugin.php?pl=project&pluginop=pds&loid=8&c=leave_m" method="post">'+
            '<div class="items_eval">'+
            '<textarea name="content" placeholder="请发表内容"></textarea>'+
            '<input type="hidden" name="formhash" value="">'+
            '<input type="hidden" name="loid" value="'+loid+'">'+
            '<input type="hidden" name="gUserBid" value="1">'+
            '<input type="submit" value="提交" />'+
            '</div></form>';
    $(obj).css('display','none').parent().parent().append(fy);
}

