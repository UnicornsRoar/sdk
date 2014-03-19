// JavaScript Document
$(document).ready(function(){
	
	function focusUserName() {
		$('#name_tip').html('用户名3-15个字节');
	}

    function focusQuestion(){
		$('#question_tip').html('内容不能多于500字节');
	}
    function focusCaptcha(){
        $('#captcha_tip').html('点击图片更换');
    }

	 function isValidUserName(user_name) {
		var bool=5;
        $.ajax({
			async: false,
			type: 'post',
			url: "sdk_f/index.php/welcome/isValidUserName",
			data:'reg_name='+user_name,
			//data: {'reg_name':user_name, 'submitted': 'submitted'},
			success: function(data) {
                if(data==1){
                    $('#name_tip').html("用户昵称不能为空吖，主银~");
                    bool=1;
                }else if(data==2){
                    $('#name_tip').html("用户昵称太短啦~");
                    bool=2;
                }else{
                    $('#name_tip').html('通过~');
                    bool=3;
                }
            }
		});
        return bool;
    }
    function isValidQuestion(question) {
        var bool=5;
        $.ajax({
			async: false,
			type: 'post',
			url: "http://localhost:90/StudentGuide/index.php/welcome/isValidQuestion",
			data:'reg_question='+question,
			//data: {'reg_name':user_name, 'submitted': 'submitted'},
			success: function(data) {
                if(data==1){
                    $('#question_tip').html("内容不能为空吖，主银~");
                    bool=1;
                }else if(data==2){
                    $('#question_tip').html("内容超过500字节啦~");
                    bool=2;
                }else{
                    $('#question_tip').html("通过~");
                    bool=3;
                }
            }

		});

        return bool;
    }
    function isValidCaptcha(captcha) {
		var bool=5;
        $.ajax({
			async: false,
			type: 'post',
			url: "http://localhost:90/StudentGuide/index.php/welcome/isValidCaptcha",
			data:'captcha='+captcha,
			//data: {'reg_name':user_name, 'submitted': 'submitted'},
			success: function(data) {
                if(data==1){
                    $('#captcha_tip').html("验证码不能为空吖，主银~");
                    bool=1;
                }else if(data==2){
                    $('#captcha_tip').html("验证码错误啦~");
                    bool=2;
                }else{
                    $('#captcha_tip').html('通过~');
                    bool=3;
                }
            }
		});
        return bool;
    }



	 function checkUserName() {
        var user_name = $('#reg_name').val();
        // 检查用户名合法性
        return isValidUserName(user_name);

    }
    function checkQuestion() {
        var question = $('#reg_question').val();
        // 检查用户名合法性
        return isValidQuestion(question);
    }
    function checkCaptcha() {
        var captcha = $('#reg_captcha').val();
        // 检查用户名合法性
        return isValidCaptcha(captcha);
    }




    $('#reg_name').focus(function(){
		focusUserName();
	});
    $('#reg_question').focus(function(){
		focusQuestion();
	});
	$('#reg_captcha').focus(function(){
       focusCaptcha();
    });
	$('#reg_name').blur(function() {
        checkUserName();
    });
    $('#reg_question').blur(function() {
        checkQuestion();
    });
    $('#reg_captcha').blur(function() {
        checkCaptcha();
    });

    $("#img_checkcode").click(function(){//点击图片时
        get_captcha();
    });
    function get_captcha() {
        //$.get("http://localhost:90/StudentGuide/index.php/welcome/captcha", function(data){
          //  $('#img_checkcode').attr('src', data);
        //})
        $.ajax({
			async: false,
			type: 'post',
			url: "http://localhost:90/StudentGuide/index.php/welcome/captcha",
			//data: {'reg_name':user_name, 'submitted': 'submitted'},
			success: function(data) {
					$('#captcha_tip').html(data);
				}
		});
    }

    $('#register_button').click(function(){

        var bool;
        var c_user_name = checkUserName() ;
        var c_question = checkQuestion();
        var c_captcha = checkCaptcha();

        if ((c_user_name==3) && (c_captcha==3)&&(c_question==3)) {
            bool=true;
        } else {
            bool=false;

        }
		if(bool){
			$('#register1').submit();
		}else{
			return false;
		}
	});

	function checkFields() {
        var c_user_name = checkUsername();
        var c_question = checkQuestion();
        var c_captcha = checkCaptcha();

        if ((c_user_name==3) && (c_captcha==3)&&(c_question==3)) {
            return true;
        } else {
            return false;

        }
    }


});
