# hielec
嗨电子

在Linux下安装
'./'、'/' 换成 DISCUZ_ROOT .'
赋予读写权限
chmod 777 hielec -R



#调试
debug();


数据库变更
20170925
alter table pre_trade add brand_name varchar(150) not null DEFAULT '' COMMENT '自定义品牌名称' after brand_id;
