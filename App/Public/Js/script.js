$(function(){
    $(".datepicker").datepicker();
    dateChinese();
    $(".btn-cc").click(function(){
        $(".dialog-bg").hide();
        $("#login").hide();
        return false;
    }); 
    $(".j-close").hide();
    $(".endtime").click(function(){
        $(".endtime").hide(500);
        $(".j-close").show(500);
    });
    $(".close").click(function(){
        $(".j-close").hide(500);
        $(".endtime").show(500);
    });
    var loginlef=(screen.width-450)/2;
    $("#login").css("left",loginlef+"px");
    $(".btnlogin").click(function(){
        $(".dialog-bg").show();
        $("#login").show();
    });
    $("#vedio").click(function(){
        $(".dialog-bg").show();
        $("#vediodiv").show();
    });
    $(".j-remember").click(function(){
        if($(".chkbox").checked==true){
            $(".chkbox").checked=false;
        }
        else{
           $(".chkbox").checked=true; 
        }
    });
    $(".cancel").click(function(){
        $(".dialog-bg").hide();
        $(".login").hide();
        $('#video_id').val('');
        $('#video_name').val('');
        $('#video_url').val('');
        $('#video_url_error').text('');
        $('#video_name_error').text('');
    });
    $(".user2").mouseenter(function(){
        $(".hoverli").show();
    });
    $(".user2").mouseleave(function(){
        $(".hoverli").hide();
    });
});
$(window).load(function(){
    var controlWidth1 = new controlWidth(960,1020);
    var banner1 = new banner();
    banner1.alert2(2);
});
function controlWidth(min,max){
    var allWidth = $(document).width();
    while(allWidth<min){
        allWidth=min;
        $(".container").width(min);
        $(".row-fluid").width(min);
    }
    while(allWidth>max){
        allWidth=max;
        $(".container").width(max);
        $(".row-fluid").width(max)
    }
}
/*
    imagesdiv:要移动的放图片的div;
    index:全局变量，显示的图片的序号;
    imgWidth:图片宽度;
    imgs:图片数组;
    left1:imagesdiv的起始left;imagesdiv,index,imgWidth,imgs,left1
*/
function banner(){
    var index = 42;
    var timer = null;
    var offset = 8000;//时间间隔
    var left1 = -24995;
    var isScrolling = false;
    //var left1 = 1/2*bannerWidth-85/2*imgWidth; 
    var imgWidth = 600;
    var detailLength = arr.length;
    var left1 = -24995;
    $(".imagesdiv .image").css("float","left");
    $(".imagesdiv").css("position","absolute");  
    $(".imagesdiv").css("left",left1+"px");
	autoPage(true);
    function clickLeft(){
        if (timer){
            clearTimeout(timer);
        }
        index--;  
        slideimg(index,left1);   
        if(index<2){
            index = $(".imagesdiv .image").length-3;
            left4 = left1-imgWidth*(3+detailLength)+"px";
            $(".imagesdiv").animate({left:left4},1); 
            var detailIndex = index-2;
            if(index>=detailLength+2){
                detailIndex = index%detailLength-2;
                if(detailIndex<0){
                    detailIndex+=detailLength;
                }
            }
            $($(".i-detail .information")[0]).text(places[detailIndex]);
            $($(".i-detail .information")[1]).text(times[detailIndex]);
            $($(".i-detail .information")[2]).text(hosts[detailIndex]);
            }   
            //timer = window.setTimeout('auto(' + false + ')', offset);
			timer = window.setTimeout(function(){autoPage(false)},offset)
    } 
	function autoPage(isFirst)
    {
         $(".imagesdiv .image").css("float","left");
         if(isFirst == false) index++; 
          slideimg(index,left1);
        if (index > $(".imagesdiv .image").length-3) {
            $(".imagesdiv").animate({left:left1},1);
            index = 2;
            $($(".i-detail .information")[0]).text(places[0]);
            $($(".i-detail .information")[1]).text(times[0]);
            $($(".i-detail .information")[2]).text(hosts[0]);
        }    
            //timer = window.setTimeout('auto(' + false + ')', offset);
			timer=setTimeout(function(){autoPage(false)},offset)
    }
	
    function slideimg (index,left1){          
        var left2=left1-imgWidth*(index-42);
        var left3=left2+"px";      
        $(".i-detail .information").show();
        $(".i-detail .icon").show();
        if(times[index-2]==1){
            $(".i-detail .information").hide();
            $(".i-detail .icon").hide();
        }
        if (isScrolling == false) {
            isScrolling = true;
            $(".banner .btn").attr("disabled","disabled");
            $(".imagesdiv").animate({opacity:0.75},20)
            .animate({left:left3},400)
            .animate({opacity:1.0},20,'linear',function(){
            isScrolling = false;
            $(".banner .btn").attr("disabled"," none");
            });
            var detailIndex = index-2;
            if(index>=detailLength+2){
                detailIndex = index%detailLength-2;
                if(detailIndex<0){
                    detailIndex+=4;
                }
            }
            $($(".i-detail .information")[0]).text(places[detailIndex]);
            $($(".i-detail .information")[1]).text(times[detailIndex]);
            $($(".i-detail .information")[2]).text(hosts[detailIndex]);
            if (index > $(".imagesdiv .image").length-3) {
                var clone = $(".imagesdiv .image:gt(1)").clone(true);
                var width2 = $(".imagesdiv").width();
                $(".imagesdiv").width(width2*2);
                $(".imagesdiv").append(clone);
            }
        }
        if (isScrolling == true){
            return false;
        }
    }
    $($(".banner .btn")[0]).click(function(){
        if (isScrolling == false) {
            clickLeft();
        }
    });
    $($(".banner .btn")[1]).bind("click",function(){
        if (isScrolling == false) {
            if (timer){
                clearTimeout(timer);
            }
            index++;
            slideimg(index,left1);
            //timer = window.setTimeout('auto(' + false + ')', offset);
			timer=window.setTimeout(function(){autoPage(false)},offset)
        }
    });
}   
// banner.prototype = {
//     alert2 : function(num){      
//         function alert3(num){
//         alert(num+'alertagain');
//     } 
//     alert3(num); 
//     },


// } 
/**
 *预加载banner图片
 */
function loadImg(arr,links){
    var imgslength = 1;
    var objImg = new Image(); //预加载
    for(var i=0; i<arr.length; i++){
        objImg.src =arr[i]; //预加载
    }
    objImg.onload = function(){
        $('.load-pic').hide(); 
        downImg(arr.length-2);  
        downImg(arr.length-1);
        for (var i = 0; i < arr.length; i++) {
            downImg(i);
        }
        for (var i = 0; i < arr.length; i++) {
            downImg(i);
        }
        for (var i = 0; i < arr.length; i++) {
            downImg(i);
        }
        for (var i = 0; i < arr.length; i++) {
            downImg(i);
        }
        for (var i = 0; i < arr.length; i++) {
            downImg(i);
        }
        for (var i = 0; i < arr.length; i++) {
            downImg(i);
        }
        for (var i = 0; i < arr.length; i++) {
            downImg(i);
        }
        for (var i = 0; i < arr.length; i++) {
            downImg(i);
        }
        for (var i = 0; i < arr.length; i++) {
            downImg(i);
        }
        for (var i = 0; i < arr.length; i++) {
            downImg(i);
        }
        for (var i = 0; i < arr.length; i++) {
            downImg(i);
        }
        for (var i = 0; i < arr.length; i++) {
            downImg(i);
        }
        downImg(0);
        downImg(1);
    }
    function downImg(i){
    imgslength++;      
    $(".imagesdiv").width(600*imgslength);
    // alert(imagesdiv.width());
    var $image=$("<div class='image fl'></div>");
    $image.append("<a href='"+links[i]+"'></a>");
    $image.append("<img src='"+arr[i]+"'/>");
    // $image.find('img').attr('src',arr[i]);
    $(".imagesdiv").append($image);
    $($(".i-detail .information")[0]).text(places[0]);
    $($(".i-detail .information")[1]).text(times[0]);
    $($(".i-detail .information")[2]).text(hosts[0]);
}
}

// function text(){
//     $(':text').focus(function(){
//     if(!this.initValue){
//         this.initValue = this.value;
//     }
//     if(this.value === this.initValue){
//         this.value = '';
//     }
//     }).blur(function(){
//     if(this.value === '' || this.value === null){
//         this.value = this.initValue;
//     }
//     });
// }
 function dateChinese(){
     $.datepicker.regional['zh-CN'] = {
         closeText: '关闭',
         prevText: '&#x3c;上月',
         nextText: '下月&#x3e;',
         currentText: '今天',
         monthNames: ['一月','二月','三月','四月','五月','六月',
         '七月','八月','九月','十月','十一月','十二月'],
         monthNamesShort: ['一','二','三','四','五','六',
         '七','八','九','十','十一','十二'],
         dayNames: ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],
         dayNamesShort: ['周日','周一','周二','周三','周四','周五','周六'],
         dayNamesMin: ['日','一','二','三','四','五','六'],
         weekHeader: '周',
         dateFormat: 'yy-mm-dd',
         firstDay: 1,
         isRTL: false,
         showMonthAfterYear: true,
         yearSuffix: '年'};
     $.datepicker.setDefaults($.datepicker.regional['zh-CN']);
}

