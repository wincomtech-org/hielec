<?php
//个人中心左边导航
// $def_target = '_blank';
$target = '';$style = '';$defined = '';
$attr = ($target?' target="'.$target.'"':'') . ($style?' style="'.$style.'"':'') . $defined;
$arrUCMenu = array(
        array(
            'href'=>'#', 'name'=>'论坛', 'ico'=>'1', 'attr'=>$attr,
            'sub' => array(
                    array('href'=>'home.php?mod=space&do=index','name'=>'个人空间','attr'=>$attr),
                    array('href'=>'home.php?mod=spacecp&ac=avatar','name'=>'我的自拍','attr'=>$attr),
                    array('href'=>'forum.php?mod=guide&view=my','name'=>'我的帖子','attr'=>$attr),
                    array('href'=>'home.php?mod=space&do=favorite&view=me','name'=>'我的收藏','attr'=>$attr),
                    array('href'=>'home.php?mod=space&do=friend','name'=>'我的好友','attr'=>$attr),
                ),
            ),
        array(
            'href'=>'#', 'name'=>'外包', 'ico'=>'2', 'attr'=>$attr,
            'sub' => array(
                    array('href'=>'home.php?mod=project&do=publish','name'=>'我发布的项目','attr'=>$attr),
                    array('href'=>'home.php?mod=project&do=bid','name'=>'我竞标的项目','attr'=>$attr),
                    array('href'=>'home.php?mod=project&do=comment','name'=>'待评价项目','attr'=>$attr),
                    array('href'=>'home.php?mod=project&do=case','name'=>'我的案例','attr'=>$attr),
                    array('href'=>'home.php?mod=project&do=collect','name'=>'外包收藏','attr'=>$attr),
                ),
            ),
        array(
            'href'=>'#', 'name'=>'二手交易', 'ico'=>'3', 'attr'=>$attr,
            'sub' => array(
                    array('href'=>'home.php?mod=trade&do=list','name'=>'出售闲置品','attr'=>$attr),
                    array('href'=>'home.php?mod=trade&do=log','name'=>'交易记录','attr'=>$attr),
                    array('href'=>'home.php?mod=trade&do=cols','name'=>'收藏的商品','attr'=>$attr),
                ),
            ),
        array(
            'href'=>'#', 'name'=>'我的下载', 'ico'=>'4', 'attr'=>$attr,
            'sub' => array(
                    array('href'=>'home.php?mod=down&do=up','name'=>'资料上传','attr'=>$attr),
                    array('href'=>'home.php?mod=down&do=list','name'=>'资料下载','attr'=>$attr),
                    array('href'=>'home.php?mod=down&do=cols','name'=>'我的收藏','attr'=>$attr),
                    array('href'=>'home.php?mod=down&do=log','name'=>'下载记录','attr'=>$attr),
                ),
            ),
        array(
            'href'=>'#', 'name'=>'Datasheet', 'ico'=>'5', 'attr'=>$attr,
            'sub' => array(
                    array('href'=>'home.php?mod=datasheet&do=list','name'=>'我的下载','attr'=>$attr),
                    array('href'=>'home.php?mod=datasheet&do=cols','name'=>'我的收藏','attr'=>$attr),
                    array('href'=>'home.php?mod=datasheet&do=log','name'=>'下载记录','attr'=>$attr),
                ),
            ),
        array(
            'href'=>'#', 'name'=>'我的活动', 'ico'=>'6', 'attr'=>$attr,
            'sub' => array(
                    array('href'=>'home.php?mod=activity&do=list','name'=>'参与的活动','attr'=>$attr),
                    array('href'=>'home.php?mod=activity&do=cols','name'=>'收藏列表','attr'=>$attr),
                    array('href'=>'home.php?mod=activity&do=exchange','name'=>'兑换记录','attr'=>$attr),
                ),
            ),
    );
?>