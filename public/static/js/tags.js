(function($){
$.fn.extend({tags: function(params){
	return this.each(function(){
		var $this = $(this);
		var _config = {
			language: "zh-CN",
			ajax: {
				url: LP.baseuri +'tag/data',
				dataType: 'json',
				type: 'post',
				delay: 250,
				data: function (_params) {
					var v = {page: _params.page, _token: $.crsf, filters: {}, of: 'json'};
					v.filters.keywords = {'like': _params.term};
					v = $.extend(true, v, params);
					return v;
				},
				processResults: function (json, page) {
					if (json.result != 'success' && json.result != 'api') return {result: []};
					var data = [], items = json.data.data;
					for(var i = 0; i < items.length; ++i)
						data.push({'id': items[i].keywords, 'text': items[i].keywords + ' <span class="text-muted">('+ (items[i].count || 0) + '次使用)</span>', 'selection': items[i].keywords });
					return {results: data};
				},
				cache: true
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
		$this.select2($.extend({tags: true}, _config));
	});
}});
})(jQuery);