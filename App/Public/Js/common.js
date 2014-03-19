/**
 * 区字符串的字节数
 *
 * @para string str 字符串
 * @return int 返回字符串字节数
 */
function getByteLen(str) {
    var l = str.length;
    var n = l;
    for (var i = 0; i < l; i++)
        if (str.charCodeAt(i) < 0 || str.charCodeAt(i) > 255)
        n++;
    return n
}

/**
 * 获取附件格式
 *
 * @para string name 附件名称
 */
function getFileType(name) {
	var file_type;
    var name_pieces = new Array();
	name_pieces = name.split('.');
	
	switch (name_pieces[name_pieces.length - 1]) {
		case 'rar':
		case 'zip':
			file_type = 'rar';
			break;
		case 'pdf':
			file_type = 'pdf';
			break;
		case 'doc':
		case 'docx':
			file_type = 'doc';
			break;
		case 'ppt':
		case 'pptx':
			file_type = 'ppt';
			break;
		case 'xls':
		case 'xlsx':
			file_type = 'xls';
			break;
		case 'jpeg':
		case 'jpg':
        case 'gif':
		case 'png':
		case 'bmp':
			file_type = 'jpg';
			break;
		case 'mp3':
		case 'mp4':
		case 'avi':
		case 'rmvb':
		case 'wmv':
		case '3pg':
			file_type = 'mp4';
			break;
		case 'txt':
			file_type = 'txt';
			break;	
	}
	
	return file_type;
}

/**
 *  刷新后保持滚动条位置
 */ 
function Trim(strValue) { 
	return strValue.replace(/^\s*|\s*/g,""); 
} 
function SetCookie(sName, sValue) { 
	document.cookie = sName + "=" + escape(sValue); 
} 

function GetCookie(sName) { 
	var aCookie = document.cookie.split(";"); 
	for (var i=0; i < aCookie.length; i++) { 
		var aCrumb = aCookie[i].split("="); 
		if (sName == Trim(aCrumb[0])) { 
			return unescape(aCrumb[1]); 
		}	 
	} 

	return null; 
} 
function scrollback() { 
	if (GetCookie("scroll")!=null){document.body.scrollTop=GetCookie("scroll")} 
} 
