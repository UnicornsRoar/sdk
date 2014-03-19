    var index = 1;
    var timer = null;
    var offset = 8000;
    var left1 = -410;
$(function(){
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

    $(".cancel").click(function(){
        $(".dialog-bg").hide();
        $(".login").hide();
    });
    $(".user").mouseenter(function(){
        $(".hoverli").show();
    });
    $(".user").mouseleave(function(){
        $(".hoverli").hide();
    });
}); 
$(window).load(function(){
    var imagesdiv = $(".imagesdiv");
    var imgs = $(".imagesdiv .image");
    imgs.css("float","left");
    var btn = $(".banner .btn")
    var imgWidth = 600;
    var allWidth = $(document).width();
    while(allWidth<960){
        allWidth=960;
        $(".container").width(960);
        $(".row-fluid").width(960);
    }
    while(allWidth>1020){
        allWidth=1020;
        $(".container").width(1020);
        $(".row-fluid").width(1020)
    }
    imagesdiv.width(600*imgs.length);
    imagesdiv.css("position","absolute");
    var left1 = -410;
    //var left1 = 3/2*imgWidth-1/2*bannerWidth;
    imagesdiv.css("left",left1+"px");
    //auto(true);
    $(btn[0]).bind("click",function(){
        if (timer){
            clearTimeout(timer);
        }
        index--;
        if(index<1){
            index=imgs.length-2;
        }
        slideimg(index,left1);
        timer = window.setTimeout('auto(' + false + ')', offset);
    });
    $(btn[1]).bind("click",function(){
        if (timer){
            clearTimeout(timer);
        }
        index++;
        if(index>imgs.length-2){
            index=1;
        }
        slideimg(index,left1);
        timer = window.setTimeout('auto(' + false + ')', offset);
    });
});
    // function auto(isFirst){
    // var btn = $(".banner .btn")
    // var imgs = $(".imagesdiv .image");
    // imgs.css("float","left");
    // if(isFirst == false) index++;
    // if(index>imgs.length-2){
    //     index=1;
    // }
    //  $(btn[1]).trigger("click");
    // //slideimg(index,left1);
    // timer = window.setTimeout('auto(' + false + ')', offset);
    // }

//imagesdiv:要移动的放图片的div;
// index:全局变量，显示的图片的序号;
// imgWidth:图片宽度;
// imgs:图片数组;
// left1:imagesdiv的起始left;imagesdiv,index,imgWidth,imgs,left1
    function slideimg(index,left1){ 
        var imagesdiv = $(".imagesdiv");
        var imgs = $(".imagesdiv .image");
        var imgWidth = 600;
        var left2=left1-imgWidth*(index-1);
        var left3=left2+"px";
        imagesdiv.animate({left:left3},1000);
    }
/**
 *预加载banner图片
 */
function loadImg(arr,links){
    var objImg = new Image(); //预加载
    for(var i=0; i<arr.length; i++){
        objImg.src =arr[i]; //预加载
    }
    objImg.onload = function(){
        $('.load-pic').hide();
        downImg(arr.length-1);
        for (var i = 0; i < arr.length; i++) {
            downImg(i);
        }
        downImg(0);
    }
    function downImg(i){
        var $image=$("<div class='image fl'></div>");
        $image.append("<a href='"+links[i]+"'></a>");
        $image.append("<img src=''/>");
        $image.find('img').attr('src',arr[i]);
        $(".imagesdiv").append($image);
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

  $(
    function(){$( ".datepicker" ).datepicker();
    dateChinese();
});