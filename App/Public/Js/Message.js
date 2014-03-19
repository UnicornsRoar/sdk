// 隐藏/显示回复框
$(function(){
	$(".reply").hide();
	$(".reply-a2").click(function() {
		var reply=$(this).nextAll(".reply");
		if (reply.is(':visible')){
			reply.slideUp();
		}else{
			reply.slideDown();
		}
	});

});