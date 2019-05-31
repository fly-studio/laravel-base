(function($){
$().ready(function(){
	var $dt = $('#datatable');
	var datatable = $dt.dataTable().api();

	var makeLinks = function(size, total, namespace, name, format) {
		if (isNaN(size) || size <= 0) return;
		var pages = Math.ceil(total / size);
		var urlQuery = $dt.data('url-query');
		var $links = $('#export-links').empty();
		$('#export-size').text(size);
		for (var i = 1; i <= pages; i++) {
			var $o = $('<div class="col-md-3 col-xs-4"><a href="'+LP.baseuri+namespace+'/'+name+'/export" class="btn btn-link" target="_blank" data-append-queries="true">第'+i+'个</a> ('+ (size * (i - 1) + 1) + '-' + (i == pages ? total : size * i)  +')</div>').appendTo($links);
			$('a', $o).querystring($.extend(true, {}, urlQuery, {of: format, page: i, size: size}));
		}
	};

	$('#export-slider').slider({
		min: 0,
		max: 0,
		value: 0,
		tooltip: 'show',
		selection: 'before',
		orientation: 'horizontal'
	});

	$('body').on('click', '[data-export]', function(e){
		var $this = $(e.target);
		var format = $this.data('export');
		var namespace = $this.data('namespace');
		var name = $this.data('name');
		var total = datatable.page.info().recordsTotal;
		var count = datatable.page.info().recordsDisplay;
		var size = $('#export-modal').data('size');
		if (!size || size > count) size = count;
		$('#export-format').text(format);
		$('#export-total').text(total);
		$('#export-count').text(count);
		//$('#export-size').text(size);

		$('#export-modal').modal({show: true, backdrop: 'static'});

		$('#export-slider').slider('setAttribute', 'max', size).slider('setValue', size).off('slideStop').on('slideStop', function(e){
			var _size = parseInt($(this).data('slider').getValue());
			makeLinks(_size, count, namespace, name, format);
		});
		//init
		makeLinks(size, count, namespace, name, format);
	});
});
})(jQuery);
