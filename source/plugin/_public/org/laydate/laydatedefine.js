
document.write('<script src="images/js/laydate.js"><\/script>');

function skip(){
	var num= $('#txt_pagesum').val();
	window.location.href="http://www.baidu.com?page="+num;
}

!function(){
	laydate.skin('molv');//切换皮肤，请查看skins下面皮肤库
	laydate({elem: '#demo'});//绑定元素
}();

//日期范围限制
var start = {
	elem: '#start',
	format: 'YYYY-MM-DD',
	min:  '1970-00-00', //设定最小日期为当前日期
	max:laydate.now(), //最大日期
	istime: true,
	istoday: false,
	choose: function(datas){
	end.min = datas; //开始日选好后，重置结束日的最小日期
	end.start = datas //将结束日的初始值设定为开始日
	}
};
laydate(start);

var end = {
	elem: '#end',
	format: 'YYYY-MM-DD',
	min: '1970-00-00',
	max: laydate.now(),
	istime: true,
	istoday: false,
	choose: function(datas){
	start.max = datas; //结束日选好后，充值开始日的最大日期
	}
};
laydate(end);