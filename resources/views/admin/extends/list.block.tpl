<{extends file="admin/extends/main.block.tpl"}>

<{block "head-styles-plus"}>
<link rel="stylesheet" href="<{'css/proui/datatable.min.css'|static}>">
<{/block}>
<{block "head-scripts-plus"}>
<link rel="stylesheet" href="<{'js/bootstrap-slider/bootstrap-slider.min.css'|static}>">
<script src="<{'js/bootstrap-slider/bootstrap-slider.min.js'|static}>"></script>
<script src="<{'js/DatePicker/WdatePicker.js'|static}>"></script>
<script>
(function($){
	$().ready(function(){
		$('[name="<{block "name"}><{/block}>/list"]').addClass('active').parents('li').addClass('active');
		$('a[data-append-queries]').each(function(){$(this).data('href', this.href);});
		<{block "inline-script-plus"}><{/block}>
	});
})(jQuery);
</script>
<script src="<{'js/datatable/jquery.dataTables.min.js'|static}>"></script>
<script src="<{'js/template.js'|static}>"></script>
<script src="<{'js/proui/table.min.js'|static}>"></script>
<script src="<{'js/proui/export.min.js'|static}>"></script>
<{include file="common/dates.inc.tpl"}>
<{/block}>

<{block "header"}>
<!-- Form Header -->
<ul class="breadcrumb breadcrumb-top">
	<li><a href="<{''|url}>/<{block "namespace"}>admin<{/block}>"><{config('settings.title')}></a></li>
	<li><a href="<{''|url}>/<{block "namespace"}>admin<{/block}>/<{block "name"}><{/block}>"><{block "title"}><{/block}>管理</a></li>
	<li class="active">列表</li>
</ul>
<!-- END Form Header -->
<{/block}>

<{block "block-title"}>
<!-- DataTable Title -->
<div class="block-title">
	<{block "menus"}>
		<h2 class="pull-left"><strong><{block "title"}><{/block}>列表</strong> 检索</h2>
		<{* <div class="collapse navbar-collapse pull-left">
			<ul class="nav navbar-nav">
				<{block "menus-before"}><{/block}>
				<{block "menus-create"}>
				<li>
					<a href="<{''|url}>/<{block "namespace"}>admin<{/block}>/<{block "name"}><{/block}>/create" data-shortcuts="ctrl+n"> <i class="fa fa-plus"></i> 添加</a>
				</li>
				<{/block}>
				<{block "menus-dropdown-plus"}><{/block}>
				<{block "menus-after"}><{/block}>
			</ul>
		</div> *}>
	<{/block}>
	<{block "options"}>
	<div class="block-options pull-right">
		<{block "options-toggle"}>
		<a href="javascript:void(0)" class="btn btn-alt btn-sm btn-primary enable-tooltip" data-toggle="block-toggle-content" title="折叠/展示 (Ctrl+Shift+T)" data-shortcuts="ctrl+shift+t"><i class="fa fa-arrows-v"></i></a>
		<{/block}>
		<{block "options-full"}>
		<a href="javascript:void(0)" class="btn btn-alt btn-sm btn-primary enable-tooltip" data-toggle="block-toggle-fullscreen" title="全屏切换 (F11)" data-shortcuts="f11"><i class="fa fa-desktop"></i></a>
		<{/block}>
		<!-- <a href="javascript:void(0)" class="btn btn-alt btn-sm btn-primary" data-toggle="block-hide"><i class="fa fa-times"></i></a>  -->
	</div>
	<{/block}>
	<div class="clearfix"></div>
</div>
<div class="clearfix"></div>
<!-- END DataTable Title -->
<{/block}>

<{block "block-content"}>
<div class="block-content">
<{block "filter-before"}><{/block}>
<{block "filter"}><{/block}>
	<div class="clearfix"></div>
<{block "filter-after"}><{/block}>
</div>
<div class="clearfix"></div>
<{block "block-content-table-before"}><{/block}>
<{block "block-content-table"}>
<div class="table-responsive">
	<{block "table-tools"}>
	<div class="block-options" id="tools-contrainer">
		<{block "table-tools-before"}><{/block}>
		<{block "table-tools-create"}>
		<{if $_permissionTable->checkMethodPermission('create')}>
		<a class="btn btn-success btn-sm btn-alt" data-toggle="tooltip" title="新建<{block 'title'}><{/block}>" href="<{''|url}>/<{block "namespace"}>admin<{/block}>/<{block "name"}><{/block}>/create" data-shortcuts="ctrl+n"><i class="fa fa-plus animated pulse infinite"></i></a>
		<{/if}>
		<{/block}>
		<{block "table-tools-plus"}><{/block}>
		<{block "table-tools-resh"}>
		<a class="btn btn-info btn-sm btn-alt " data-toggle="tooltip" title="刷新" id="reload"><i class="fa fa-refresh fa-spin"></i></a>
		<{/block}>
		<{block "table-tools-dropdown"}>
		<div class="btn-group btn-group-sm">
			<a href="javascript:void(0)" class="btn btn-alt btn-sm btn-info dropdown-toggle enable-tooltip" data-toggle="dropdown" title="操作" data-original-title="操作"><span class="caret"></span></a>
			<ul class="dropdown-menu dropdown-custom dropdown-menu-left">
				<{block "table-tools-dropdown-before"}><{/block}>
				<{block "table-tools-dropdown-operate"}>
					<li class="dropdown-header">操作<i class="fa fa-cog pull-right"></i></li>
					<li>
						<{block "table-tools-dropdown-operate-before"}><{/block}>
						<{block "table-tools-dropdown-operate-delete"}>
						<{if $_permissionTable->checkMethodPermission('destory')}>
						<a href="<{''|url}>/<{block "namespace"}>admin<{/block}>/<{block "name"}><{/block}>/0" method="delete" selector="#datatable [name='id[]']:checked" confirm="<{block "table-tools-dropdown-delete-confirm"}>您确定删除这%L项？此操作不可恢复！<{/block}>" class="" data-shortcuts="del"><span class="text-danger"><i class="fa fa-times pull-right "></i>删除所选</span></a>
						<{/if}>
						<{/block}>
						<{block "table-tools-dropdown-operate-plus"}><{/block}>
						<{block "table-tools-dropdown-operate-after"}><{/block}>
					</li>
				<{/block}>
				<{block "table-tools-dropdown-export"}>
					<{if $_permissionTable->checkMethodPermission('export')}>
					<li class="dropdown-header">导出<i class="fa fa-share pull-right"></i></li>
					<li>
						<a href="javascript:void(0)"><i class="fa fa-print pull-right"></i> 打印</a>
						<a href="javascript:void(0)" data-namespace="<{block "namespace"}>admin<{/block}>" data-name="<{block 'name'}><{/block}>" data-export="csv"><i class="fi fi-csv pull-right"></i> CSV </a>
						<a href="javascript:void(0)" data-namespace="<{block "namespace"}>admin<{/block}>" data-name="<{block 'name'}><{/block}>" data-export="xls"><i class="fi fi-xls pull-right"></i> Excel 2003</a>
						<a href="javascript:void(0)" data-namespace="<{block "namespace"}>admin<{/block}>" data-name="<{block 'name'}><{/block}>" data-export="xlsx"><i class="fi fi-xlsx pull-right"></i> Excel 2007+</a>
					</li>
					<{/if}>
				<{/block}>
				<{block "table-tools-dropdown-after"}><{/block}>
			</ul>
		</div>
		<{/block}>

		<{block "table-tools-after"}><{/block}>
	</div>
	<{/block}>
	<table id="datatable" class="table table-vcenter table-condensed table-bordered table-striped table-hover"
		data-name='<{block "name"}><{/block}>'
		data-namespace='<{block "namespace"}>admin<{/block}>'
		data-search-delay='<{block "search-delay"}>800<{/block}>'
		data-display-start='<{block "display-start"}>0.0<{/block}>'
		data-page-length='<{$_size|default:config("size.common")}>'
		data-auto-width='<{block "auto-width"}>false<{/block}>'
		data-searching='<{block "searching"}>true<{/block}>'
		data-processing='<{block "processing"}>true<{/block}>'
		data-paging-type='<{block "paging-type"}>full_numbers<{/block}>'
		data-order='<{block "order"}>[[0, "desc"]]<{/block}>'
		data-query-params='<{block "query-params"}>{}<{/block}>'
		<{block "datatable-settings"}><{/block}>
	>
		<thead>
			<{block "table-thead-before"}><{/block}>
			<tr>
			<{block "table-th"}>
				<{block "table-th-before"}><{/block}>
				<{block "table-th-id"}><th class="text-left"><input type="checkbox" id="checkAll" > #</th><{/block}>
				<{block "table-th-plus"}><{/block}>
				<{block "table-th-timestamps"}>
					<{block "table-th-timestamps-created_at"}><th>添加</th><{/block}>
					<{block "table-th-timestamps-updated_at"}><th>更新</th><{/block}>
				<{/block}>
				<{block "table-th-options"}>
				<th class="text-center">操作</th>
				<{/block}>
				<{block "table-th-after"}><{/block}>
			<{/block}>
			</tr>
			<{block "table-thead-plus"}><{/block}>
			<{block "table-thead-after"}><{/block}>
		</thead>
		<tbody>
			<{block "table-tbody"}>
			<tr style="display: none;">
			<{block "table-td"}>
				<{block "table-td-before"}><{/block}>
				<{block "table-td-id"}>
				<td class="text-left" data-from="id" data-order-sequence='["desc", "asc"]'><input type="checkbox" name="id[]" value="{{data}}">{{data}}</td>
				<{/block}>
				<{block "table-td-plus"}><{/block}>
				<{block "table-td-timestamps"}>
					<{block "table-td-timestamps-created_at"}>
				<td data-from="created_at" data-order-sequence='["desc", "asc"]'>{{data}}</td>
					<{/block}>
					<{block "table-td-timestamps-updated_at"}>
				<td data-from="updated_at" data-order-sequence='["desc", "asc"]'>{{data}}</td>
					<{/block}>
				<{/block}>
				<{block "table-td-options"}>
				<td class="text-center" data-from="" data-orderable="false">
					<div class="btn-group">
						<{block "table-td-options-before"}><{/block}>
							<{block "table-td-options-edit"}>
							<{if $_permissionTable->checkMethodPermission('edit')}>
						<a href="<{''|url}>/<{block "namespace"}>admin<{/block}>/<{block "name"}><{/block}>/{{full.id}}/edit" data-toggle="tooltip" title="编辑" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a>
							<{/if}>
							<{/block}>
							<{block "table-td-options-plus"}><{/block}>
							<{block "table-td-options-delete"}>
							<{if $_permissionTable->checkMethodPermission('destory')}>
						<a href="<{''|url}>/<{block "namespace"}>admin<{/block}>/<{block "name"}><{/block}>/{{full.id}}" method="delete" confirm="<{block "table-td-options-delete-confirm"}>您确定删除这项：{{full.id}}吗？<{/block}>" data-toggle="tooltip" title="删除" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a>
							<{/if}>
							<{/block}>
						<{block "table-td-options-after"}><{/block}>
					</div>
				</td>
				<{/block}>
				<{block "table-td-after"}><{/block}>
			<{/block}>
			</tr>
			<{/block}>
		</tbody>
	</table>
</div>
<div class="clearfix"></div>
<{/block}>
<{block "block-content-table-after"}><{/block}>
<{/block}>

<{block "body-after"}>
<{block "export"}>
<div class="modal fade" id="export-modal" tabindex="-1" role="dialog" aria-labelledby="export-modal" aria-hidden="true" data-size="<{$_exportSize|default:config('size.export')}>">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">关闭</span></button>
				<h4 class="modal-title">导出 数据</h4>
			</div>
			<div class="modal-body">
				<p>导出格式：<label for="" class="label label-info" id="export-format"><{$_of}></label></p>
				<p>当前数量：<b id="export-count">0</b> <!-- of <b id="export-total"></b> --></p>
				<div class="alert alert-warning"><b>注意：</b>数据按照当前表格的<b>筛选结果</b>、<b>排序方式</b>导出。</div>
				<p>每个文件：<span id="export-size">0</span>条
				<div class="input-slider-success">
					<input type="text" id="export-slider" class="form-control">
				</div>
				<div class="row" id="export-links"></div>
				<div class="clearfix"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
			</div>
		</div>
	</div>
</div>
<{/block}>
<{/block}>
