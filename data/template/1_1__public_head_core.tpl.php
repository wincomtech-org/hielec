<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="<?php echo CHARSET;?>">
    <?php if($_G['config']['output']['iecompatible']) { ?><meta http-equiv="X-UA-Compatible" content="IE=EmulateIE<?php echo $_G['config']['output']['iecompatible'];?>" /><?php } ?>
    <title><?php if(!empty($SEO['title'])) { ?><?php echo $SEO['title'];?> - <?php } elseif(!empty($navtitle)) { ?><?php echo $navtitle;?> - <?php } if(empty($nobbname)) { ?> <?php echo $_G['setting']['bbname'];?> - <?php } ?><?php echo $_G['setting']['seohead'];?></title>

    <meta name="keywords" content="<?php if(!empty($metakeywords)) { echo dhtmlspecialchars($metakeywords); } elseif(!empty($SEO['keywords'])) { ?><?php echo $SEO['keywords'];?><?php } ?>" />
    <meta name="description" content="<?php if(!empty($metadescription)) { echo dhtmlspecialchars($metadescription); ?> <?php } elseif(!empty($SEO['description'])) { ?><?php echo $SEO['description'];?> <?php } if(empty($nobbname)) { ?>,<?php echo $_G['setting']['bbname'];?><?php } ?>" />
    <meta name="generator" content="Lothar! <?php echo $_G['setting']['version'];?>" />
    <meta name="author" content="Lotahr! Team and Wincomtech UI Team" />
    <meta name="copyright" content="2016-2030 Wincomtech Inc." />
    <meta name="MSSmartTagsPreventParsing" content="True" />
    <meta http-equiv="MSThemeCompatible" content="Yes" />
    <base href="<?php echo $_G['siteurl'];?>" />




