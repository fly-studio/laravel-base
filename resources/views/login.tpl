<{extends file="extends/main.block.tpl"}>

<{block "head-scripts-plus"}>
	<script type="text/javascript">
	(function($){
		$().ready(function(){
			<{call validate selector='#form'}>
		});
	})(jQuery);
	</script>
<{/block}>

<{block "body-container"}>
<div class="container">
	<h1 class="page-header">登录</h1>
	<form action="<{'auth/authenticate-query'|url nofilter}>" id="form" method="POST">
		<{csrf_field() nofilter}>
		<div class="form-group">
			<label for="username">用户名</label>
			<input type="text" class="form-control" name="username" id="username" placeholder="请输入用户名..." value="<{old('username')}>">
		</div>
		<div class="form-group">
			<label for="password">密码</label>
			<input type="password" class="form-control" name="password" id="password" placeholder="请输入密码...">
		</div>
		<div class="checkbox">
			<label>
				<input type="checkbox" name="remember" value="true"> 记住我
			</label>
		</div>
		<button type="submit" class="btn btn-default">登录</button>
	</form>
</div>
<{/block}>