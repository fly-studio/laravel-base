<{extends file="admin/extends/list.block.tpl"}>

<{block "title"}>用户<{/block}>

<{block "name"}>member<{/block}>

<{block "head-scripts-after"}>
<script src="<{'static/js/emojione.js'|url}>"></script>
<{/block}>

<{block "filter"}>
<{include file="admin/member/filters.inc.tpl"}>
<{/block}>

<{block "table-th-plus"}>
<th class="text-center"><i class="gi gi-user"></i></th>
<th>用户名</th>
<th>昵称</th>
<th>姓名</th>
<th>性别</th>
<th>手机</th>
<th>用户组</th>
<{/block}>

<!-- DataTable的Block -->
<{block "table-td-plus"}>
<td data-from="avatar_aid" data-orderable="false">
	<img src="<{null|attachment}>/{{data}}/80x80" alt="avatar" class="img-circle">
</td>
<td data-from="username">{{data}}</td>
<td data-from="nickname"><span class="enable-emoji">{{if data}}{{data.emojione()}}{{/if}}</span></td>
<td data-from="realname">{{data}}</td>
<td data-from="gender"><span class="label label-primary">{{data.title || '未知'}}</span></td>
<td data-from="phone">{{data}}</td>
<td data-from="roles" data-orderable="false">
{{each data as v k}}
<span class="label label-info">{{v.display_name}}</span>
{{/each}}
</td>
<{/block}>

<{block "table-td-options-delete-confirm"}>您确定删除这项：{{full.username}}吗？<{/block}>
