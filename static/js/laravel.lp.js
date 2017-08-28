window.LP = {};
window.QUERY_LANGUAGE = {
	'error' : '错误',
	'reload' : '重新载入',
	'redirect' : '页面跳转',
	'unselected' : '请至少选择一项！',
	'network_timeout' : '网络故障，请检查网络连接后重试！',
	'parser_error' : '数据解析失败，刷新重试下？',
	'server_error' : '服务器可能出现了点问题，刷新页面重试下？',
	'encrypt_key' : '数据已经加密，但未传递正确的公钥。',
	'encrypt_js' : '数据已经加密，但页面未加载解密JS。',
	'encrypt_string' : '数据已经加密，但密文解密失败，请联系管理员。',
	'encrypt_unserialize' : '数据已经加密，但解密后反序列化失败，请联系管理员。',
};
(function(){
	//base uri
	var thiscript = document.currentScript;
	if (!thiscript) {
		var scripts = document.getElementsByTagName("script");
		thiscript = scripts[ scripts.length - 1 ];
	}
	window.baseuri = thiscript.src.toString().match(/[^\/:](\/.*)static\/js\/laravel\.lp(\.min)?\.js/i) ? thiscript.src.toString().match(/[^\/:](\/.*)static\/js\/laravel\.lp(\.min)?\.js/i)[1] : thiscript.src.toString().replace(/\\/g, '/').replace(/\/[^\/]*\/?$/, '') + '/';
	if (!window.baseuri) window.baseuri = '/';
	LP.baseuri = window.baseuri;
})();
(function(){
	//ssl
	if (typeof JSEncrypt != 'undefined' && window.sessionStorage) {
		var getRSAKey = function() {
			var rsa = window.sessionStorage.getItem('l+rsa');
			if (rsa) rsa = JSON.parse(rsa);
			if (!rsa)
			{
				var crypt = new JSEncrypt({default_key_size:1024});
				var key = crypt.getKey();

				rsa = {
					private: key.getPrivateKey(),
					public: key.getPublicKey(),
				};
				window.sessionStorage.setItem('l+rsa',JSON.stringify(rsa));
			}
			return rsa;
		};
		var sslFunc = function()
		{
			this.rsa = getRSAKey();
			this.encrypt = function(text) {
				var crypt = new JSEncrypt();
				crypt.setKey(this.rsa.public);
				return crypt.encrypt(text);
			};
			this.decrypt = function(text) {
				var crypt = new JSEncrypt();
				crypt.setKey(this.rsa.private);
				return crypt.decrypt(text);
			};
		};
		LP.ssl = new sslFunc();
	}
})(jQuery);

(function($){

	LP.csrf = $('meta[name="csrf-token"]').attr('content');
	//init csrf
	var headers = {};
	if (LP.csrf)
		headers['X-CSRF-TOKEN'] = LP.csrf;
	if (LP.ssl)
		headers['X-RSA'] = encodeURIComponent(LP.ssl.rsa.public);

	//init ssl
	$.ajaxSetup({headers: headers, dataFilter: function(data, type){
		var callback = ''; 
		var jsonError = function(content) {
			var data = {};
			data.result = 'error';
			data.message = {title: QUERY_LANGUAGE.error, content: content};
			return JSON.stringify(data);
		};
		//if (type.toLowerCase() == 'jsonp') //unsupport jsonp
		//	callback = '';
		if (type.toLowerCase() == 'json') {
			var json = $.parseJSON(data);
			if (typeof json != 'undefined' && typeof json.result != 'undefined' && json.result == 'api' && typeof json.encrypted == 'string')
			{
				var key,encrypted_json,encrypted;
				if (typeof LP.ssl != 'undefined') {
				 	try{
						key = LP.ssl.decrypt(json.encrypted);
					} catch (e) {
						console.log(e.stack);
						return jsonError(QUERY_LANGUAGE.encrypt_key + e.message);
					}
					encrypted = json.data;
					try{
						var s = base64js.toByteArray(encrypted);
						encrypted_json = JSON.parse(aesjs.util.convertBytesToString(s)); //json_decode()
					} catch (e) {
						console.log(e.stack);
						return jsonError(QUERY_LANGUAGE.encrypt_string + e.message);
					}
					try{
						//base64 decode
						var keyBytes = base64js.toByteArray(key),
						ivBytes = base64js.toByteArray(encrypted_json.iv),
						valueBytes = base64js.toByteArray(encrypted_json.value);
						//aes cbc
						var aesCbc = new aesjs.ModeOfOperation.cbc(keyBytes, ivBytes);
						var decryptedBytes = aesCbc.decrypt(valueBytes);
						var decypted = aesjs.util.convertBytesToString(decryptedBytes);
						//unserialize
						json.data = unserialize(decypted);
						delete json.key;
					} catch(e) {
						console.log(e.stack);
						return jsonError(QUERY_LANGUAGE.encrypt_unserialize + e.message);
					}

				} else if (!json.key)
					return jsonError(QUERY_LANGUAGE.encrypt_key);
				else 
					return jsonError(QUERY_LANGUAGE.encrypt_js);
			}
			delete json.encrypted;
			data = JSON.stringify(json);
			if (typeof json.debug != 'undefined' && !!json.debug) console.log(json);
			//delete json;
		}

		return data;
	}});

	LP.query = function(method, url, data){
		var _this = this, _headers = {}, _data = data;
		var $dfd = jQuery.Deferred();
		if (data && data instanceof String) _data = $.deparam(data);
		//使用POST模拟的PUT或者DELETE等
		if (_data && _data._method) {
			method = _data._method;
			_headers['X-HTTP-Method-Override'] = method;
		}
		if (_data && _data._token) //add csrf
			_headers['X-CSRF-TOKEN'] = _data._token;

		$.ajax({
			url : url,
			data : _data ? _data : null,
			async : true, 
			cache : false,
			type : method.toUpperCase(),
			headers: _headers,
			timeout : 20000,
			dataType : /[\?&](jsonp|callback)=\?/i.test(url) ? 'jsonp' : 'json',
			success : function(json, textStatus, jqXHR) {
				var args = arguments;
				if (typeof json != 'undefined' && typeof json.result != 'undefined' && (json.result == 'success' || json.result == 'api'))
					$dfd.resolve.apply(_this, args);
				else
					$dfd.reject.apply(_this, args);
			},
			error : function(XMLHttpRequest, textStatus, errorThrown) {
				var args = arguments;
				$dfd.reject.apply(_this, args);
			}
		});
		return $dfd.promise();
	};

	

	$.each(['get', 'post', 'head', 'patch', 'put', 'delete'], function(index, method) {
		LP[method] = LP[method.toUpperCase()] = function(url, data, callback) {
			var _this = this;
			var $dtd = LP.query.call(_this, method, url, data);
			if (callback && $.isFunction(callback)) $dtd.done(function(){
				var args = arguments;
				callback.apply(_this, args);
			});
			return $dtd;
		};
	});

	LP.tip = function(result, message, tipType, tipConfig) {
		if (typeof message == 'undefined' || typeof tipType != 'object') return;
		else if (typeof message == 'string') message = {content: message};

		if (typeof $.lptip_interface != 'undefined') {
			$.lptip_interface(result, message, tipType, tipConfig);
		} else
			alert(message.content.noHTML());

		switch(tipType.type)
		{
			case 'redirect':
				setTimeout(function() {
					self.location.href = tipType.url;
				}, tipType.timeout);
				break;
			case 'refresh':
				setTimeout(function() {
					self.location.reload();
				}, tipType.timeout);
				break;
			case 'back':
			case 'toast':
				break;
		}
	};

	LP.queryTip = function(method, url, data, tipConfig)
	{
		tipConfig = typeof tipConfig == 'undefined' ? {} : tipConfig;
		var _this = this;
		var $dtd = LP.query.call(_this, method, url, data);
		if (tipConfig !== false)
			$dtd.done(function(json){
				LP.tip.call(_this, json.result, json.message, json.tipType, tipConfig);
			}).fail(function(XMLHttpRequest, textStatus){
				if (typeof XMLHttpRequest == 'object' && typeof XMLHttpRequest.result != 'undefined')
				{
					var json = XMLHttpRequest;
					LP.tip.call(_this, json.result, json.message, json.tipType, tipConfig);
				}
				else
					switch(textStatus) {
						case 'timeout':
							$.toast(QUERY_LANGUAGE.network_timeout);
							break;
						case 'error':
							break;
						case 'notmodified':
							break;
						case 'parsererror':
							$.toast(QUERY_LANGUAGE.parser_error);
							break;
						default:
							$.toast(QUERY_LANGUAGE.server_error);
							break;
					}
			});
		return $dtd;
	};

	$.fn.extend({
	query : function(callback, tipConfig) {
		return this.each(function() {
			var $this = $(this);
			var is_form = $this.is('form');
			var on = $this.data('lp-query');
			if (on)
				$this.off(is_form ? 'submit': 'click', on);
			if (callback == 'destroy') return;
			//bind
			var validator = is_form ? $this.data('validator') : null;
			if (validator) validator.settings.submitHandler = function(f, e) {};
			on = function(e) {
				var selector = $this.attr('selector');
				if ($this.is('.disabled,[disabled]')) return false;
				var $selector = is_form ? $this.add(selector) : $(selector);
				if (validator && !$.isEmptyObject(validator.invalid)) //validator is invalid
					return false;
				
				if((selector || is_form) && $selector.serializeArray().length <= 0) //selector is set,but nothing to query
				{
					$.toast(QUERY_LANGUAGE.unselected);
					return false;
				}
				
				var url = $this.attr(is_form ? 'action' : 'href');
				var method = $this.attr('method');
				var msg = $this.attr('confirm');
				var query = function(){
					var $doms = is_form ? $(':submit,:image', $this)/*.add($this)*/ : $this;
					$doms = $doms.filter(':not(.disabled,[disabled])');
					$doms.prop('disabled',true).attr('disabled','disabled').each(function(){
						var $t = $(this);var o = $t.offset();
						$('<div style="position:absolute;left:'+(o.left + $t.width())+'px;top:'+(o.top - 16) +'px;height:16px;width:16px;display:block;z-index:99999" class="query-loading"><img src="data:image/gif;base64,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"</div>').appendTo('body');
					}); //disabled the submit button

					return LP.queryTip.call($this, method, url, $selector.serializeArray(), tipConfig).done(function(json){
						if (typeof callback != 'undefined' && $.isFunction(callback))
							callback.call($this, json);
					}).always(function(){
						$('.query-loading').remove();
						$doms.prop('disabled',false).removeAttr('disabled');
					});
				};
				if (msg) {
					msg = msg.replace('%L', $selector.serializeArray().length);
					$.confirm(msg, query);
				} else
					query.call(this);
				e.stopImmediatePropagation();
				return false;
			};
			$this.on(is_form ? 'submit': 'click', on).data({'lp-query': on});
		});
	}
	});

	$.baseuri = LP.baseuri;
	$.LP = LP;

})(jQuery);