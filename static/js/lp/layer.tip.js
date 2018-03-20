window.TIPS_LANGUAGE = {
	'tip' : '\u63d0\u793a', //提示
	'ok' : '\u786e\u5b9a', //确定
	'cancel' : '\u53d6\u6d88', //取消
	'back' : '\u8fd4\u56de' //返回
};
(function(){

	LP.tip.diy_interface = function(message, result, tipType, extraConfig) {
		var _config = {
			content: '<div style="word-break:break-all;word-wrap:break-word;text-align:left;">' + message.content + '</div>',
			title: typeof message.title != 'undefined' ? message.title : TIPS_LANGUAGE.tip,
			time: tipType.timeout,
			btn: tipType.type == 'back' ? [TIPS_LANGUAGE.back] : false,
			yes: function (index, layero) {
				layer.close(index);
			}
		};
		layer.open(extend(_config, extraConfig));
	};

	LP.tip.alert_interface = function(message) {
		return new Promise(function(resolve, reject) {
			layer.alert(message.content, function(index){
				layer.close(index);
				resolve();
			});
		});
	};

	LP.tip.toast_interface = function(message, timeout) {
		return new Promise(function(resolve, reject) {
			layer.msg(message.content, {
				time: timeout
			}, function(){
				resolve();
			});
		});
	};

	LP.tip.confirm_interface = function(message) {
		return new Promise(function(resolve, reject) {
			layer.confirm(message.content, function(index){
				layer.close(index);
				resolve();
			}, function(index) {
				layer.close(index);
				reject();
			});
		});
	};

	LP.tip.prompt_interface = function(message) {
		return new Promise(function(resolve, reject) {
			layer.prompt({
				formType: 2,
				value: '',
				title: message.content
			}, function(value, index, elem){
				layer.close(index);
				resolve(value);
			}, function(){
				layer.close(index);
				reject();
			});
		});
	};
})();
