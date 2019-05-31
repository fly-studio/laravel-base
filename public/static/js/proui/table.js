(function($){
let querystring = function(param) {
	var params = jQuery.deparam.querystring();
	return params[param] ? params[param] : null;
};
$().ready(function(){
	var method = {config: {}};
	var $dt = $('#datatable');

	method.bindMethods = function(obj){

		$('a[method]:not([method="delete"])', obj).query();
		$('a[method="delete"]', obj).query(json =>{
			if (method.datatable)
				method.datatable.ajax.reload(null, false);

		}, true);
		if ($(obj).is('tr'))
		{
			$(obj).on('click', function(e) {
				var $target = $(e.target);
				if ($target.parentsUntil('tr').add($target).filter('a,:input,button').length > 0) //exists a input button
					return;

				var $this = $(this);
				var active = !!$this.data('active');
				$this.data('active', !active);
				$this.triggerHandler('active');
			}).on('active', function(e){
				var active = !!$(this).data('active');
				$('td:eq(0) :checkbox', $(obj)).prop('checked', active);
			});
		}

	};

	method.getConfig = function()
	{
		var configs = ['name', 'namespace', 'queryParams', 'displayStart', 'pageLength'];
		var r = {};
		for (var i = 0; i < configs.length; i++)
			r[ configs[i] ] = $dt.data( configs[i] ) || null;
		var config = $.bbq.getState();
		if (config)
			r = $.extend(true, {},  r, config);
		r.displayStart = ~~r.displayStart;
		r.pageLength = ~~r.pageLength;
		$dt.data(r); //read from hash and set to table's data

		return r;
	};

	method.setConfig = function(settings){
		var config = {
			displayStart: settings._iDisplayStart,
			pageLength: settings._iDisplayLength,
			search: {search: settings.oPreviousSearch.sSearch},
			order: []
		};
		settings.aLastSort.forEach(function(v){
			config.order.push([v.col, v.dir]);
		});
		//$dt.data(config); //已经写到hash中了，没必要设置table's data
		$.bbq.pushState(config);
		return true;
	};

	method.getColumns = function(removeIt)
	{
		var r = [];
		$('tbody td,tbody th', $dt).each(function(i, v){
			var $t = $(this);
			//recover < > &
			var render = template.compile(($t.html() ? $t.html() : '').toString().replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&#0*39;/g, "'"));

			var c = {
				data: $t.data('from') || null,
				orderable: $t.data('orderable') !== false,
				cellType: this.tagName.toLowerCase(),
				className: this.className,
				contentPadding: $t.data('contentPadding') || null,
				defaultContent: $t.data('defaultContent') || null,
				name: this.name || null,
				orderData: $t.data('orderData'),
				orderDataType: $t.data('orderDataType') || null,
				orderSequence: $t.data('orderSequence') || [ 'asc', 'desc' ],
				render: function(data, type, full) {
					var d = {data: data, type: type, full: full};
					return render(d);
				},
				searchable: $t.data('searchable') !== false,
				//type: $t.data('type') || null,
				visible: $t.data('visible') !== false,
				//width: $t.data('width') || null
			};
			r.push(c);
		});
		if (removeIt === true) $('tbody', $dt).empty();
		return r;
	};

	$.fn.dataTable.ext.renderer.pageButton.bootstrap = function ( settings, host, idx, buttons, page, pages ) {
		var api     = new $.fn.dataTable.Api( settings );
		var classes = settings.oClasses;
		var lang    = settings.oLanguage.oPaginate;
		var aria = settings.oLanguage.oAria.paginate || {};
		var btnDisplay, btnClass, counter=0;

		var attach = function( container, buttons ) {
			var i, ien, node, button;
			var clickHandler = function ( e ) {
				e.preventDefault();
				if ( !$(e.currentTarget).hasClass('disabled') && api.page() != e.data.action ) {
					api.page( e.data.action ).draw( 'page' );
				}
			};

			for ( i=0, ien=buttons.length ; i<ien ; i++ ) {
				button = buttons[i];

				if ( $.isArray( button ) ) {
					attach( container, button );
				}
				else {
					btnDisplay = '';
					btnClass = '';

					switch ( button ) {
						case 'ellipsis':
							btnDisplay = '&#x2026;';
							btnClass = 'disabled';
							break;

						case 'first':
							btnDisplay = lang.sFirst;
							btnClass = button + (page > 0 ?
								'' : ' disabled');
							break;

						case 'previous':
							btnDisplay = lang.sPrevious;
							btnClass = button + (page > 0 ?
								'' : ' disabled');
							break;

						case 'next':
							btnDisplay = lang.sNext;
							btnClass = button + (page < pages-1 ?
								'' : ' disabled');
							break;

						case 'last':
							btnDisplay = lang.sLast;
							btnClass = button + (page < pages-1 ?
								'' : ' disabled');
							break;

						default:
							btnDisplay = button + 1;
							btnClass = page === button ?
								'active' : '';
							break;
					}

					if ( btnDisplay ) {
						node = $('<li>', {
								'class': classes.sPageButton+' '+btnClass,
								'id': idx === 0 && typeof button === 'string' ?
									settings.sTableId +'_'+ button :
									null
							} )
							.append( $('<a>', {
									'href': '#',
									'aria-controls': settings.sTableId,
									'aria-label': aria[ button ],
									'data-dt-idx': counter,
									'tabindex': settings.iTabIndex
								} )
								.html( btnDisplay )
							)
							.appendTo( container );

						settings.oApi._fnBindAction(
							node, {action: button}, clickHandler
						);

						counter++;
					}
				}
			}
		};

		// IE9 throws an 'unknown error' if document.activeElement is used
		// inside an iframe or frame.
		var activeEl;

		try {
			// Because this approach is destroying and recreating the paging
			// elements, focus is lost on the select button which is bad for
			// accessibility. So we want to restore focus once the draw has
			// completed
			activeEl = $(host).find(document.activeElement).data('dt-idx');
		}
		catch (e) {}

		attach(
			$('ul', $(host).empty().html('<div class="col-sm-10 col-xs-12"><ul class="pagination pull-right"/></div>')),
			buttons
		);
		/*添加跳页功能*/
		var inputPageJump = $('<input>', {
			class: 'form-control',
			type: "number",
			min: 1,
			max: pages
		}).val(page+1).on("keyup", function(event){
			if (event.keyCode == 13) {
				var curr = this.value.replace(/\s|\D/g, '') | 0;
				if (curr) {
					var pages = api.page.info().pages;
					curr = curr > pages ? pages : curr;
					curr--;
					api.page(curr).draw(false);
				}
			}
		});
		var btnPageJump = $('<span />', {
			'class': "input-group-addon",
			'aria-controls': settings.sTableId,
			'tabindex': settings.iTabIndex
		}).html(lang.jump).on("click",function(){
			var curr = inputPageJump.val().replace(/\s|\D/g, '') | 0;
			if (curr) {
				var pages = api.page.info().pages;
				curr = curr > pages ? pages : curr;
				curr--;
				api.page(curr).draw(false);
			}
		});

		$(host).append($('<div />', {
			'class' : "col-sm-2 pull-right input-group hidden-xs"
		}).append(inputPageJump).append(btnPageJump));

		if ( activeEl ) {
			$(host).find( '[data-dt-idx='+activeEl+']' ).focus();
		}
	};

	$.extend($.fn.dataTable.ext.classes, {
		sWrapper: 'dataTables_wrapper form-inline',
		sFilterInput: 'form-control',
		sLengthSelect: 'form-control',
		sProcessing: 'dataTables_processing',
	});

	method.make = function(){
		var config = method.getConfig();
		var columns = method.getColumns(true);
		method.datatable = $dt.DataTable({
			dom: "r" +
				"<'row'<'#toolbar.col-sm-8 text-left'><'col-sm-4 search-filter text-right'f><'clearfix'>>" +
				"<'row'<'col-sm-4 hidden-xs'l><'col-sm-8 col-xs-12'p><'clearfix'>>" +
				"t" +
				"<'row'<'col-sm-4 hidden-xs'i><'col-sm-8 col-xs-12'p><'clearfix'>>",
			language: {
				lengthMenu: '_MENU_',
				zeroRecords: '没有记录',
				search: '<div class="input-group">_INPUT_<span class="input-group-addon"><i class="fa fa-search"></i></span></div>',
				info: '<b>_START_</b>-<b>_END_</b> of <b>_TOTAL_</b>',
				infoEmpty: '<b>0</b>-<b>0</b> of <b>0</b>',
				infoFiltered: '(from <b>_MAX_</b>)',
				searchPlaceholder: '输入关键字',
				paginate: {
					previous: '<i class="fa fa-backward"></i>',
					next: '<i class="fa fa-forward"></i>',
					first: '<i class="fa fa-step-backward"></i>',
					last: '<i class="fa fa-step-forward"></i>',
					jump: '<i class="fa fa-paper-plane-o"></i>'
				},
				aria: {
					paginate: {
						first: '首页',
						previous: '上页',
						next: '下页',
						last: '尾页'
					}
				},
				processing: '<div class="mask"></div><div class="inner"><h3 class="text-light"><b>Loading...</b></h3><div class="preloader-spinner fa-spin"></div></div>'
			},
			column: {
				asSorting: [ 'desc', 'asc' ]  //first sort desc, then asc
			},
			renderer: 'bootstrap',
			ajax: function(data, callback, settings){

				var o = {}, d = data;
				for(var i = 0; i < d.order.length; ++i)
				{
					var item = d.order[i];
					if (typeof d.columns[item.column] != 'undefined' && !!d.columns[item.column].orderable)
					{
						var name = !!d.columns[item.column].name ? d.columns[item.column].name : d.columns[item.column].data;
						if (name) o[name] = item.dir;
					}
				}
				var query = $.extend(true, {}, config.queryParams, {
					q: $.extend(true, d.search.value ? {_all: d.search.value} : {}, querystring('q')),
					o: o,
					f: $.extend(true, {}, querystring('f'))
				});
				$dt.data('url-query', query);
				//修改导出按钮的链接
				$('a[data-append-queries]').each(function(){$(this).attr('href', $(this).data('href'));}).querystring(query);

				var p = (parseInt(d.start) + 1) / d.length;
				var params = $.extend(true, {}, {
					size: d.length,
					page: !isNaN(p) ? Math.ceil(p) : 1
				}, query);

				LP.http.jQueryAjax.getInstance().alertError().post(LP.baseuri + config.namespace+'/'+config.name+'/data?of=json', params).then(json => {
					json.data.data.forEach(function(v, k){v.DT_RowId = 'line-' + (v.id ? v.id : k);});
					callback(json.data);
				}).catch(e => {
					callback({data: [], total: 0, recordsFiltered: 0, recordsTotal: 0, per_page: 0});
				});
			},
			serverSide: true,
			deferRender: true,
			columns: columns,
			rowCallback: function( row, data, dataIndex ) {
				//call
				$dt.triggerHandler('datatable.row', [row, data, dataIndex]);
			},
			createdRow: function( row, data, dataIndex ) {
				// Initialize Tooltips
				if ($.fn.tooltip) $('[data-toggle="tooltip"], .enable-tooltip', row).tooltip({container: 'body', animation: false});
				// Initialize Popovers
				if ($.fn.popover) $('[data-toggle="popover"], .enable-popover', row).popover({container: 'body', animation: true});
				//call
				method.bindMethods(row);
				$dt.triggerHandler('datatable.created-row', [row, data, dataIndex]);
			},
			drawCallback: function( settings ) {
				method.setConfig(settings);
				//call
				$dt.triggerHandler('datatable.draw', [settings]);
			},
			/*footerCallback: function(tfoot, data, start, end, display) {
				$dt.triggerHandler('datatable.footer', [tfoot, data, start, end, display]);
			},
			formatNumber: function( toFormat ) {
				$dt.triggerHandler('datatable.format-number', [toFormat]);
			},
			headerCallback: function(thead, data, start, end, display) {
				$dt.triggerHandler('datatable.header', [thead, data, start, end, display]);
			},
			infoCallback: function(settings, start, end, max, total, pre) {
				$dt.triggerHandler('datatable.info', [settings, start, end, max, total, pre]);
			},*/
			initComplete: function(settings, json) {
				$dt.triggerHandler('datatable.init', [settings, json]);
			},
			preDrawCallback: function( settings ) {
				$dt.triggerHandler('datatable.pre-draw', [settings]);
			},
			stateLoadCallback: function(settings, callback) {
				$dt.triggerHandler('datatable.state-load', [settings, callback]);
			},
			stateLoaded: function(settings, data) {
				$dt.triggerHandler('datatable.state-loaded', [settings, data]);
			},
			stateLoadParams: function(settings, data) {
				$dt.triggerHandler('datatable.state-load-params', [settings, data]);
			},
			stateSaveCallback: function(settings, data) {
				$dt.triggerHandler('datatable.state-save', [settings, data]);
			},
			stateSaveParams: function(settings, data) {
				$dt.triggerHandler('datatable.state-save-params', [settings, data]);
			}
			/*,
			stateSave: false,
			stateDuration: -1*/
		});
	};

	//初始化
	method.bindMethods('body');
	method.make();
	$('#tools-contrainer').appendTo('#toolbar');
	method.bindMethods('#toolbar');

	$('#reload').on('click', function(){
		method.datatable.ajax.reload(null, false);
	});
	/* Select/Deselect all checkboxes in tables */
	$('thead th:eq(0) :checkbox', '.table').on('click', function(e) {

		var checkedStatus   = $(this).prop('checked');
		var table           = $(this).closest('table');
		e.stopPropagation();
		//e.preventDefault();
		$('tbody :checkbox:visible', $dt).each(function() {

			$(this).prop('checked', checkedStatus).triggerHandler('click');
		});

	});
});

})(jQuery);
