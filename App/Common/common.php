<?php


/**
 * 获取head之间的css引用
 *
 * @para css文件路径数组
 */
function get_css($array = array()) {
    if (!is_null($array)) {
        $css_href = '';
        foreach ($array as $css) {
            $css_href .= '<link rel="stylesheet" type="text/css" href="' . $css . '" />' . "\n";
        }
        echo $css_href;
    }
}

/**
 * 获取head之间的js引用
 *
 * @para js文件路径数组
 */
function get_js($array = array()) {
    if (!is_null($array)) {
        $js_src = '';
        foreach ($array as $js) {
            $js_src .= '<script src="' . $js . '"></script>' . "\n";
        }
        echo $js_src;
    }
}
/*
 * 生成小时分钟选择下拉菜单
 */
function createTimeSelection($name1,$name2,$time=0){
	$hour=date('G',$time);
	$min=date('i',$time);
	$min=$min-$min%5;
	$select='<select name="'.$name1.'" id="'.$name2.'" class="fl acti-clock">';
	$select.='<option value="">小时</option>';
	for($i=1;$i<=24;$i++){
		$select.='<option value="'.$i.'"';
		if($i==$hour){
			$select.=' selected="selected"';
		}
		$select.='>'.$i.'</option>';
	}
	$select.='</select>';
	$select.='<select name="'.$name2.'" id="'.$name2.'" class="fl acti-clock">';
	$select.='<option value="">分钟</option>';
	for($i=0;$i<=11;$i++){
		if((($i*5)==0)||(($i*5)==5)){
			$a='0'.($i*5);
			$select.='<option value="'.($i).'"';
		}else{
			$a=$i;
			$select.='<option value="'.($a*5).'"';
		}
		
		if(($i*5)==$min){
			$select.=' selected="selected"';
		}
		if(($i*5)==0||($i*5)==5){
			$a='0'.($i*5);
			$select.='>'.($a).'</option>';
		}else{
			$a=$i;
			$select.='>'.($a*5).'</option>';
		}
	}
	$select.='</select>';
	return $select;
}


?>