<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); if(in_array($_G['basescript'],array('forum'))) { ?>
<script type="text/javascript">
// var jq = jQuery.noConflict();// noConflict() 方法会释放会 $ 标识符的控制，这样其他脚本就可以使用它了。
jq(function(){
    jq("#demoform").Validform({
        tiptype:1, 
        showAllError:true,
        datatype:{
            "f":/^-?[1-9]+(\.\d+)?$|^-?0(\.\d+)?$|^-?[1-9]+[0-9]*(\.\d+)?$/,// 小数（浮点数）
            "f2":/^[0-9]+(\.[0-9]{2})?$/,// 小数（浮点数）允许0
            "f2_0":/^([1-9]+(\.[0-9]{2})?|0\.[1-9][0-9]|0\.0[1-9])$/,// 小数（浮点数）不允许0
        },
    });
});
</script>
<?php } else { ?>
<script type="text/javascript">
$(function(){
    // $("#demoform").Validform();// 就这一行代码！
    $("#demoform").Validform({
        // btnSubmit:"#btn_sub",// 指定触发对象按钮
        // btnReset:".btn_reset",// 指定重置对象按钮
        tiptype:3, 
        // ignoreHidden:false,
        // dragonfly:false,
        // tipSweep:true,
        // label:".label",
        showAllError:true,
        // postonce:true,
        // ajaxPost:true,
        datatype:{
            "*6-20":/^[^\s]{6,20}$/,// 任意字符6-20个
            "z2-4":/^[\u4E00-\u9FA5\uf900-\ufa2d]{2,4}$/,// 
            "f":/^-?[1-9]+(\.\d+)?$|^-?0(\.\d+)?$|^-?[1-9]+[0-9]*(\.\d+)?$/,// 小数（浮点数）
            "f2":/^[0-9]+(\.[0-9]{2})?$/,// 小数（浮点数）允许0
            "f2_0":/^([1-9]+(\.[0-9]{2})?|0\.[1-9][0-9]|0\.0[1-9])$/,// 小数（浮点数）不允许0
            "username":function(gets,obj,curform,regxp){
                //参数gets是获取到的表单元素值，obj为当前表单元素，curform为当前验证的表单，regxp为内置的一些正则表达式的引用;
                var reg1=/^[\w\.]{4,16}$/,
                    reg2=/^[\u4E00-\u9FA5\uf900-\ufa2d]{2,8}$/;
                if(reg1.test(gets)){return true;}
                if(reg2.test(gets)){return true;}
                return false;
                //注意return可以返回true 或 false 或 字符串文字，true表示验证通过，返回字符串表示验证失败，字符串作为错误提示显示，返回false则用errmsg或默认的错误提示;
            },
            "phone":function(){
                // 5.0 版本之后，要实现二选一的验证效果，datatype 的名称 不 需要以 "option_" 开头;    
            },
            "idcard":function(){
            }
        },
    });



    // 下面的是自定义
    /*var demo=$("#demoform").Validform({
        tiptype:3,
        label:".label",
        showAllError:true,
        datatype:{
            "zh1-6":/^[\u4E00-\u9FA5\uf900-\ufa2d]{1,6}$/
        },
        ajaxPost:true
    });
    
    //通过$.Tipmsg扩展默认提示信息;
    //$.Tipmsg.w["zh1-6"]="请输入1到6个中文字符！";
    demo.tipmsg.w["zh1-6"]="请输入1到6个中文字符！";
    
    demo.addRule([{
        ele:".inputxt:eq(0)",
        datatype:"zh2-4"
    },
    {
        ele:".inputxt:eq(1)",
        datatype:"*6-20"
    },
    {
        ele:".inputxt:eq(2)",
        datatype:"*6-20",
        recheck:"userpassword"
    },
    {
        ele:"select",
        datatype:"*"
    },
    {
        ele:":radio:first",
        datatype:"*"
    },
    {
        ele:":checkbox:first",
        datatype:"*"
    }]);*/
    /*// 创建自定义提示
    tiptype:function(msg,o,cssctl){
        //msg：提示信息;
        //o:{obj:*,type:*,curform:*},
        //obj指向的是当前验证的表单元素（或表单对象，验证全部验证通过，提交表单时o.obj为该表单对象），
        //type指示提示的状态，值为1、2、3、4， 1：正在检测/提交数据，2：通过验证，3：验证失败，4：提示ignore状态, 
        //curform为当前form对象;
        //cssctl:内置的提示信息样式控制函数，该函数需传入两个参数：显示提示信息的对象 和 当前提示的状态（既形参o中的type）;
    }*/
});
</script>
<?php } ?>
