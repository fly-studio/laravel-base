<script type="text/x-templete" id="at-selector-tpl">
<div class="btn-group" style="z-index: 3;">
	<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<i class="fa fa-paper-plane-o text-info"></i>
	</button>
	<ul class="dropdown-menu dropdown-menu-right">
		<li class="dropdown-header">最近<i class="fa fa-clock-o pull-right"></i></li>

		<li><a href="javascript:void(0);" data-start="<{\Carbon\Carbon::now()->toDateString()}>" data-end="" class="btn btn-sm btn-link btn-at-selector">今天</a></li>
		<li><a href="javascript:void(0);" data-start="<{\Carbon\Carbon::now()->startOfWeek()->toDateString()}>" data-end="" class="btn btn-sm btn-link btn-at-selector">本周</a></li>
		<li><a href="javascript:void(0);" data-start="<{\Carbon\Carbon::now()->startOfMonth()->toDateString()}>" data-end="" class="btn btn-sm btn-link btn-at-selector">本月</a></li>
		<li><a href="javascript:void(0);" data-start="<{\Carbon\Carbon::now()->startOfQuarter()->toDateString()}>" data-end="" class="btn btn-sm btn-link btn-at-selector">本季</a></li>
		<li><a href="javascript:void(0);" data-start="<{\Carbon\Carbon::now()->startOfYear()->toDateString()}>" data-end="" class="btn btn-sm btn-link btn-at-selector">今年</a></li>

		<li class="dropdown-header">区间<i class="fa fa-calendar pull-right"></i></li>

		<li><a href="javascript:void(0);" data-start="<{\Carbon\Carbon::now()->subWeek()->toDateString()}>" data-end="" class="btn btn-sm btn-link btn-at-selector">7天</a></li>
		<li><a href="javascript:void(0);" data-start="<{\Carbon\Carbon::now()->subWeeks(2)->toDateString()}>" data-end="" class="btn btn-sm btn-link btn-at-selector">14天</a></li>
		<li><a href="javascript:void(0);" data-start="<{\Carbon\Carbon::now()->subMonth()->toDateString()}>" data-end="" class="btn btn-sm btn-link btn-at-selector">一月</a></li>
		<li><a href="javascript:void(0);" data-start="<{\Carbon\Carbon::now()->subMonths(3)->toDateString()}>" data-end="" class="btn btn-sm btn-link btn-at-selector">三月</a></li>
		<li><a href="javascript:void(0);" data-start="<{\Carbon\Carbon::now()->subMonths(6)->toDateString()}>" data-end="" class="btn btn-sm btn-link btn-at-selector">半年</a></li>
		<li><a href="javascript:void(0);" data-start="<{\Carbon\Carbon::now()->subYear()->toDateString()}>" data-end="" class="btn btn-sm btn-link btn-at-selector">一年</a></li>

		<li class="dropdown-header">历史<i class="fa fa-history pull-right"></i></li>

		<li><a href="javascript:void(0);" data-start="<{\Carbon\Carbon::now()->subDay()->toDateString()}>" data-end="<{\Carbon\Carbon::now()->subDay()->toDateString()}> 23:59:59" class="btn btn-sm btn-link btn-at-selector">昨天</a></li>
		<li><a href="javascript:void(0);" data-start="<{\Carbon\Carbon::now()->subWeek()->startOfWeek()->toDateString()}>" data-end="<{\Carbon\Carbon::now()->subWeek()->endOfWeek()->toDateString()}> 23:59:59" class="btn btn-sm btn-link btn-at-selector">上周</a></li>
		<li><a href="javascript:void(0);" data-start="<{\Carbon\Carbon::now()->subMonth()->startOfMonth()->toDateString()}>" data-end="<{\Carbon\Carbon::now()->subWeek()->endOfWeek()->toDateString()}> 23:59:59" class="btn btn-sm btn-link btn-at-selector">上月</a></li>
	</ul>
</div>
</script>
<script src="<{'static/js/laravel.at-selector.min.js'|url nofilter}>"></script>
