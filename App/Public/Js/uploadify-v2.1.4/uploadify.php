<?php
/*
Uploadify v2.1.4
Release Date: November 8, 2010

Copyright (c) 2010 Ronnie Garcia, Travis Nickels

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/
if (!empty($_FILES)) {
	// 文件根目录
	$document_root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$uploadPath =  $document_root . $_REQUEST['folder'] . '/';
	// $targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];

    // 按年月组织文件存放的目录
    $dir = date('Ym', time());
    // 图片物理地址
    $targetPath = $uploadPath . $dir . '/';
    // 如果文件夹不存在，创建新的文件夹
    if (!file_exists($targetPath)) {
        mkdir($targetPath, 0777);
    }
    
    $namePieces = explode('.', $_FILES['Filedata']['name']);
    $ext = $namePieces[1];
    // 生成唯一文件名
    $randNum = md5(uniqid(mt_rand(), true));
    $fileName = $randNum . '.' . $ext;
    
    $thumbName = $randNum . '_thumb.' . $ext;
    
	$targetFile =  str_replace('//','/',$targetPath) . $fileName;
	
	$fileTypes  = str_replace('*.','',$_REQUEST['fileext']);
	$fileTypes  = str_replace(';','|',$fileTypes);
	$typesArray = split('\|',$fileTypes);
	$fileParts  = pathinfo($_FILES['Filedata']['name']);
	
	if (in_array($fileParts['extension'],$typesArray)) {
		// Uncomment the following line if you want to make the directory if it doesn't exist
		// mkdir(str_replace('//','/',$targetPath), 0755, true);
		
		move_uploaded_file($tempFile,$targetFile);
        
        echo str_replace($document_root,'',$targetFile);
        
        // 创建缩略图
        include('../../../../Core/Lib/ORG/Util/CreatMiniature.class.php');
        $CreatMiniature = new CreatMiniature();
        $CreatMiniature->SetVar($targetPath . $fileName, 'file');
        $CreatMiniature->Cut($targetPath . $thumbName, 280, 224);
	} else {
        echo 'Invalid file type.';
	}
}
?>