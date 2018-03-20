window.TIPS_LANGUAGE = {
	'tip' : '\u63d0\u793a', //提示
	'ok' : '\u786e\u5b9a', //确定
	'cancel' : '\u53d6\u6d88', //取消
	'back' : '\u8fd4\u56de' //返回
};
(function($){
	$.noty.defaults = {
		layout: 'center',
		theme: 'defaultTheme',
		type: 'alert',
		text: '',
		dismissQueue: true, // If you want to use queue feature set this true
		template: '<div class="noty_message"><span class="noty_text"></span><div class="noty_close"></div></div>',
		animation: {
			open: {height: 'toggle'},
			close: {height: 'toggle'},
			easing: 'swing',
			speed: /(Android|webOS|iPhone|Windows\sPhone|iPod|BlackBerry|SymbianOS)/i.test(navigator.userAgent) ? 0 : 500 // opening & closing animation speed
		},
		timeout: 2000, // delay for closing event. Set false for sticky notifications
		force: false, // adds notification to the beginning of queue when set to true
		modal: true,
		maxVisible: 15, // you can set max visible notification for dismissQueue true option
		closeWith: ['click'], // ['click', 'button', 'hover']
		callback: {
			onShow: function() {},
			afterShow: function() {},
			onClose: function() {},
			afterClose: function() {}
		},
		buttons: false // an array of buttons
	};
	var tips_exchange = {
		'success': 'success',
		'failure': 'warning',
		'warning': 'warning',
		'error': 'error',
		'notice': 'alert',
		'information': 'information'
	};
	LP.tip.diy_interface = function(message, result, tipType, extraConfig)
	{
		var setting = {
			text : '<div style="text-align:left;"><h4>' + (typeof message.title != 'undefined' ? message.title : TIPS_LANGUAGE.tip)  + '</h4><div style="word-break:break-all;word-wrap:break-word;">'+ (typeof message.content != 'undefined' ? message.content : TIPS_LANGUAGE.tip) +'</div></div>',
			type : tips_exchange[result] ? tips_exchange[result] : 'alert',
			timeout : tipType.timeout,
			buttons : tipType.type == 'back' ? [
			{
				addClass: 'btn btn-warning',
				text: TIPS_LANGUAGE.back,
				onClick: function($noty) {
					$noty.close();
				}
			}
			] : false
		};
		if (typeof extraConfig == 'object')
			setting = $.extend(true, setting, extraConfig);

		var $noty = noty(setting);
		$('button:eq(0)',$noty.$buttons).focus();
	};

	LP.tip.alert_interface = function(message) {
		return new Promise(function(resolve, reject) {
			var setting = {
				text : '<div style="text-align:left;"><h4>' + (typeof message.title != 'undefined' ? message.title : TIPS_LANGUAGE.tip) + '</h4><div style="word-break:break-all;word-wrap:break-word;text-align:left;">'+ (typeof message.content != 'undefined' ? message.content : TIPS_LANGUAGE.tip) +'</div></div>',
				type : 'success',
				timeout : false ,
				buttons : [
					{
						addClass: 'btn btn-primary',
						text: TIPS_LANGUAGE.ok,
						onClick: function($noty) {
							$noty.close();
							resolve();
						}
					}
				]
			};
			var $noty = noty(setting);
			$('button:eq(0)',$noty.$buttons).focus();
		});

	};

	LP.tip.toast_interface = function(message, timeout)
	{
		return new Promise(function(resolve, reject) {
			var setting = {
				text : '<div style="text-align:left;"><h4>' + (typeof message.title != 'undefined' ? message.title : TIPS_LANGUAGE.tip) + '</h4><div style="word-break:break-all;word-wrap:break-word;">'+ (typeof message.content != 'undefined' ? message.content : TIPS_LANGUAGE.tip) +'</div></div>',
				type : 'warning',
				timeout : !isNaN(timeout) && timeout > 0 ? timeout : 1500,
				callback: {
					onClose: function() {
						resolve();
					}
				}
			};
			noty(setting);
		});
	};

	LP.tip.confirm_interface = function(message) {
		return new Promise(function(resolve, reject) {
			var setting = {
				text : '<div style="text-align:left;"><h4>' + (typeof message.title != 'undefined' ? message.title : TIPS_LANGUAGE.tip) + '</h4><div style="word-break:break-all;word-wrap:break-word;">'+ (typeof message.content != 'undefined' ? message.content : TIPS_LANGUAGE.tip) +'</div></div>',
				type : 'warning',
				timeout :  false ,
				buttons : [
					{
						addClass: 'btn btn-primary',text: TIPS_LANGUAGE.ok,
						onClick: function($noty) {
							$noty.close();
							resolve();
						}
					},{
						addClass: 'btn btn-danger',text: TIPS_LANGUAGE.cancel,
						onClick: function($noty) {
							$noty.close();
							reject();
						}
					}
				]
			};
			var $noty = noty(setting);
			$('button:eq(1)',$noty.$buttons).focus();
		});
	};

	LP.tip.prompt_interface = function(message) {
		return new Promise(function(resolve, reject) {
			var setting = {
				text : '<div style="text-align:left;"><h4>' + (typeof message.title != 'undefined' ? message.title : TIPS_LANGUAGE.tip) + '</h4><label style="word-break:break-all;word-wrap:break-word;">'+ (typeof message.content != 'undefined' ? message.content : TIPS_LANGUAGE.tip) +'<input type="text" class="form-control" name="prompt" placeholder="" autofocus="autofocus"></label></div>',
				type : 'alert',
				timeout :  false ,
				buttons : [
					{
						addClass: 'btn btn-primary',text: TIPS_LANGUAGE.ok,
						onClick: function($noty) {
							$noty.close();
							var v = $('[name="prompt"]',$noty.$bar).val();
							resolve(v);
						}
					},{
						addClass: 'btn btn-danger',text: TIPS_LANGUAGE.cancel,
						onClick: function($noty) {
							$noty.close();
							reject();
						}
					}
				]
			};
			var $noty = noty(setting);
			$('[name="prompt"]',$noty.$bar).focus().on('keypress', function(e){
				if (e.keyCode==13)
				{
					$noty.close();
					var v = $(this).val();
					resolve(v);
				}
			});
		});
	};
})(jQuery);
