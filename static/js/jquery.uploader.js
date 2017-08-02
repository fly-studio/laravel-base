window.UPLOADER_LANGUAGE = {
	'close' : '关闭',
	'select_file' : '选择文件',
	'ctrl_v_tip' : '可以使用Ctrl+V直接粘贴截图（需现代浏览器）',
	'ctrl_v_button' : 'Ctrl+V 粘贴',
	'resize' : '图片会自动等比缩放至：{{0}} (仅jpg)',
	'uploading' : '正在上传文件...',
	'success' : '上传成功!',
	'rotate_right' : '向右旋转',
	'rotate_left' : '向左旋转',
	'loading' : '正在载入...',
	'reading' : '文件读取中...',
	'filetype' : '请上传{{0}}文件!',
	'filenum_limite' : '只允许上传{{0}}个文件，请删减后重试!',
	'hashing' : '正在效验文件...',
	'hash_success' : '云端文件已存在，文件秒传成功!',
	'error' : '错误',
	'num_limit' : '只能上传{{0}}个文件!',
	'allsize_limit' : '文件总大小超出限制!',
	'filesize_limite'  : '文件大小超出 {{0}}!',
	'duplicate' : '上传队列中有重复文件!'
};
(function($){
	if(typeof LP == 'undefined')
		throw('this javascript file need behind \'laravel.lp.js\'');

	$.fn.extend({
		uploader : function(max_width, max_height, filesize, filetype, filelimit, id) {
			var options = {
				max_width: 0,
				max_height: 0,
				filesize: 2 * 1024 * 1024,
				filetype: 'jpg,jpeg,png,bmp,gif,webp,svg',
				filelimit: 1,
				id: null,
			};
			if (typeof max_width == 'object' && max_width !== null) // it's options
				options = $.extend({}, options, max_width);
			else if (max_width !== true)
			{
				if (typeof max_width != 'undefined') options.max_width = parseFloat(max_width);
				if (typeof max_height != 'undefined') options.max_height = parseFloat(max_height);
				if (typeof filesize != 'undefined') options.filesize = parseFloat(filesize);
				if (typeof filetype != 'undefined') options.filetype = filetype.toLowerCase();
				if (typeof filelimit != 'undefined') options.filelimit = parseInt(filelimit);
				if (typeof id != 'undefined') options.id = id;
			}
			var img_types = ['jpg','jpeg','png','bmp','gif','webp','svg'];
			return this.each(function(){
				var t = $(this);

				var flex_uploader = t.prop('flex_uploader') ? t.prop('flex_uploader') : {};
				if (flex_uploader && flex_uploader.uploader)
				{ //删除原有的控件
					delete flex_uploader.uploader;
					flex_uploader.$container.remove();
					t.removeProp('flex_uploader');
				}
				//删除该控件，下面的不用执行
				if (max_width === true) return;

				var method = {interval: null};
				var nonce = function(){
					return rand(10000,99999);
				};
				var id = nonce();
				var uploader_id = 'uploader-id-' + id, pick_id = 'pick-id-' + id;
				var progresses_id = uploader_id + '-progresses', thumbnails_id = uploader_id + '-thumbnails', input_id = uploader_id + '-input';
				//添加容器到input下
				flex_uploader.$container = $('<div class="uploader-container" id="'+ uploader_id +'">'+
					'<div class="pull-left"><div id="'+ pick_id +'">'+ UPLOADER_LANGUAGE.select_file +'(≤ '+ bytesToSize(options.filesize) +')</div></div>' +
					'<div class="pull-left tags">&nbsp;<span class="label label-success">.' + options.filetype.replace(/,/g,'</span>&nbsp;<span class="label label-success">.') + '</span>&nbsp;' +
					(options.max_width > 0 && options.max_height > 0 ? '<br /><small>&nbsp;'+ UPLOADER_LANGUAGE.resize.replace('{{0}}', options.max_width.toString().toHTML() + 'x' + options.max_height.toString().toHTML()) + '</small>' : '') +
					'</div><div class="clearfix"></div>' +
					'<div id="' + progresses_id + '" class="progresses"></div><div class="clearfix"></div>' +
					'<div id="' + thumbnails_id + '" class="thumbnails row"></div><div class="clearfix"></div>' +
					'</div>').insertAfter(t);
				
				if (typeof $.fn.tooltip != 'undefined') $('.enable-tooltip', flex_uploader.$container).tooltip();

				var $progresses = {};
				var progress = function(file){
					if (!$progresses[file.id])
						$progresses[file.id] = $('<div class="media alert alert-warning fade in"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
							'<div class="media-left media-middle"><img class="media-object" src="" alt=""></div>' +
							'<div class="media-body"><h4 class="media-heading">'+file.name.toHTML()+'('+bytesToSize(file.size)+')</h4>' +
							'<div class="media-message"></div>' +
							'<div class="progress">' +
								'<div class="progress-bar progress-bar-warning progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">' +
									'<span class=""></span>' +
								'</div>' +
							'</div>' +
							'</div>' +
						'</div>').appendTo('#'+progresses_id).on('closed.bs.alert', function(){
							if (file.getStatus() == 'complete')
								progress(file).remove();
							else
								progress(file).cancel();
						});
						//console.log(file);
					return {
						init: function(){
							$progresses[file.id].removeClass('alert-info alert-danger alert-success alert-warning');
							return this;
						},
						initProgress: function(){
							 $('.progress-bar', $progresses[file.id]).removeClass('progress-bar-info progress-bar-danger progress-bar-success progress-bar-warning');
						},
						thumb: function(){
							flex_uploader.uploader.makeThumb( file, function( error, ret ) {
								$('.media-object', $progresses[file.id]).attr('src', error ? LP.baseuri+'placeholder?text='+file.ext+'&size=75x75&fontsize=35' : ret);
							});
						},
						name: function(name){
							$('.progressName', $progresses[file.id]).html(name.toHTML());
							return this;
						},
						message: function(message){
							$('.media-message', $progresses[file.id]).html(message);
							return this;
						},
						error: function(message) {
							this.init();
							this.initProgress();
							file.setStatus('invalid');
							$progresses[file.id].addClass("alert-danger");
							$('.progress-bar', $progresses[file.id]).addClass("progress-bar-danger");
							return this.message(message);
						},
						progressing: function (percentage, is_hashing) {
							this.init();
							this.initProgress();
							$progresses[file.id].addClass("alert-info");
							percentage = percentage.toFixed(2);
							var $bar = $('.progress-bar', $progresses[file.id]).width(percentage + "%");
							$('.progress-bar span', $progresses[file.id]).text(percentage + "%");
							if (percentage < 20)
								$bar.addClass('progress-bar-warning');
							else if (percentage < 95)
								$bar.addClass('progress-bar-info');
							else
								$bar.addClass('progress-bar-success');
							if (percentage < 100) $bar.addClass('active'); else $bar.removeClass('active');
							return this.message(UPLOADER_LANGUAGE.uploading);
						},
						success: function() {
							this.progressing(100);
							this.init();
							$progresses[file.id].addClass("alert-success");

							$progresses[file.id].delay(1500).queue(function(){
								$(this).alert('close').dequeue();
							});
							return this.message(UPLOADER_LANGUAGE.success);
						},
						cancel: function() {
							flex_uploader.uploader.cancelFile(file);
							return this.remove();
						},
						remove: function() {
							$progresses[file.id].remove();
							delete $progresses[file.id];
							return this;
						}
					};
				};
				var $thumbnails = {};
				var preview = function(id, filename, fileext, url)
				{
					if (typeof id != 'undefined' && typeof $thumbnails[id] == 'undefined')
					{
						$thumbnails[id] = $('<div class="col-xs-6 col-md-4 alert uploader-thumbnail" data-id="'+id+'"><div class="thumbnail">' +
							'<div class="file-panel"><span class="cancel" data-dismiss="alert" aria-label="Close">'+UPLOADER_LANGUAGE.close+'</span><span class="rotateRight">'+UPLOADER_LANGUAGE.rotate_right+'</span><span class="rotateLeft">'+UPLOADER_LANGUAGE.rotate_left+'</span></div>' +
							'<a href="'+url+'"  target="_blank"><img src="'+LP.baseuri+'placeholder?size=300x200&text='+encodeURIComponent(UPLOADER_LANGUAGE.loading)+'" alt="" class="img-responsive center-block"  onerror="this.src=\''+ LP.baseuri +'placeholder?size=300x200&text=\'+encodeURIComponent(\''+UPLOADER_LANGUAGE.reading+'\');"></a>' +
							'<div class="caption">' +
							'<h4><span class="title">'+(filename ? filename.toHTML() : '')+'</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></h4>' +
							'</div><div class="clearfix"></div>' +
							'</div><div class="clearfix"></div></div>').appendTo('#' + thumbnails_id).on('closed.bs.alert', function(){
								preview($(this).data('id')).remove();
							});
							$('.rotateLeft,.rotateRight', $thumbnails[id]).on('click', function(){
								var $obj = $(this).closest('.uploader-thumbnail');
								var rotation = parseInt($obj.data('rotation')) || 0;
								rotation += $(this).is('.rotateLeft') ? -90 : 90;
								var deg = 'rotate(' + rotation + 'deg)';
								$('img', $obj).css(
									$.supportTransition ? {'-webkit-transform': deg,'-mos-transform': deg,'-o-transform': deg,'transform': deg}
									: {filter: 'progid:DXImageTransform.Microsoft.BasicImage(rotation='+ (~~((rotation/90)%4 + 4)%4) +')'}
								);
								$obj.data('rotation', rotation);
							});
						if (!fileext || !url)
						{
							LP.get(LP.baseuri + 'api/attachment/'+ id).done(function(json){
								if (id != json.data.id)
									preview(id).replace(json.data.id);
								var $obj = $('.uploader-thumbnail[data-id="'+json.data.id+'"]');

								if (typeof json.data.ext != 'undefined')
								{
									var pic = img_types.indexOf(json.data.ext.toLowerCase()) > -1 ? json.data.url : LP.baseuri + 'placeholder?size=300x200&text='+encodeURIComponent(json.data.ext);
									$('.title', $obj).text(json.data.filename);
									$('img', $obj).attr('src', pic);
									$('a', $obj).attr('href', json.data.url);
								}
							});
						}
						else
						{
							var $obj = $('.uploader-thumbnail[data-id="'+id+'"]');
							var pic = img_types.indexOf(fileext.toLowerCase()) > -1 ? url : LP.baseuri + 'placeholder?size=300x200&text='+ encodeURIComponent(fileext);
							$('img', $obj).attr('src', pic);
						}
					}

					return {
						build: function(){
							if (t.triggerHandler('uploader.previewing', [this.getFile(), id, attachment().get()]) === false) return this;
							attachment().add(id);
							t.triggerHandler('uploader.previewed',[this.getFile(), id, attachment().get()]);
							return this;
						},
						remove: function(){
							var file = this.getFile();
							if (t.triggerHandler('uploader.removing', [file, id, attachment().get()]) === false) return this;
							if (file) flex_uploader.uploader.removeFile(file, true);
							attachment().remove(id);
							$thumbnails[id].remove();
							delete $thumbnails[id];
							t.triggerHandler('uploader.removed',[file, id, attachment().get()]);
							return this;
						},
						replace(targetID)
						{
							$thumbnails[targetID] = $thumbnails[id];
							$thumbnails[id].attr('data-id',targetID).data('id', targetID);
							delete $thumbnails[id];
							attachment().replace(id, targetID);
						},
						setFile: function(file){
							$thumbnails[id].data("file", file);
							return this;
						},
						getFile: function(){
							return $thumbnails[id].data("file");
						},
						removeAll: function(){
							for (var id in $thumbnails) {
								preview(id).remove();
							}
						},
						rebuildAll: function() {
							//remove all files
							var files = flex_uploader.uploader.getFiles();
							for(var i = 0;i < files.length;i++)
								flex_uploader.uploader.removeFile(files[i], true);
							//remove all preview
							this.removeAll();
							//build
							var aids = attachment().get();
							for (i = 0; i < aids.length; i++) {
								preview(aids[i]).build();
							}
							return this;
						}
					};
				};
				var attachment = function() {
					var aid = t.val();
					if (aid == '0') aid = '';
					var aids = aid ? ( aid instanceof Array ? aid : aid.split(',') ) : [];
					return {
						write: function() {
							if (t.is('select')){
								t.empty();
								aids.forEach(function(i){
									t.append('<option value="'+i+'">'+i+'</option>');
								});
							}
							t.val(aids);
							return this;
						},
						add: function(id) {
							if (options.filelimit == 1) aids = [id.toString()];
							var i = aids.indexOf(id.toString());
							if (i == -1) aids.push(id.toString());
							return this.write();
						},
						replace: function(encodedID, id) {
							var i = aids.indexOf(encodedID.toString());
							if (i != -1) aids[i] = id.toString();
							return this.write();
						},
						remove: function(id) {
							var i = aids.indexOf(id.toString());
							if (i != -1) aids.splice(i, 1);
							return this.write();
						},
						removeAll: function(){
							aids = [];
							return this.write();
						},
						get: function(){
							return aids;
						}
					};
				};


				//---------------------------------------
				method.beforeFileQueued = function(file) {
					if (options.filetype.split(',').indexOf(file.ext.toLowerCase()) == -1){
						$.alert(UPLOADER_LANGUAGE.filetype.replace('{{0}}', options.filetype));
						return false;
					}
					if (options.filelimit > 1 &&  attachment().get().length >= options.filelimit) {
						$.alert(UPLOADER_LANGUAGE.filenum_limite.replace('{{0}}', options.filelimit));
						return false;
					}
					return true;
				};
				method.fileQueued = function(file) {
					if (t.triggerHandler('uploader.uploading', [file, attachment().get()]) === false) return false;
					progress(file).init().thumb();

					//前台压缩图片，检查的是源文件的MD5，而非压缩之后的，所以直接上传。另外：压缩之后的图片暂时无法检查BUG
					if (options.max_width > 0 && options.max_height > 0 && (file.ext == 'jpg' || file.ext == 'jpeg'))
					{
						flex_uploader.uploader.upload(file);
						return true;
					}

					this.md5File( file ).progress(function(percentage) {
						progress(file).progressing(0).message(UPLOADER_LANGUAGE.hashing + ' ' + (percentage * 100).toFixed(2) + '%');
					}).then(function(val) {
						LP.PUT(LP.baseuri + 'attachment/hash', {
							hash: val,
							_token: LP.csrf,
							filename: file.name,
							ext: file.ext,
							size: file.size
						}).done(function(json) {
							flex_uploader.uploader.skipFile(file);
							progress(file).success().message(UPLOADER_LANGUAGE.hash_success);
							if (options.filelimit == 1) preview().removeAll();
							preview(json.data.id, json.data.filename, json.data.ext, json.data.url).build().setFile(file);
							t.triggerHandler('uploader.uploaded',[file, json, attachment().get()]);
						}).fail(function(){
							file.md5 = val;
							flex_uploader.uploader.upload(file);
						});
					});
					return true;
				};
				method.uploadBeforeSend = function(obj, data, headers) {
					//console.log(obj);
					data.uuid = obj.blob.ruid + obj.file.id + obj.file.size;
					data.start = obj.start;
					data.end = obj.end;
					data.chunks = obj.chunks;
					data.chunk = obj.chunk;
					data.total = obj.file.size;
					data.hash = obj.file.md5;
				};
				method.uploadStart = function(file) {
					
				};
				//上传过程中触发，携带上传进度。
				method.uploadProgress = function(file, percentage) {
					t.triggerHandler('uploader.progressing',[file, percentage * 100, attachment().get()]);
					progress(file).progressing(percentage * 100);
				};
				//当文件上传成功时触发。
				method.uploadSuccess = function(file, json) {
					var message = json.message && json.message.content ? json.message.content : json.message;
					if (json && (json.result == 'success' || json.result == 'api')) {
						progress(file).success();
						if (options.filelimit == 1) preview().removeAll();
						preview(json.data.id, json.data.filename, json.data.ext, json.data.url).build().setFile(file);
						t.triggerHandler('uploader.uploaded',[file, json, attachment().get()]);
					} else {
						progress(file).error(UPLOADER_LANGUAGE.error+': ' + message);
						t.triggerHandler('uploader.error',[file, message, attachment().get()]);
						//$.alert(message);
					}
				};
				
				//当文件上传出错时触发。
				method.uploadError = function(file, reason) {
					progress(file).error(UPLOADER_LANGUAGE.error+': ' + reason);
					t.triggerHandler('uploader.error',[file, reason, attachment().get()]);
				};
				//不管成功或者失败，文件上传完成时触发。
				method.uploadComplete = function(file) {
					
				};
				method.error = function(code, max, file) {
					switch(code)
					{
						case 'Q_EXCEED_NUM_LIMIT':
							$.alert(UPLOADER_LANGUAGE.num_limit.replace('{{0}}', max));
							break;
						case 'Q_EXCEED_SIZE_LIMIT':
							$.alert(UPLOADER_LANGUAGE.allsize_limit);
							break;
						case 'F_EXCEED_SIZE':
							$.alert(UPLOADER_LANGUAGE.filesize_limite.replace('{{0}}', bytesToSize(options.filesize)));
							break;
						case 'F_DUPLICATE':
							$.alert(UPLOADER_LANGUAGE.duplicate);
							break;
						case 'Q_TYPE_DENIED':
							$.alert(UPLOADER_LANGUAGE.filetype.replace('{{0}}', options.filetype));
							break;
						default:
							$.alert(code);
							break;
					}
				};

				method.create = function()
				{
					if (flex_uploader.uploader || typeof t.prop('flex_uploader') == 'undefined' || typeof t.prop('flex_uploader').uploader != 'undefined') return;
					flex_uploader.uploader = WebUploader.create({
						// swf文件路径
						swf: LP.baseuri + "static/js/webuploader/Uploader.swf",
						// 文件接收服务端。
						server: LP.baseuri + "attachment/uploader?of=json",
						// 选择文件的按钮。可选。内部根据当前运行是创建，可能是input元素，也可能是flash
						pick: {
							id: document.getElementById(pick_id),
							multiple: true
						},
						//表单附加数据
						formData: $.extend(true, {}, {'_token': LP.csrf}),
						//文件表单name
						fileVal: 'Filedata',
						//METHOD
						method: 'POST',
						//二进制上传，php://input都为文件内容，其他参数在$_GET中
						sendAsBinary: false,
						//可提交文件数量限制
						fileNumLimit: 0,
						//总文件大小限制
						//fileSizeLimit: 1024 * 1024 * 1024, //1G
						//单文件大小限制
						fileSingleSizeLimit: options.filesize,
						//是否去重
						duplicate: false,
						// 文件选择筛选。
						accept: {
							title: UPLOADER_LANGUAGE.select_file,
							extensions: options.filetype,
							mimeTypes: typeof mimeType != 'undefined' && !navigator.userAgent.indexOf('Chrome') ? options.filetype.split(',').map(function(v){return mimeType.lookup('name.' + v);}).join(',') : '*/*'
						},
						//是否允许在文件传输时提前把下一个文件的分片,MD5准备好
						prepareNextFile: true,
						//分多大一片？ 默认大小为5M.
						chunkSize: 5242880,
						//分片允许自动重传多少次
						chunkRetry: 5,
						//是否分片上传。
						chunked: true,
						//同时上传并发数
						threads: 3,
						//以下两个属性设置后，如果一个页面有多个uploader，会将rt_rt_xxxxx放到最后一个.webuploader-container容器内
						//指定拖拽的容器
						//dnd: document.getElementById(uploader_id),
						//全局禁用拉拽，防止默认打开文件
						//disableGlobalDnd: true,
						//可以粘贴的容器
						//paste: document.getElementById(uploader_id),
						thumb: {// 缩略图
							width: 75,
							height: 75,
							// 图片质量，只有type为`image/jpeg`的时候才有效。
							quality: 70,
							// 是否允许放大，如果想要生成小图的时候不失真，此选项应该设置为false.
							allowMagnify: false,
							// 是否允许裁剪。
							crop: true,
							// 为空的话则保留原有图片格式。
							// 否则强制转换成指定的类型。
							//type: 'image/jpeg'
						},
						// 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
						resize: false,
						compress: null
					});
					// 修改后图片上传前，尝试将图片压缩到max_width * max_height
					if (options.max_width > 0 && options.max_height > 0)
						flex_uploader.uploader.option( 'compress', {
							width: options.max_width,
							height: options.max_height,
							// 图片质量，只有type为`image/jpeg`的时候才有效。
							quality: 100,
							// 是否允许放大，如果想要生成小图的时候不失真，此选项应该设置为false.
							allowMagnify: false,
							// 是否允许裁剪。
							crop: false,
							// 是否保留头部meta信息。
							preserveHeaders: true,
							// 如果发现压缩后文件大小比原来还大，则使用原来图片。此属性可能会影响图片自动纠正功能
							noCompressIfLarger: true,
							// 单位字节，如果图片大小小于此值，不会采用压缩。
							compressSize: 0
						});
					flex_uploader.uploader.on('beforeFileQueued', method.beforeFileQueued);
					flex_uploader.uploader.on('fileQueued', method.fileQueued);
					flex_uploader.uploader.on('uploadBeforeSend', method.uploadBeforeSend);
					flex_uploader.uploader.on('uploadStart', method.uploadStart);
					flex_uploader.uploader.on('uploadProgress', method.uploadProgress);
					flex_uploader.uploader.on('uploadSuccess', method.uploadSuccess);
					flex_uploader.uploader.on('uploadError', method.uploadError);
					flex_uploader.uploader.on('uploadComplete', method.uploadComplete);
					flex_uploader.uploader.on('error', method.error);

					t.prop('flex_uploader', flex_uploader);
					//init
					if(options.id) attachment().add(options.id);
					preview().rebuildAll();
				};

				t.prop('flex_uploader', flex_uploader);

				if (!flex_uploader.$container.is(':visible')/* flex_uploader.$container[0].offsetParent === null*/) { //如果默认隐藏状态(即使是父级隐藏)，需要特殊处理
					method.interval = setInterval(function(){
						if (flex_uploader.$container.is(':visible'))
						{
							clearInterval(method.interval);
							method.create();
						}
					}, 500); //还能有更好的方法吗? MutationObserver?

				} else {
					method.create();
				}

			});
			
		}
	});
})(jQuery);