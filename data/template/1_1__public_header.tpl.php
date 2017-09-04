<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>

<!-- 当文件中不含原系统 代码时 可用此头 --><?php include template('_public/head_core'); ?>    <link type="text/css" rel="stylesheet" href="<?php echo LO_PUB_CSS;?>common.css">
    <script src="<?php echo LO_PUB_JS;?>jquery-1.12.1.min.js" type="text/javascript"></script>
    <link type="text/css" rel="stylesheet" href="<?php echo LO_PUB_ORG;?>validform/validform.css">
    <script src="<?php echo LO_PUB_ORG;?>validform/Validform_v5.3.2_min.js" type="text/javascript"></script>
    <?php if(THISPLUG == 'lodatasheet') { ?>
        <link type="text/css" rel="stylesheet" href="<?php echo LO_PUB_CSS;?>datasheet.css">
        <link rel="stylesheet" href="<?php echo LO_PUB_CSS;?>talents.css">
        <!-- <script src="<?php echo LO_PUB_JS;?>jquery-1.11.3.min.js" type="text/javascript"></script> -->
    <?php } elseif(THISPLUG == 'lodown') { ?>
        <link rel="stylesheet" href="<?php echo LO_PUB_CSS;?>down.css"/>
        <style>
            .f_down_t1 span {float: left;width: 80px;}
            .f_down_tc {float:left;width: 730px;}
            .f_down_tc1h{word-break:keep-all;height:25px;overflow:hidden; }
            .f_down_tc li{display:inline-block;height:25px;line-height:25px;margin:0 5px; overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}
            .f_down_tc li>a {font-size: 14px;color:#666;white-space:nowrap;}
            .luntan_more,.luntan_shouqi{float:right;color:blue;font-size:14px;cursor:pointer;}
        </style>
    <?php } elseif(THISPLUG == 'loactivity') { ?>
        <link rel="stylesheet" href="<?php echo LO_PUB_CSS;?>active.css"/>
    <?php } elseif(THISPLUG == 'lotrade') { ?>
        <link rel="stylesheet" href="<?php echo LO_PUB_CSS;?>esjy.css">
        <link rel="stylesheet" href="<?php echo LO_PUB_CSS;?>product-detail.css" type="text/css">
        <style>
            body #tj>li {padding:15px 0;}
            body #tj>li>ul{overflow:hidden;}
            #tj>li .f_diqu ul{width:450px;float:left;}
            #tj>li .fy_diqu{word-break: keep-all;height:25px;overflow: hidden;}
            #tj>li .f_diqu ul li{display: inline-block;padding:0px;height:22px;line-height:22px;margin: 0 5px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}
            #tj>li .f_diqu ul li a{padding:5px;white-space: nowrap;}
            #tj>li .f_diqu .f_more{float:right;width:40px;cursor:pointer;color:#2a65b1;}
        </style>
    <?php } elseif(CURSCRIPT == 'forum') { ?>
        <link rel="stylesheet" type="text/css" href="<?php echo LO_PUB_CSS;?>forum.css"/>
        <script type="text/javascript">
            var jq = jQuery.noConflict();// noConflict() 方法会释放会 $ 标识符的控制，这样其他脚本就可以使用它了。
        </script>
    <?php } elseif(CURSCRIPT == 'home') { ?>
        <link rel="stylesheet" href="<?php echo LO_PUB_CSS;?>talents.css"/>
    <?php } else { ?>
        
    <?php } ?>

</head>

<body <?php if($_G['basescript']=='forum') { ?>style="overflow:hidden;"<?php } ?>>
    