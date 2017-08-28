(function($){
	var method = {cacheData: {}};
	method.getData = function(url, params) {
		var key = JSON.stringify({url: url, params: params});
		var $dfd = jQuery.Deferred();

		if (typeof method.cacheData[key] != 'undefined')
			$dfd.resolve(method.cacheData[key]);
		else {
			LP.post(url, $.extend(true, {}, params, {of: 'json'})).done(function(json, textStatus, jqXHR){
				var items = json.data.data;
				method.cacheData[key] = items;
				$dfd.resolve(method.cacheData[key]);
			}).fail(function(){
				var args = arguments;
				$dfd.reject.apply($dfd, args);
			});
		}
		return $dfd.promise();
	};
	method.replaceData = function(data, format)
	{
		return format.replace(/\{\{([\w\.]+)\}\}/g, function($0, $1){
			var o = $1.split('.');
			var d = data;
			for(var i = 0; i < o.length; ++i)
			{
				var n = o[i];
				if (typeof d[ n ] != 'undefined')
					d = d[n];
				else 
					return null;
			}
			return d;
		});
	};
	method.format = function(items, id, selection, text, pid) {
		var result = [];
		for(var i = 0; i < items.length; ++i) {
			var d = {
				'id': id ? method.replaceData(items[i], id) : items[i].id, 
				'text': text ? method.replaceData(items[i], text) : items[i].text,
				'selection': selection ? method.replaceData(items[i], selection) : items[i].selection,
				'pid': pid ? method.replaceData(items[i], pid) : items[i].pid
			};
			if (typeof items[i].children == 'object' && typeof items[i].children.length != 'undefined')
				d.children = method.format(items[i].children, id, selection, text, pid);
			result.push(d);
		}
		return result;
	};
	method.recursive = function(items, prefix) {
		prefix = typeof prefix == 'undefined' ? '' : prefix;
		var result = [];
		for(var i = 0; i < items.length; ++i) {
			var v = items[i];
			var _class = 'treeline ';
			if (i === 0 && !prefix) //首节点
				_class += 'top-';
			else if (i == items.length - 1) //尾节点
				_class += 'bottom-';
			else
				_class += 'center-';
			_class += v.children ? 'open' : 'line';

			var d = {
				'id': v.id,
				'selection': v.selection,
				'text': prefix + '<span class="'+_class+'"></span>' + v.text
			};
			result.push(d);
			if (v.children)
			{
				var ds = method.recursive(v.children, prefix + (i == items.length - 1 ? '<span class="treeline blank"></span>' : '<span class="treeline blank-line"></span>'));
				for(var j = 0; j < ds.length; ++j)
					result.push(ds[j]);
			}
		}
		return result;
	};
	$.fn.extend({
		selectModel: function(options){
			if (options == 'destroy') return this.select2('destroy');
			if (options == 'open') return this.select2('open');
			if (options == 'close') return this.select2('close');
			return this.each(function(){
				var $this = $(this);
				var id = $this.data('id');
				var text = $this.data('text');
				var selection = $this.data('selection') ? $this.data('selection') : text;
				var params = $this.data('params');
				var values = $this.attr('value') ? $this.attr('value').split(',') : $this.val();
				var url = LP.baseuri + $this.data('model')+'/data';
				params = $.extend(true, {}, {all: 'true'}, params);
				method.getData(url, params).done(function(json){
					var data = method.format(json, id, selection, text); 
					$this.select2($.extend(true, {}, {language: "zh-CN", data: data, allowClear: true}, options));
					//初始值
					$this.val(values ? values : null).trigger("change");
				});
			});
		},
		treeModel: function(options){
			if (options == 'destroy') return this.select2('destroy');
			if (options == 'open') return this.select2('open');
			if (options == 'close') return this.select2('close');
			return this.each(function(){
				var $this = $(this);
				var id = $this.data('id');
				var pid = $this.data('pid');
				var text = $this.data('text');
				var selection = $this.data('selection') ? $this.data('selection') : text;
				var params = $this.data('params');
				var values = $this.attr('value') ? $this.attr('value').split(',') : $this.val();
				var url = LP.baseuri + $this.data('model')+'/data';
				params = $.extend(true, {}, {all: 'true', tree: 'true'}, params);

				method.getData(url, params).done(function(json){
					var data = method.format(json, id, selection, text, pid);
					data = method.recursive(data);
					$this.select2($.extend(true, {}, {
						//theme: "bootstrap",
						language: "zh-CN",data: data, allowClear: true,
						templateResult: function(data){return $('<div>' + data.text + '</div>');},
						templateSelection: function(data){return data.selection;}
					}, options));
					//初始值
					$this.val(values ? values : null).trigger("change");
				});
			});
		},
		suggestModel: function(options){
			if (options == 'destroy') return this.select2('destroy');
			if (options == 'open') return this.select2('open');
			if (options == 'close') return this.select2('close');
			return this.each(function(){
				var $this = $(this);
				var id = $this.data('id');
				var text = $this.data('text');
				var selection = $this.data('selection') ? $this.data('selection') : text;
				var term = $this.data('term') ? $this.data('term') : null;
				var q = $this.data('q') ? $this.data('q') : null;
				var values = $this.attr('value') ? $this.attr('value').split(',') : $this.val();
				var url = LP.baseuri + $this.data('model') + '/data';
				var _config = {
					language: "zh-CN",
					ajax: {
						delay: 500,
						//dataType: 'json',
						//type: 'post',
						//cache: true,
						transport: function (_params, success, failure) {
							var params = $this.data('params');
							var config = {page: _params.data.page, f: {}, q: {}};
							if(term) config.f[term] = {'like': _params.data.term};
							if(q) config.q[q] = _params.data.q;
							method.getData(url, $.extend(true, {}, config, params)).done(function(json){
								var data = method.format(json, id, selection, text);
								success(data);
							});
						},
						processResults: function (json, page) {
							return {results: json};
						}
					},
					escapeMarkup: function (markup) {return markup;},
					minimumInputLength: 2,
					allowClear: true,
					templateResult: function(data){return data.text;},
					templateSelection: function(data){return data.selection || data.text;}
				};
				//有初始的值
				if (values) {
					var params = $this.data('params');
					method.getData(url, $.extend(true, {}, params, {f: {id: {in: values}}})).done(function(json){
						var data = method.format(json, id, selection, text);
						$this.select2($.extend(true, {}, _config, options, {data: data}));

					});
				} else
					$this.select2($.extend(true, {}, _config, options));
			});
		},
		tagsModel: function(options){
			if (options == 'destroy') return this.select2('destroy');
			if (options == 'open') return this.select2('open');
			if (options == 'close') return this.select2('close');
			return this.each(function(){
				var $this = $(this);
				var term = $this.data('term') ? $this.data('term') : null;
				var q = $this.data('q') ? $this.data('q') : null;
				if (!term && !q) term = 'keywords';
				var id = $this.data('id') ? $this.data('id') : '{{keywords}}';
				var text = $this.data('text') ? $this.data('text') : '{{keywords}} <span class="text-muted">({{count}}次使用)</span>';
				var selection = $this.data('selection') ? $this.data('selection') : '{{keywords}}';
				var values = $this.attr('value') ? $this.attr('value').split(',') : $this.val();
				var url = LP.baseuri + ($this.data('model') ? $this.data('model') : 'admin/tag') + '/data';
				var _config = {
					language: "zh-CN",
					ajax: {
						delay: 500,
						//dataType: 'json',
						//type: 'post',
						//cache: true,
						transport: function (_params, success, failure) {
							var params = $this.data('params');
							var config = {page: _params.data.page, f: {}, q: {}};
							if(term) config.f[term] = {'like': _params.data.term};
							if(q) config.q[q] = _params.data.q;
							method.getData(url, $.extend(true, {}, config, params)).done(function(json){
								var data = method.format(json, id, selection, text);
								success(data);
							});
						},
						processResults: function (json, page) {
							return {results: json};
						}
					},
					escapeMarkup: function (markup) {return markup;},
					minimumInputLength: 1,
					templateResult: function(data){return data.text;},
					templateSelection: function(data){return data.selection || data.text;},
					createTag: function (_params) {
						var term = $.trim(_params.term);
						if (term === '') return null;
						return {
							id: term,
							selection: term,
							text: term + ' <span style="color:#f5f5f5">(创建)</span>',
							newTag: true // add additional parameters
						};
					}
				};
				if (values) {
					var params = $this.data('params');
					var config = {f: {id: {in: values}}, q: {}};
					method.getData(url, $.extend(true, {}, config, params)).done(function(json){
						var data = method.format(json, id, selection, text);
						$this.select2($.extend(true, {}, _config, options, {data: data}));
					});
				} else
					$this.select2($.extend(true, {}, _config, options));
			});
		}
	});

$().ready(function(){
	$('.select-model').selectModel();
	$('.tree-model').treeModel();
	$('.suggest-model').suggestModel();
	$('.tags-model').tagsModel();
});
})(jQuery);