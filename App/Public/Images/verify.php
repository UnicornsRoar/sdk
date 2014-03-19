<?php
class Verify
{
    private $width;
	private $height;
	private $codenum;
	private $image;
	private $spotnum;
	private $code;
	
    function __construct($width = 80,$height = 20, $codenum = 4)
	{
	    $this -> width   = $width;
		$this -> height  = $height;
		$this -> codenum = $codenum;
		$this -> code    = $this->create_code();
		$spotnum = floor($width*$height/10);
		if ($spotnum > 240-$codenum)
		{
		    $this -> spotnum = 240-$codenum;
		}else $this -> spotnum = $spotnum;
	}
	
	function show_image()
	{
	    //创建图像背景
		$this -> creatimage();
		//设置干扰元素
		$this -> set_distrub_color();
		//向图像中随机画出文本
		$this -> output_text();
		//输出图像
		$this -> output_image();		
	}
	
	private function create_code()
	{
	    $code   = "abcdefghijkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789";
		$string = '';
		for ($i=1; $i<= $this->codenum; $i++)
		    $string .= $code[rand(0,strlen($code)-1)];
		return $string;
	}		
	
	function get_code()
	{
	    return $this->code;
	}
	

	private function creatimage()
	{
	    //创建图像资源
	    $this -> image = imagecreatetruecolor($this -> width,$this -> height);
		//随机产生背景颜色
		$backcolor = imagecolorallocate($this->image,rand(200,255),rand(200,255),rand(200,255));
		//填充背景色
		imagefill($this->image,0,0,$backcolor);
		//画出矩形边框
		$border = imagecolorallocate($this->image,0,0,0);
		imagerectangle($this->image,0,0,$this->width-1,$this->height-1,$border);
	}

	private function set_distrub_color()
	{
	    //随即产生不同颜色的点
	    for ($i=0; $i<=$this->spotnum; $i++)
		{
		    $color = imagecolorallocate($this->image,rand(0,255),rand(0,255),rand(0,255));
	        imagesetpixel($this->image,rand(1,$this->width-2),rand(1,$this->height-2),$color);			
		}
		//随即产生弧线
		for ($i=0; $i<=5; $i++)
		{
		    $color = imagecolorallocate($this->image,rand(0,255),rand(0,255),rand(0,255));
			imagearc($this->image,rand(-10,$this->width),rand(-10,$this->height),rand(30,300),rand(20,200),55,44,$color);
		}
	}
	
	private function output_text()
	{
	    $block = floor($this->width/$this->codenum);
	    for ($i=1; $i<=$this->codenum; $i++)
		{
		    $color    = imagecolorallocate($this->image,rand(0,120),rand(0,120),rand(0,120));
			$fontsize = rand(6,8);
			$index    = $block*$i-$block/2;
			$x = rand($index-3,$index-3);
			$y = rand(0,$this->height-20);
            imagechar($this->image,$fontsize,$x,$y,$this->code[$i-1],$color);			
		}
	}
	
	private function output_image()
	{
	    //设置显示格式
	    if (imagetypes() & IMG_GIF)
		{
	        header("Content-Type:image/png");
		    imagepng($this -> image);
		}elseif (imagetypes() & IMG_JPG)
		{
		    header("Content-Type:image/jpeg");
		    imagepng($this -> image);
		}elseif (imagetypes() & IMG_PNG)
		{
		    header("Content-Type:image/png");
		    imagepng($this -> image);
		}elseif (imagetypes() & IMG_WBMP)
		{
		    header("Content-Type:image/vnd.wap.wbmp");
		    imagepng($this -> image);
		}else
		{
		    die("PHP不支持图像");
		}
	}
	
	function __destruct()
	{
	    //销毁图像
	    imagedestroy($this->image);
	}
	
}
session_start();
$Verify = new Verify();
$_SESSION['verify'] = md5(strtolower($Verify->get_code()));
$Verify->show_image();
