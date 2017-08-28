(function($){
$.kindeditor_default_setting = {
	allowFileManager : false,
	allowFileUpload: false,
	//readonlyMode : true,
	items : [
		'bold',	'italic', 'underline','strikethrough','|','cut', 'copy', 'paste',
		'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
		'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
		'superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'source','preview', 'fullscreen', 
		'/',
		'formatblock', 'fontname', 'fontsize', 'forecolor', 'hilitecolor', '|' , 'undo', 'redo', '|',  'lineheight', 'removeformat', '|', 'image', 'multiimage',
		'flash', 'table', 'hr', 'emoticons', '|', 
		'anchor', 'link', 'unlink','|','baidumap'
	],
	urlType : 'domain',
	formatUploadUrl : false,
	extraFileUploadParams : {
	   "PHPSESSID" : "<{''|@session_id|encrypt}>"
	},
	uploadJson : LP.baseuri+'attachment/kindeditor?of=json',
	filePostName : 'Filedata'
};
$.ueditor_default_setting = {
	simple: {toolbars: [
		['fullscreen', 'source', '|', 'undo', 'redo', '|', 'drafts', 'pasteplain', 'selectall', 'cleardoc', '|', 'print', 'preview', 'searchreplace', '|', 'removeformat', 'formatmatch', 'autotypeset', '|', 'directionalityltr', 'directionalityrtl', '|',  'help'],
		['bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'forecolor', 'backcolor', '|', 'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', 'indent','|', 'rowspacingtop', 'rowspacingbottom', 'lineheight', '|', 'superscript', 'subscript', '|', 'touppercase', 'tolowercase'],
		['customstyle', 'paragraph', 'fontfamily', 'fontsize', '|', 'template', '|', 'insertorderedlist', 'insertunorderedlist', '|', 'link', 'unlink', 'anchor', '|', 'blockquote',/*'insertcode',*/ 'pagebreak', 'horizontal', '|', 'simpleupload', 'insertimage']
	]},complete: {toolbars: typeof window.UEDITOR_CONFIG != 'undefined' ? window.UEDITOR_CONFIG.toolbars : []
	}
};
window.UEDITOR_CONFIG.serverUrl = LP.baseuri + 'attachment/ueditor?of=json&_token=' + LP.csrf;
})(jQuery);