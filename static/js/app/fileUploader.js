(function (root, factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD
		define(['underscore', 'webuploader', 'jquery.jplayer'], function (_, WebUploader) {
			return (root['fileUploader'] = factory(_, WebUploader));
		});
	} else if (typeof module === 'object' && typeof module.exports === 'object') {
		// commonJS
		module.exports = factory(require("underscore"),require("webuploader"),require("jquery.jplayer"),require("filestyle"));
	} else {
		// 普通引入：构造函数使用3个全局变量，但是还需要依赖 'jquery.jplayer', 'bootstrap', 'filestyle'
		root['fileUploader'] = factory(jQuery, underscore, WebUploader);
	}
})(window, function(_, WebUploader){
	var uploader = {
		'defaultoptions' : {
			direct : false,
			global : false,
			dest_dir : '',
			callback : null,
			type : 'image',
			mode : '',
			multiple : false,
			allowUploadVideo : true,
			fileSizeLimit: false,
			uploader : {
				//fileNumLimit: 30,
				//fileSizeLimit: 4 * 1024 * 1024,
				//fileSingleSizeLimit: 30* 4 * 1024 * 1024
			}
		},
		'uploader' : {},

		'show' : function(callback, options){
			return this.init(callback, options);
		},
		'init' : function(callback, options) {
			var $this = this;
			$this.options = $.extend({}, $this.defaultoptions, options);
			$this.options.callback = callback;
			// 微信
			if(this.options.isWechat){
				if(options.account_error) {
					util.message('公众号号没有上传素材的权限', '', 'info');
					return false;
				}
			} else {
				if(this.options.global){
					this.options.global = 'global';
				} else {
					this.options.global = '';
				}
				document.cookie = "__fileupload_type="+ escape (this.options.type);
				document.cookie = "__fileupload_dest_dir="+ escape (this.options.dest_dir);
				document.cookie = "__fileupload_global="+ escape (this.options.global);
			}
			$('#modal-webuploader').remove();

			if ($('#modal-webuploader').length == 0) {
				$(document.body).append($this.buildHtml().mainDialog);
			}
			$this.modalobj = $('#modal-webuploader');
			$this.modalobj.modal('show');
			$this.modalobj.on('shown.bs.modal', function(){
				if (!$(this).data('init')) {
					switch($this.options.type){
						case 'image':	
						case 'thumb':	
							// 普通
							if(!$this.options.isWechat){
								$this.initRemote();
							}
							$this.initLocal();
							break;
						case 'audio':	
							$this.initLocalAudio();
							break;
						case 'voice':	
							$this.initLocalVoice();
							break;
						case 'video':	
							if(!$this.options.isWechat){
								$this.initVideoRemote();
							}
							// 新增图文时，不显示 '浏览视频'
							if($this.options.allowUploadVideo){
								$this.initLocalVideo();
							}
							break;
					}
					// 新增图文时，不显示 '上传视频'
					if($this.options.allowUploadVideo){
						$this['init' + $this.options.type.substring(0,1).toUpperCase() + $this.options.type.substring(1) + 'Uploader']();
					}
				}
			});
			return $this.modalobj;
		},		
		'initUploader' : function(type) {
			var $this = this, typeText, accept, fileNumLimit, fileSingleSizeLimit, fileSizeLimit, compress;
			switch(type) {
				case 'image': 
					typeText = '图片';
					typeUnit = '张';
					accept = {
						title: 'Images',
						extensions: 'gif,jpg,jpeg,bmp,png,ico',
						mimeTypes: 'image/gif,image/jpg,image/jpeg,image/bmp,image/png,image/ico'
					};
					fileNumLimit = 30;
					fileSingleSizeLimit = 5 * 1024 * 1024;
					fileSizeLimit = fileNumLimit * fileSingleSizeLimit;
					compress = $this.options.isWechat ? {
						quality: 80,
						preserveHeaders: true,
						noCompressIfLarger: true,
						compressSize: 1 * 1024 * 1024
					} : false;
					break;
				case 'audio': 
				case 'voice': 
					typeText = '音频';
					typeUnit = '个';
					accept = {
						title: 'Audios',
						extensions: 'mp3,wma,wav,amr',
						mimeTypes: 'audio/*'
					};
					fileNumLimit = 30;
					fileSingleSizeLimit = 6 * 1024 * 1024;
					fileSizeLimit = fileNumLimit * fileSingleSizeLimit;
					compress = false;
					if ($this.options.isWechat) {
						fileNumLimit = 5;
						if($this.options.mode == 'temp'){
							accept.extensions = 'mp3';
							fileSingleSizeLimit = 2 * 1024 * 1024;
							fileSizeLimit = 5 * 2 * 1024 * 1024;
						}else{
							fileSingleSizeLimit = 5 * 1024 * 1024;
							fileSizeLimit = 5 * 5 * 1024 * 1024;
						}
					}
					break;
				case 'video':
					typeText = '视频';
					typeUnit = '个';
					accept = {
						title: 'Video',
						extensions: 'rm,rmvb,wmv,avi,mpg,mpeg,mp4',
						mimeTypes: 'video/*' 		// 这里不确定
					};
					fileNumLimit = 30;
					fileSingleSizeLimit = 20 * 1024 * 1024;
					fileSizeLimit = fileNumLimit * fileSingleSizeLimit;
					compress = false;
					if ($this.options.isWechat) {
						fileNumLimit = 5;
						if($this.options.mode == 'temp'){
							accept.extensions = 'mp4';
							fileSingleSizeLimit = 10 * 1024 * 1024;
							fileSizeLimit = 5 * 10 * 1024 * 1024;
						}else{
							fileSingleSizeLimit = 20 * 1024 * 1024;
							fileSizeLimit = 5 * 20 * 1024 * 1024;
						}
					}
					break;
			}
			if($this.options.isWechat){
				$this.options.flag = 0;
				$this.modalobj.find('#li_upload_perm a').html('上传永久'+typeText);
				$this.modalobj.find('#li_upload_temp a').html('上传临时'+typeText+'(保留3天)');
			}else{
				$this.modalobj.find('#li_upload a').html('上传'+typeText);
			}
			$this.modalobj.find('.modal-body').append(this.buildHtml().uploaderDialog);
			var $wrap = $('#uploader'),
				// 图片容器
				$queue = $( '<ul class="filelist"><li class="fileinput-button js-add-image" id="filePicker2" style="display:none;"> <a href="javascript:;" class="fileinput-button-icon">+</a></li></ul>' )
					.appendTo( $wrap.find( '.queueList' ) ),
				// 状态栏，包括进度和控制按钮
				$statusBar = $wrap.find( '.statusBar' ),
				// 文件总体选择信息。
				$info = $statusBar.find( '.info' ),
				// 上传按钮
				$upload = $wrap.find( '.uploadBtn' ),
				// 没选择文件之前的内容。
				$placeHolder = $wrap.find( '.placeholder' ),
				$progress = $statusBar.find( '.progress' ).hide(),
				$confirmBtn = $wrap.find('.btn-primary');
				// 添加的文件数量
				fileCount = 0,
				// 添加的文件总大小
				fileSize = 0,
				// 优化retina, 在retina下这个值是2
				ratio = window.devicePixelRatio || 1,
				// 缩略图大小
				thumbnailWidth = 110 * ratio,
				thumbnailHeight = 110 * ratio,
				// 可能有pedding, ready, uploading, confirm, done.
				state = 'pedding',
				// 所有文件的进度信息，key为file id
				percentages = {},
				supportTransition = (function(){
					var s = document.createElement('p').style,
						r = 'transition' in s ||
							'WebkitTransition' in s ||
							'MozTransition' in s ||
							'msTransition' in s ||
							'OTransition' in s;
					s = null;
					return r;
				})(),uploader;

			var options = {
				pick: {
					id: '#filePicker',
					label: '点击选择'+typeText,
					multiple : true
				},
				dnd: '#dndArea',
				paste: '#uploader',
				// swf文件路径
				swf: './resource/componets/webuploader/Uploader.swf',
				// 文件接收服务端。
				server: $this.options.isWechat ? './index.php?c=utility&a=wechat_file&do=upload' : './index.php?c=utility&a=file&do=upload&upload_type='+type,
				compress: compress, 
				accept: accept,
				fileNumLimit: fileNumLimit,
				fileSizeLimit: fileSizeLimit,
				fileSingleSizeLimit: fileSingleSizeLimit,
			}
			// warning: 1.options是 'webUploader的上传配置'；2.$this.options是整个fileUploader的配置。注意区分开2者。注意区分开2者。注意区分开2者。注意区分开2者
			options = $.extend({}, options, $this.options.uploader);
			options.pick.multiple = $this.options.multiple;
			// 2个配置在下方需进行判别
			options.isWechat = $this.options.isWechat;
			options.type = $this.options.type
			if(type == 'audio' || type == 'voice'){
				if($this.options.isWechat){
					$('#dndArea p').html('临时语音只支持amr/mp3格式,大小不超过为2M,长度不超过60秒<br>永久语音只支持mp3/wma/wav/amr格式,大小不超过为5M,长度不超过60秒');
				}else{
					$('#dndArea p').html('最大支持 '+(WebUploader.formatSize(options.fileSingleSizeLimit ))+' MB 以内的语音 ('+(options.accept.extensions)+' 格式)');
				}
			}else if(type == 'video'){
				if($this.options.isWechat){
					$('#dndArea p').html('临时视频只支持mp4格式,大小不超过为10M<br>永久视频只支持rm/rmvb/wmv/avi/mpg/mpeg/mp4格式,大小不超过为20M');
				}else{
					$('#dndArea p').html('最大支持 '+(WebUploader.formatSize(options.fileSingleSizeLimit ))+' MB 以内的视频 ('+(options.accept.extensions)+' 格式)');
				}
			}
			if ($this.options.fileSizeLimit) {
				options.fileSizeLimit = $this.options.fileSizeLimit;
			}
			// 实例化
			uploader = WebUploader.create(options);
			uploader.uploadedFiles = [];
			// 添加“添加文件”的按钮，
			uploader.addButton({
				id: '#filePicker2',
				label: '+',
				multiple : $this.options.multiple
			});
			// 成功上传
			accept = 0;
			uploader.resetUploader = function(){
				fileCount = 0;
				fileSize = 0;
				accept = 0;
				uploader.uploadedFiles = [];
				$.each(uploader.getFiles(), function(index, file){
					removeFile( file );
				});
				if($this.options.isWechat){
					if($this.options.video){
						$("#upload :text[name='title']").val('');
						$("#upload :text[name='introduction']").val('');
					}
					updateTotalProgress();
					uploader.reset();
					uploader.refresh();
					$('#dndArea').removeClass('element-invisible');
					$('#uploader').find('.filelist').empty();
					$('#filePicker').find('.webuploader-pick').next().css({'left':'190px'});
					var bar = $('#uploader').find('.statusBar');
					bar.find('.info').empty();
					bar.find('.accept').empty();
					bar.hide();
				}else{
					uploader.refresh();
					uploader.reset();
					$upload.removeClass( 'disabled' );
					setState( 'pedding' );
				}
			}
			// 当有文件添加进来时执行，负责view的创建
			function addFile( file ) {
				var $li = $( '<li id="' + file.id + '">' +
						'<p class="title"'+(type=='audio' || type=='voice' ? 'style="top:40px;"' : '')+'>' + file.name + '</p>' +
						'<p class="imgWrap"'+(type=='audio' || type=='voice' ? 'style="top:30px;"' : '')+'></p>'+
						//'<p class="progress"><span></span></p>' +
						'</li>' ),
					$btns = $('<div class="file-panel">' +
						'<span class="cancel">删除</span></div>').appendTo( $li ),
					$prgress = $li.find('p.progress span'),
					$wrap = $li.find( 'p.imgWrap' ),
					$info = $('<p class="error"></p>'),

					showError = function( code ) {
						switch( code ) {
							case 'exceed_size':
								text = '文件大小超出';
								break;
							case 'interrupt':
								text = '上传暂停';
								break;
							default:
								text = '上传失败，请重试';
								break;
						}
						$info.text( text ).appendTo( $li );
					};
				if ( file.getStatus() === 'invalid' ) {
					showError( file.statusText );
				} else {
					// @todo lazyload
					if(type == 'image'){
						$wrap.text( '预览中' );
						uploader.makeThumb( file, function( error, src ) {
							if ( error ) {
								$wrap.text( '不能预览' );
								return;
							}
							var img = $('<img src="'+src+'">');
							$wrap.empty().append( img );
						}, thumbnailWidth, thumbnailHeight );
					}else{
						$wrap.text(WebUploader.formatSize( file.size ) + ' kb');
					}
					percentages[ file.id ] = [ file.size, 0 ];
					file.rotation = 0;
				}
				file.on('statuschange', function( cur, prev ) {
					if ( prev === 'progress' ) {
						$prgress.hide().width(0);
					} else if ( prev === 'queued' ) {
						$li.off( 'mouseenter mouseleave' );
						$btns.remove();
					}
					// 成功
					if ( cur === 'error' || cur === 'invalid' ) {
						showError( file.statusText );
						percentages[ file.id ][ 1 ] = 1;
					} else if ( cur === 'interrupt' ) {
						showError( 'interrupt' );
					} else if ( cur === 'queued' ) {
						percentages[ file.id ][ 1 ] = 0;
					} else if ( cur === 'progress' ) {
						$info.remove();
						if(type == 'image'){
							$prgress.css('display', 'block');
						}
					} else if ( cur === 'complete' ) {
						//$li.append( '<span class="success"></span>' );
					}
					$li.removeClass( 'state-' + prev ).addClass( 'state-' + cur );
				});

				$li.on( 'mouseenter', function() {
					$btns.stop().animate({height: 30});
				});

				$li.on( 'mouseleave', function() {
					$btns.stop().animate({height: 0});
				});

				$btns.on( 'click', 'span', function() {
					var index = $(this).index(),
						deg;
					switch ( index ) {
						case 0:
							uploader.removeFile( file );
							return;
						case 1:
							file.rotation += 90;
							break;
						case 2:
							file.rotation -= 90;
							break;
					}
					if ( supportTransition ) {
						deg = 'rotate(' + file.rotation + 'deg)';
						$wrap.css({
							'-webkit-transform': deg,
							'-mos-transform': deg,
							'-o-transform': deg,
							'transform': deg
						});
					} else {
						$wrap.css( 'filter', 'progid:DXImageTransform.Microsoft.BasicImage(rotation='+ (~~((file.rotation/90)%4 + 4)%4) +')');
					}
				});
				if ($this.options.multiple) {
					$queue.find('.fileinput-button').show();
				}
				$li.insertBefore($queue.find('.fileinput-button'));
			}

			// 负责view的销毁
			function removeFile( file ) {
				var $li = $('#'+file.id);
				delete percentages[ file.id ];
				updateTotalProgress();
				$li.off().find('.file-panel').off().end().remove();
			}

			function updateTotalProgress() {
				var loaded = 0,
					total = 0,
					spans = $progress.children(),
					percent;
				
				$.each( percentages, function( k, v ) {
					total += v[ 0 ];
					loaded += v[ 0 ] * v[ 1 ];
				} );
				
				percent = total ? loaded / total : 0;
				
				spans.eq( 0 ).text( Math.round( percent * 100 ) + '%' );
				spans.eq( 1 ).css( 'width', Math.round( percent * 100 ) + '%' );
				updateStatus();
			}

			function updateStatus() {
				var text = '', stats;

				if ( state === 'ready' ) {
					if($this.options.isWechat){

						//根据素材类型（临时或永久）更改上传地址
						if($this.options.mode == '') {
							var mode = $this.modalobj.find('.nav-pills li.active').attr('data-mode');
						} else {
							var mode = $this.options.mode;
						}
						if(!$this.options.flag) {
							uploader.option('server', uploader.option('server') + '&mode=' + mode + '&types=' + $this.options.type);
							$this.options.flag = 1;
						}
					}
					text = '选中' + fileCount + typeUnit + typeText +'，共' + WebUploader.formatSize( fileSize ) + '。';
				} else if ( state === 'confirm' ) {
					stats = uploader.getStats();
					if ( stats.uploadFailNum ) {
						text = '已上传'+stats.successNum+typeUnit+typeText+','+stats.uploadFailNum+typeUnit+typeText+'上传失败，<a class="retry" href="#">重新上传</a>失败'+typeText+'或<a class="ignore" href="#">忽略</a>'
					}
				} else {
					stats = uploader.getStats();
					text = '共'+fileCount+typeUnit+'（'+WebUploader.formatSize(fileSize)+'），已上传' + stats.successNum + typeUnit;
					if ( stats.uploadFailNum ) {
						text += '，失败' + stats.uploadFailNum + typeUnit;
					}
				}
				$info.html( text );
			}
			function setState( val ) {
				var file, stats;
				if ( val === state ) {
					return;
				}
				$upload.removeClass( 'state-' + state );
				$upload.addClass( 'state-' + val );
				state = val;
				switch ( state ) {
					case 'pedding':
						$placeHolder.removeClass( 'element-invisible' );
						$queue.hide();
						uploader.refresh();
						break;
					case 'ready':
						$placeHolder.addClass( 'element-invisible' );
						if($this.options.isWechat && $this.options.type == 'video'){
							$('#upload form').removeClass('hide');
						}
						$queue.show();
						uploader.refresh();
						break;
					case 'uploading':
						$progress.show();
						$upload.text( '暂停上传' );
						break;
					case 'paused':
						$progress.show();
						$upload.text( '继续上传' );
						break;
					case 'confirm':
						$progress.hide();
						$upload.text( '开始上传' ).addClass( 'disabled' );
						stats = uploader.getStats();
						if ( stats.successNum && !stats.uploadFailNum ) {
							setState( 'finish' );
							return;
						}
						break;
					case 'finish':
						$upload.removeClass( 'disabled' );
						stats = uploader.getStats();
						if ( stats.successNum ) {
							if (uploader.uploadedFiles.length > 0) {
								$this.finish(uploader.uploadedFiles);
								uploader.resetUploader();
								return;
							}
						} else {
							// 没有成功的图片，重设
							state = 'done';
							location.reload();
						}
						break;
				}
				updateStatus();
			}
			uploader.onUploadProgress = function( file, percentage ) {
				var $li = $('#'+file.id),
					$percent = $li.find('.progress span');
				$percent.css( 'width', percentage * 100 + '%' );
				percentages[ file.id ][ 1 ] = percentage;
				fileid = file.id;
				updateTotalProgress();
			};
			uploader.onFileQueued = function( file ) {
				fileCount++;
				fileSize += file.size;
				
				if ( fileCount === 1 ) {
					$placeHolder.addClass( 'element-invisible' );
					$statusBar.show();
				}
				
				addFile( file );
				setState( 'ready' );
				updateTotalProgress();
			};
			uploader.onFileDequeued = function( file ) {
				fileCount--;
				fileSize -= file.size;
				
				if ( !fileCount ) {
					setState( 'pedding' );
				}

				removeFile( file );
				updateTotalProgress();
			};
			uploader.on( 'all', function( type ) {
				var stats;
				switch( type ) {
					case 'uploadFinished':
						setState( 'confirm' );
						break;
					case 'startUpload':
						setState( 'uploading' );
						break;
					case 'stopUpload':
						setState( 'paused' );
						break;
				}
			});
			uploader.on( 'uploadSuccess', function(file, result) {
				if (result.message){
					alert(result.message);
					uploader.resetUploader();
					return ;
				}
				if (result.attachment || result.media_id){
					accept++;
					uploader.uploadedFiles.push(result);
					$('#'+file.id).append( '<span class="success" style="line-height: 50px;">'+result.width +'x'+ result.height +'</span>' );
					$('.accept').text('成功上传 '+accept+' '+typeUnit+typeText);
				}
			});
			uploader.onError = function( code ) {
				if(code == 'Q_EXCEED_SIZE_LIMIT'){
					alert('错误信息: '+typeText+'大于 '+WebUploader.formatSize(options.fileSizeLimit )+' 无法上传.');
					return
				}
				if(code == 'F_DUPLICATE'){
					alert('错误信息: 不能重复上传'+typeText+'.');
					return
				}
				alert( 'Eroor: ' + code );
			};
			$upload.on('click', function() {
				if ( $(this).hasClass( 'disabled' ) ) {
					return false;
				}
				if(state != 'pedding' && uploader.options.isWechat && uploader.options.type == 'video'){
					var title = $('#upload :text[name="title"]').val();
					var introduction = $('#upload textarea[name="introduction"]').val();
					if(!title) {
						util.message('视频标题不能为空');
						return false;
					}
					if(!introduction) {
						util.message('视频描述不能为空');
						return false;
					}
					uploader.option('formData', {title : title, introduction : introduction});
				}
				if ( state === 'ready' ) {
					uploader.upload();
				} else if ( state === 'paused' ) {
					uploader.upload();
				} else if ( state === 'uploading' ) {
					uploader.stop();
				}
			});

			$info.on( 'click', '.retry', function() {
				uploader.retry();
			} );

			$info.on( 'click', '.ignore', function() {
				// alert( 'todo' );
			} );

			$upload.addClass( 'state-' + state );
			updateTotalProgress();
		},
		'initImageUploader' : function () {
			this.initUploader('image');
		},
		'initAudioUploader' : function () {
			this.initUploader('audio');
		},
		'initVoiceUploader' : function () {
			this.initUploader('voice');
		},
		'initVideoUploader' : function () {
			this.initUploader('video');
		},
		'initRemote' : function() {
			var $this = this;
			$this.modalobj.find('#li_network').removeClass('hide');
			$this.modalobj.find('.modal-body').append($this.buildHtml().remoteDialog);
			$this.modalobj.find('.btn-primary').click(function(){
				var url = $this.modalobj.find('#networkurl').val();
				if (url.length > 0 && $this.options.type == 'image'){
					$.getJSON('./index.php?c=utility&a=file&do=fetch', {'url':url}, function(result){
						if(result.message){
							alert(result.message);
						}
						if (result) {
							$this.finish([result]);
							result = {};
						}
					});
				}
			});
		},
		'initVideoRemote' : function() {
			var $this = this;
			$this.modalobj.find('#li_network').removeClass('hide');
			$this.modalobj.find('.modal-body').append($this.buildHtml().remoteVideoDialog);
			$this.modalobj.find('#networkurl').blur(function(){
				var url = $(this).val();
				if (url.length > 0) {
					createPreviewVideo(url);
				} else {
					$('#preview').html('');	
				}
			});
			$this.modalobj.find('.btn-primary').click(function(){
				var url = $this.modalobj.find('#networkurl').val();
				if (url.length > 0 && $this.options.type == 'video'){
					// 远程获取视频地址
					var conUrl = convert_url(url);
					conUrl = unhtmlForUrl(conUrl);
					$this.finish([{'url' : conUrl, 'isRemote' : true}]);		// 添加一个isRemote，在百度编辑器中进行判断
				}
			});
			function createPreviewVideo(url){
				if ( !url )return;
				var conUrl = convert_url(url);
				conUrl = unhtmlForUrl(conUrl);
				$("#preview").html('<div style="position:absolute;top:0;margin:0;padding:120px 50px;width:100%;height:100%;font-size:20px;"><span>只支持 腾讯，优酷，土豆视频，如无法预览视频，请前往视频网址处的分享区域，复制通用地址到编辑器内部！</span></div>'+
				'<iframe src="'+conUrl+'" allowfullscreen="true" style="border:0;position:absolute;top:0;left:0;margin:0;padding:0;width:100%;height:100%;"></iframe>');
			}
			function convert_url(url){
				if ( !url ) return '';
				var id, iframe_url;
				if (url.indexOf('v.qq.com') >= 0) {
					id = url.match(/vid\=([^\&]*)($|\&)/);
					if	(id) {
						iframe_url = 'http://v.qq.com/iframe/player.html?vid='+id[1]+'&tiny=0&auto=0';
					} else {
						id = url.match(/\/([0-9a-zA-Z]+).html/);
						if(id) {
							iframe_url = 'http://v.qq.com/iframe/player.html?vid='+id[1]+'&tiny=0&auto=0';
						}
					}
					if (!id) {
						return;
					}
				} else if (url.indexOf('v.youku.com') >= 0) {
					id = url.match(/id_(.*)\.html/);
					iframe_url = 'http://player.youku.com/embed/' + id[1];
				} else if (url.indexOf('tudou.com') >= 0) {
					id = url.match(/\/([-\w]+)/g);
					id = id[id.length - 1].substring(1);
					iframe_url = 'http://www.tudou.com/programs/view/html5embed.action?code=' + id;
				} else {
					return;
				}
				return iframe_url;
			}
			function unhtmlForUrl(str, reg) {
				return str ? str.replace(reg || /[<">']/g, function (a) {
					return {
						'<':'&lt;',
						'&':'&amp;',
						'"':'&quot;',
						'>':'&gt;',
						"'":'&#39;'
					}[a]

				}) : '';
			}
		},
		'initLocal' : function() {
			var $this = this;
			$this.modalobj.find('#li_history_image').removeClass('hide');
			$this.modalobj.find('.modal-body').append(this.buildHtml().localDialog);
			$this.localPage(1);
		},
		'localPage' : function(page) {
			var $this = this;
			if($this.options.isWechat){
				var type = $this.options.type;
				var mode = $this.options.mode;
				var url = './index.php?c=utility&a=wechat_file&do=browser';
				var params = {'page': page, 'type' : type, 'mode' : mode, 'psize' : 32};
			}else{
				var year = $this.modalobj.find('#select-year .btn-info').data('id');
				var month = $this.modalobj.find('#select-month .btn-info').data('id');
				var url = './index.php?c=utility&a=file&do=local';
				var params = {'page': page, 'year': year, 'month': month, 'pagesize':36};
			}
			var $history = $this.modalobj.find('#history_image');
			$.getJSON(url, params, function(data){
				data = data.message.message;
				$history.find('.history-content').css('text-align', 'center').html('<i class="fa fa-spinner fa-pulse fa-5x"></i>');
				$history.find('#image-list-pager').html('');
				if(!_.isEmpty(data.items)) {
					$history.data('attachment', data.items);
					$history.find('.history-content').empty();
					$history.find('.history-content').html(_.template($this.buildHtml()[$this.options.isWechat ? 'weixin_localDialogLi' : 'localDialogLi'])(data));
					$history.find('#image-list-pager').html(data.page);
					$history.find('.pagination a').click(function(){
						$this.localPage($(this).attr('page'));
					});
					$history.find('.img-list li').click(function(event){
						$this.selectImage($(event.target).parents('li'));
					});
					//删除
					if($this.options.isWechat){
						$this.weixinDeletefile();
					}else{
						$this.deletefile();
					}
				} else {
					$history.find('.history-content').css('text-align', 'left').html('<i class="fa fa-info-circle"></i> 暂无数据');
				}
			});

			if(!$this.options.isWechat){
				$this.modalobj.find('.btn-select').unbind('click').click(function(){
					if($(this).hasClass('btn-info')) {
						return false;
					}
					if($(this).data('type') == 'month' && $(this).data('id') > 0) {
						if(!$this.modalobj.find('#select-year .btn-info').data('id')) {
							$this.modalobj.find('#select-year .btn-select').removeClass('btn-info');
							$this.modalobj.find('#select-year .btn-select').eq(1).addClass('btn-info');
						}
					}
					$(this).siblings().removeClass('btn-info');
					$(this).addClass('btn-info');
					$this.localPage(1);
				});
			}

			$history.find('.btn-primary').unbind('click').click(function(){
				var attachment = [];
				$history.find('.img-item-selected').each(function(){
					attachment.push($this.modalobj.find('#history_image').data('attachment')[$(this).attr('attachid')]);
					$(this).removeClass('img-item-selected');
				});
				$this.finish(attachment);
			});
			return false;
		},
		'deletefile' : function(){
			var $this = this;
			$this.modalobj.find('#history_image .img-list li .btnClose').unbind().click(function(){
				var $this = $(this);
				var id = $(this).data('id');
				if(!id) return false;
				$.post('./index.php?c=utility&a=file&do=delete', {id:id}, function(data){
					if(data != 'success') {
						alert(data);
					} else {
						$this.parent().remove();
						util.message('删除成功', '', 'success');
					}
				});
				return false;
			});
		},
		'weixinDeletefile' : function() {
			var $this = this;
			$this.modalobj.find('.history .delete-file').off('click');
			$this.modalobj.find('.history .delete-file').on('click', function(event){
				var $this = $(this);
				if (confirm("确定要删除文件吗？")){
					var id = $(this).parent().attr('attachid');
					var type =	$(this).parent().attr('data-type');
					$.post('./index.php?c=utility&a=wechat_file&do=delete', {'id' : id}, function(data){
						var data = $.parseJSON(data);
						if(!data.error) {
							util.message(data.message);
							return false;
						}
						if(type == 'image') {
							$this.parent().remove();
						} else if(type == 'audio' || type == 'voice' || type == 'video') {
							$this.parents('tr').remove();
						}
					});
				}
				event.stopPropagation();
			});
		},
		'selectImage' : function(obj) {
			var $this = this;
			$(obj).toggleClass('img-item-selected');
			// 微信
			if($this.options.isWechat){
				if ($this.options.direct) {
					$this.modalobj.find('#history_image').find('.btn-primary').trigger('click');
				}
			}else{
				if (!$this.options.multiple) {
					$this.modalobj.find('#history_image').find('.btn-primary').trigger('click');
				}
			}
		},
		'initLocalAudio' : function() {
			var $this = this;
			$this.modalobj.find('#li_history_audio').removeClass('hide');
			$this.modalobj.find('.modal-body').append(this.buildHtml().localAudioDialog);
			$this.localAudioPage(1);
		},
		'localAudioPage' : function(page) {
			var $this = this;
			if($this.options.isWechat){
				var type = $this.options.type;
				var mode = $this.options.mode;
				var url = './index.php?c=utility&a=wechat_file&do=browser';
				var params = {'page': page, 'type' : type, 'mode' : mode, 'psize' : 5};
			}else{
				var url = './index.php?c=utility&a=file&do=local&type=audio&pagesize=5';
				var params = {'page': page};
			}
			var $history = $this.modalobj.find('#history_audio');
			$.getJSON(url, params, function(data){
				data = data.message;
				$history.find('.history-content').html('<i class="fa fa-spinner fa-pulse"></i>');
				if(!_.isEmpty(data.items)) {
					$history.data('attachment', data.items);
					$history.find('.history-content').empty();
					$history.find('.history-content').html(_.template($this.buildHtml()[$this.options.isWechat ? 'weixin_localAudioDialogLi' : 'localAudioDialogLi'])(data));
					$history.find('#image-list-pager').html(data.page);
					$history.find('.pagination a').click(function(){
						$this.localAudioPage($(this).attr('page'));
					});
					$history.find('.js-btn-select').click(function(event){
						$(event.target).toggleClass('btn-primary');
						// 微信
						if($this.options.isWechat){
							if ($this.options.direct) {
								$this.modalobj.find('#history_audio').find('.modal-footer .btn-primary').trigger('click');
							}
						}else{
							if (!$this.options.multiple) {
								$this.modalobj.find('#history_audio').find('.modal-footer .btn-primary').trigger('click');
							}
						}
					});
					$this.playAudio();
					//微信
					if($this.options.isWechat){
						$this.weixinDeletefile();
					}
				}else{
					$history.find('.history-content').css('text-align', 'center').html('<i class="fa fa-info-circle"></i> 暂无数据');
				}
			});
			$history.find('.modal-footer .btn-primary').unbind('click').click(function(){
				var attachment = [];
				$history.find('.history-content .btn-primary').each(function(){
					attachment.push($this.modalobj.find('#history_audio').data('attachment')[$(this).attr('attachid')]);
					$(this).removeClass('btn-primary');
				});
				$this.finish(attachment);
			});
			return false;
		},
		'playAudio' : function (){
			var $this = this;
			var $history = $this.modalobj.find('#history_audio');
			$(".audio-player-play").click(function(){
				var src = $(this).attr("attach");
				if(!src) {
					return;
				}
				if ($("#player")[0]) {
					var player = $("#player");
				} else {
					var player = $('<div id="player"></div>');
					$(document.body).append(player);
				}
				player.data('control', $(this));
				player.jPlayer({
					playing: function() {
						$(this).data('control').find("p").removeClass("fa-play").addClass("fa-stop");
					},
					pause: function (event) {
						$(this).data('control').find("p").removeClass("fa-stop").addClass("fa-play");
					},
					swfPath: "resource/components/jplayer",
					supplied: "mp3,wma,wav,amr",
					solution: "html, flash",
				});
				player.jPlayer("setMedia", {mp3: $(this).attr("attach")}).jPlayer("play");
				if($(this).find("p").hasClass("fa-stop")) {
					player.jPlayer("stop");
				} else {
					$history.find('.fa-stop').removeClass("fa-stop").addClass("fa-play");
					player.jPlayer("setMedia", {mp3: $(this).attr("attach")}).jPlayer("play");
				}
			});
		},
		'initLocalVoice' : function() {
			this.initLocalAudio();	
		},
		'initLocalVideo' : function() {
			var $this = this;
			$this.modalobj.find('#li_history_video').removeClass('hide');
			$this.modalobj.find('.modal-body').append(this.buildHtml().localVideoDialog);
			$this.localVideoPage(1);
		},
		'localVideoPage' : function(page) {
			var $this = this;
			if($this.options.isWechat){
				var type = $this.options.type;
				var url = './index.php?c=utility&a=wechat_file&do=browser';
				var params = {'page': page, 'type' : type, 'psize' : 5};
			}else{
				var url = './index.php?c=utility&a=file&do=local&type=video&pagesize=5';
				var params = {'page': page};
			}
			var $history = $this.modalobj.find('#history_video');
			$.getJSON(url, params, function(data){
				data = data.message;
				$history.find('.history-content').html('<i class="fa fa-spinner fa-pulse"></i>');
				if(!_.isEmpty(data.items)) {
					$history.data('attachment', data.items);
					$history.find('.history-content').empty();
					$history.find('.history-content').html(_.template($this.buildHtml()[$this.options.isWechat ? 'weixin_localVideoDialogLi' : 'localVideoDialogLi'])(data));
					$history.find('#image-list-pager').html(data.page);
					$history.find('.pagination a').click(function(){
						$this.localVideoPage($(this).attr('page'));
					});
					$history.find('.js-btn-select').click(function(event){
						$(event.target).toggleClass('btn-primary');
						// 微信
						if($this.options.isWechat){
							if ($this.options.direct) {
								$this.modalobj.find('#history_video').find('.modal-footer .btn-primary').trigger('click');
							}
						}else{
							if (!$this.options.multiple) {
								$this.modalobj.find('#history_video').find('.modal-footer .btn-primary').trigger('click');
							}
						}
					});
					// 微信
					if($this.options.isWechat){
						$this.weixinDeletefile();
					}
				}else{
					$history.find('.history-content').css('text-align', 'left').html('<i class="fa fa-info-circle"></i> 暂无数据');
				}
			});
			$history.find('.modal-footer .btn-primary').unbind('click').click(function(){
				var attachment = [];
				$history.find('.history-content .btn-primary').each(function(){
					attachment.push($this.modalobj.find('#history_video').data('attachment')[$(this).attr('attachid')]);
					$(this).removeClass('btn-primary');
				});
				$this.finish(attachment);
			});
			return false;
		},
		'finish' : function(attachment) {
			var $this = this;
			if($.isFunction($this.options.callback)) {
				if ($this.options.multiple == false) {
					$this.options.callback(attachment[0]);
				} else {
					$this.options.callback(attachment);
				}
				$this.modalobj.modal('hide');
			}
		},
		'buildHtml' : function() {
			var dialog = {};
			dialog['mainDialog'] = '<div id="modal-webuploader" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">\n' +
				'	<div class="modal-dialog">\n' +
				'		<div class="modal-content" style="width:785px">\n' +
				'			<div class="modal-header">\n' +
				'				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>\n' +
				'				<ul class="nav nav-pills" role="tablist">\n' +
				'					<li id="li_upload" '+(!this.options.isWechat && this.options.allowUploadVideo ? 'class="active"' : 'class="hide"')+' role="presentation"><a href="#upload" aria-controls="upload" role="tab" data-toggle="tab" onclick="$(\'#select\').hide();">上传</a></li>\n' +
				'					<li id="li_upload_perm" '+(this.options.isWechat ? 'class="active"' : 'class="hide"')+' data-mode="perm" role="presentation"><a href="#upload" aria-controls="upload" role="tab" data-toggle="tab" onclick="$(\'#select\').hide();">上传</a></li>\n' +
				'					<li id="li_upload_temp" '+(this.options.isWechat ? '' : 'class="hide"')+'data-mode="temp" role="presentation"><a href="#upload" aria-controls="upload" role="tab" data-toggle="tab" onclick="$(\'#select\').hide();">上传</a></li>\n' +
				'					<li id="li_network" '+(!this.options.allowUploadVideo ? 'class="active"' : 'class="hide"')+' role="presentation"><a href="#network" aria-controls="network" role="tab" data-toggle="tab" onclick="$(\'#select\').hide();">提取网络'+(this.options.type == 'video' ? '视频' : '图片')+'</a></li>\n' +
				'					<li id="li_history_image" class="hide" role="presentation"><a href="#history_image" aria-controls="history_image" role="tab" data-toggle="tab" onclick="$(\'#select\').show();">浏览图片</a></li>\n' +
				'					<li id="li_history_audio" class="hide" role="presentation"><a href="#history_audio" aria-controls="history_audio" role="tab" data-toggle="tab" onclick="$(\'#select\').hide();">浏览音频</a></li>\n' +
				'					<li id="li_history_video" class="hide" role="presentation"><a href="#history_video" aria-controls="history_video" role="tab" data-toggle="tab">浏览视频</a></li>\n' +
				'				</ul>\n' +
				'			</div>\n' +
				(!this.options.isWechat ? 
				'				<div id="select" style="display: none;margin:10px 0 -10px 15px; padding-left:7px;">'+
				'					<div id="select-year" style="margin-bottom:10px;">'+
				'						<div class="btn-group">'+
				'							<a href="javascript:;" data-id="0" data-type="year" class="btn btn-default btn-info btn-select">不限</a>'+
				'							<a href="javascript:;" data-id="2016" data-type="year" class="btn btn-default btn-select">2016年</a>'+
				'							<a href="javascript:;" data-id="2015" data-type="year" class="btn btn-default btn-select">2015年</a>'+
				'							<a href="javascript:;" data-id="2014" data-type="year" class="btn btn-default btn-select">2014年</a>'+
				'							<a href="javascript:;" data-id="2013" data-type="year" class="btn btn-default btn-select">2013年</a>'+
				'						</div>'+
				'					</div>'+
				'					<div id="select-month">'+
				'						<div class="btn-group">'+
				'							<a href="javascript:;" data-id="0" data-type="month" class="btn btn-default btn-info btn-select">不限</a>'+
				'							<a href="javascript:;" data-id="1" data-type="month" class="btn btn-default btn-select">1</a>'+
				'							<a href="javascript:;" data-id="2" data-type="month" class="btn btn-default btn-select">2</a>'+
				'							<a href="javascript:;" data-id="3" data-type="month" class="btn btn-default btn-select">3</a>'+
				'							<a href="javascript:;" data-id="4" data-type="month" class="btn btn-default btn-select">4</a>'+
				'							<a href="javascript:;" data-id="5" data-type="month" class="btn btn-default btn-select">5</a>'+
				'							<a href="javascript:;" data-id="6" data-type="month" class="btn btn-default btn-select">6</a>'+
				'							<a href="javascript:;" data-id="7" data-type="month" class="btn btn-default btn-select">7</a>'+
				'							<a href="javascript:;" data-id="8" data-type="month" class="btn btn-default btn-select">8</a>'+
				'							<a href="javascript:;" data-id="9" data-type="month" class="btn btn-default btn-select">9</a>'+
				'							<a href="javascript:;" data-id="10" data-type="month" class="btn btn-default btn-select">10</a>'+
				'							<a href="javascript:;" data-id="11" data-type="month" class="btn btn-default btn-select">11</a>'+
				'							<a href="javascript:;" data-id="12" data-type="month" class="btn btn-default btn-select">12</a>'+
				'						</div>'+
				'					</div>'+
				'				</div>' : '') +
				'			<div class="modal-body tab-content"></div>\n' +
				'		</div>\n' +
				'	</div>\n' +
				'</div>';

			dialog['uploaderDialog'] = '<div role="tabpanel" class="tab-pane upload active" id="upload">\n' +
				(this.options.isWechat && this.options.type == 'video' ? 
				'<form class="form-horizontal hide" style="padding-right:10px;">' +
				'			<div class="form-group">' +
				'				<label class="col-xs-12 col-sm-2 control-label">视频标题</label>' +
				'				<div class="col-sm-10">' +
				'					<input type="text" name="title" class="form-control" placeholder="视频标题">'+
				'				</div>' +
				'			</div>' +
				'			<div class="form-group">' +
				'				<label class="col-xs-12 col-sm-2 control-label">视频描述</label>' +
				'				<div class="col-sm-10">' +
				'					<textarea name="introduction" class="form-control" placeholder="视频描述"></textarea>'+
				'				</div>' +
				'			</div></form>' : '') +
				'	<div id="uploader" class="uploader">\n' +
				'		<div class="queueList">\n' +
				'			<div id="dndArea" class="placeholder">\n' +
				'				<div id="filePicker">xx</div>\n' +
				(this.options.multiple ? '<p id="">或将照片拖到这里，单次最多可选'+(this.options.isWechat ? 5 : 30)+'张</p>\n' : '<p id="">或将照片拖到这里</p>\n') +
				'			</div>\n' +
				'		</div>\n' +
				'		<div class="statusBar">\n' +
				'			<div class="infowrap">\n' +
				'				<div class="progress">\n' +
				'					<span class="text">0%</span>\n' +
				'					<span class="percentage"></span>\n' +
				'				</div>\n' +
				'				<div class="info"></div>\n' +
				'				<div class="accept"></div>\n' +
				'			</div>\n' +
				'			<div class="btns">\n' +
				'				<div class="uploadBtn btn btn-primary" style="margin-top: 4px;">确定使用</div>\n' +
				'				<div class="modal-button-upload" style="float: right; margin-left: 5px;">\n' +
				'					<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>\n' +
				'				</div>\n' +
				'			</div>\n' +
				'		</div>\n' +
				'	</div>\n' +
				'</div>';
			dialog['remoteDialog'] = '<div role="tabpanel" class="tab-pane network" id="network">\n' +
				'	<div style="margin-top: 10px;">\n' +
				'		<form>\n' +
				'			<div class="form-group">\n' +
				'				<input type="url" class="form-control" id="networkurl" placeholder="请输入网络图片地址">\n' + 
				'				<input type="hidden" name="network_attachment" value="" >\n' +
				'				<div id="network-img" class="network-img" style="background-image:url(\'{php echo tomedia(\'images/global/nopic.jpg\');}\')">\n' +
				'					<span class="network-img-sizeinfo" id="network-img-sizeinfo"></span>\n' +
				'				</div>\n' +
				'			</div>\n' +
				'		</form>\n' +
				'	</div>\n' +
				'	<div class="modal-footer" style="margin:0 -30px -30px;">\n' +
				'		<button type="button" class="btn btn-primary">确认</button>\n' +
				'		<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>\n' +
				'	</div>\n' +
				'</div>';
			dialog['remoteVideoDialog'] = '<div role="tabpanel" class="tab-pane network'+(!this.options.allowUploadVideo ? ' active' : ' ')+'" id="network">\n' +
				'	<div style="margin-top: 10px;">\n' +
				'		<form>\n' +
				'			<div class="form-group">\n' +
				'				<div style="margin: -10px 0 10px 0;">为了在微信中有更好的体验，推荐使用<a href="http://v.qq.com" target="_blank">腾讯视频</a></div>\n' +
				'				<input type="url" class="form-control" id="networkurl" placeholder="请输入网络视频地址">\n' +
				'				<div id="preview" style="position:relative;width:600px;height:300px;margin:0 auto;margin-top:15px;text-align:center;background:#ccc;">\n' +
				'				</div>\n' +
				'			</div>\n' +
				'		</form>\n' +
				'	</div>\n' +
				'	<div class="modal-footer" style="margin:0 -30px -30px;">\n' +
				'		<button type="button" class="btn btn-primary">确认</button>\n' +
				'		<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>\n' +
				'	</div>\n' +
				'</div>';

			dialog['localDialog'] = '<div role="tabpanel" class="tab-pane history" id="history_image">\n' +
				'	<div class="history-content" style="height:310px;overflow-y: auto;"></div>\n' +
				'	<nav id="image-list-pager" class="text-right we7-margin-vertical">\n' +
				'			<ul class="pager" style="margin: 0;"></ul>\n' +
				'	</nav>\n' +
				'	<div class="modal-footer" style="margin:0 -30px -30px;">\n' +
				'		<div style="float: right;">\n' +
				'		<button '+(this.options.multiple ? '' : 'style="display:none;"')+' type="button" class="btn btn-primary">确认</button>\n' +
				(this.options.multiple ? '<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>\n' : '') +
				'		</div>\n' +
				'	</div>\n' +
				'</div>';
			dialog['localDialogLi'] = '<ul class="img-list clearfix">\n' +
				'<%var items = _.sortBy(items, function(item) {return -item.id;});%>' +
				'<%_.each(items, function(item) {%> \n' +
				'<li class="img-item" attachid="<%=item.id%>" title="<%=item.filename%>">\n' +
				'	<div class="img-container" style="background-image: url(\'<%=item.url%>\');">\n' +
				'		<div class="select-status"><span></span></div>\n' +
				'	</div>\n' +
				'	<div class="btnClose" data-id="<%=item.id%>"><a href=""><i class="fa fa-times"></i></a></div>\n' +
				'</li>\n' +
				'<%});%>\n' +
				'</ul>';
			dialog['weixin_localDialogLi'] = '<ul class="img-list clearfix">\n' +
				'<%_.each(items, function(item) {%> \n' +
				'<li class="img-item" attachid="<%=item.id%>" data-type="image" title="<%=item.filename%>">\n' +
				'	<div class="btnClose delete-file"><a href="javascript:;"><i class="fa fa-times"></i></a></div>'+
				'	<div class="img-container" style="background-image: url(\'<%=item.url%>\');">\n' +
				'		<div class="select-status"><span></span></div>\n' +
				'	</div>\n' +
				'</li>\n' +
				'<%});%>\n' +
				'</ul>';	
			dialog['localAudioDialog'] = '<div role="tabpanel" class="tab-pane history" id="history_audio">\n' +
				'	<div style="height:310px; overflow-x:hidden; overflow-y: auto;">\n' +
				'		<table class="table table-hover we7-table">\n' +
				'		<thead class="navbar-inner">\n' +
				'			<tr>\n' +
				'				<th>标题</th>\n' +
				(this.options.isWechat ? 
				'				<th style="width:30%;text-align:right">创建时间</th>\n' +
				'				<th style="width:30%;text-align:right">\n' +
				'					<div class="input-group input-group-sm hide">\n' :
				'				<th style="width:20%;">创建时间</th>\n' +
				'				<th style="width:30%;">\n' +
				'					<div class="input-group input-group-sm">\n') +
				'						<input type="text" class="form-control">\n' +
				'						<span class="input-group-btn">\n' +
				'							<button class="btn btn-default" type="button"><i class="fa fa-search" style="font-size:12px; margin-top:0;"></i></button>\n' +
				'						</span>\n' +
				'					</div>\n' +
				'				</th>\n' +
				'			</tr>\n' +
				'		</thead>\n' +
				'		<tbody class="history-content">\n' +
				'		</tbody>\n' +
				'	</table></div>\n' +
				'	<nav id="image-list-pager" class="text-right we7-margin-vertical">\n' +
				'		<ul class="pager" style="margin: 0;"></ul>\n' +
				'	</nav>\n' +
				'	<div class="modal-footer" style="margin:0 -30px -30px;">\n' +
				'		<div style="float: right;">\n' +
				'		<button '+(this.options.multiple ? '' : 'style="display:none;"')+' type="button" class="btn btn-primary">确认</button>\n' +
				(this.options.multiple ? '<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>\n' : '') +
				'		</div>\n' +
				'	</div>\n' +
				'</div>';
			dialog['localAudioDialogLi'] =
				'<%var items = _.sortBy(items, function(item) {return -item.id;});%>' +
				'<%_.each(items, function(item) {%> \n' +
				'<tr>\n' +
				'	<td><a href="#" title="<%=item.filename%>"><%=item.filename%></a></td>\n' +
				'	<td class="text-right"><%=item.createtime%></td>\n' +
				'	<td class="text-right">\n' +
				'		<span class="input-group-btn">\n' +
				'			<button class="btn btn-default audio-player-play" type="button" attach="<%=item.url%>"><p style="margin:0px;" class="fa fa-play"></p></button>\n' +
				'			<button attachid="<%=item.id%>" class="btn btn-default js-btn-select">选取</button>\n' +
				'		</span>\n' +
				'	</td>\n' +
				'</tr>\n' +
				'<%});%>\n';
			dialog['weixin_localAudioDialogLi'] =
				'<%var items = _.sortBy(items, function(item) {return -item.id;});%>' +
					'<%_.each(items, function(item) {%> \n' +
					'<tr>\n' +
					'	<td><a href="<%=item.url%>" target="blank" title="<%=item.filename%>"><%=item.filename%></a></td>\n' +
					'	<td class="text-right"><%=item.createtime%></td>\n' +
					'	<td class="text-right">\n' +
					'		<span class="input-group-btn" attachid="<%=item.id%>" data-type="audio">\n' +
					'			<button class="btn btn-default audio-player-play" type="button" attach="<%=item.url%>"><p style="margin:0px;" class="fa fa-play"></p></button>\n' +
					'			<button class="btn btn-default delete-file">删除</button>\n' +
					'			<button attachid="<%=item.id%>" class="btn btn-default js-btn-select">选取</button>\n' +
					'		</span>\n' +
					'	</td>\n' +
					'</tr>\n' +
					'<%});%>\n';
			dialog['localVideoDialog'] = '<div role="tabpanel" class="tab-pane history" id="history_video">\n' +
				'	<div style="height:310px; overflow-x:hidden; overflow-y:auto;">\n' +
				'		<table class="table table-hover we7-table">\n' +
				'		<thead class="navbar-inner">\n' +
				'			<tr>\n' +
				'				<th>标题</th>\n' +
				'				<th style="width:30%;text-align:right">创建时间</th>\n' +
				'				<th style="width:30%;text-align:right">\n' +
				'					<div class="input-group input-group-sm hide">\n' +
				'						<input type="text" class="form-control">\n' +
				'						<span class="input-group-btn">\n' +
				'							<button class="btn btn-default" type="button"><i class="fa fa-search" style="font-size:12px; margin-top:0;"></i></button>\n' +
				'						</span>\n' +
				'					</div>\n' +
				'				</th>\n' +
				'			</tr>\n' +
				'		</thead>\n' +
				'		<tbody class="history-content">\n' +
				'		</tbody>\n' +
				'	</table></div>\n' +
				'	<nav id="image-list-pager" class="text-right we7-margin-vertical">\n' +
				'		<ul class="pager" style="margin: 0;"></ul>\n' +
				'	</nav>\n' +
				'	<div class="modal-footer" style="margin:0 -30px -30px;">\n' +
				'		<div style="float: right;">\n' +
				'		<button '+(this.options.multiple ? '' : 'style="display:none;"')+' type="button" class="btn btn-primary">确认</button>\n' +
				(this.options.multiple ? '<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>\n' : '') +
				'		</div>\n' +
				'	</div>\n' +
				'</div>';
			dialog['localVideoDialogLi'] =
				'<%var items = _.sortBy(items, function(item) {return -item.id;});%>' +
				'<%_.each(items, function(item) {%> \n' +
				'<tr>\n' +
				'	<td><a href="#" title="<%=item.filename%>"><%=item.filename%></a></td>\n' +
				'	<td class="text-right"><%=item.createtime%></td>\n' +
				'	<td class="text-right">\n' +
				'		<span class="input-group-btn">\n' +
				'			<button attachid="<%=item.id%>" class="btn btn-default js-btn-select">选取</button>\n' +
				'		</span>\n' +
				'	</td>\n' +
				'</tr>\n' +
				'<%});%>\n';
			dialog['weixin_localVideoDialogLi'] =
				'<%var items = _.sortBy(items, function(item) {return -item.id;});%>' +
					'<%_.each(items, function(item) {%> \n' +
					'<tr>\n' +
					'	<td><a href="<%=item.url%>" target="blank" title="<%=item.filename%>"><%=item.filename%></a></td>\n' +
					'	<td class="text-right"><%=item.createtime%></td>\n' +
					'	<td class="text-right">\n' +
					'		<span class="input-group-btn" attachid="<%=item.id%>" data-type="audio">\n' +
					'			<button class="btn btn-default delete-file">删除</button>\n' +
					'			<button attachid="<%=item.id%>" class="btn btn-default js-btn-select">选取</button>\n' +
					'		</span>\n' +
					'	</td>\n' +
					'</tr>\n' +
					'<%});%>\n';

			return dialog;
		}
	};
	return uploader;
});