<{extends file="extends/main.block.tpl"}>

<{block "head-scripts-plus"}>
	<script type="text/javascript">
	(function($){
		$().ready(function(){
			<{call validate selector='#form'}>
		});
	})(jQuery);
	</script>
	<link rel="stylesheet" href="<{'css/socialites.css'|static nofilter}>">
<{/block}>

<{block "body-container"}>
<div class="container">
	<h1 class="page-header">登录</h1>
	<form action="<{'auth/authenticate-query'|url nofilter}>" id="form" method="POST">
		<{csrf_field() nofilter}>
		<div class="form-group">
			<div class="row">
				<label for="username" class="col-xs-6">用户名</label>
				<span class="col-xs-6 text-right"><a href="<{'member/create'|url nofilter}>?redirect_uri=<{$_redirect_uri|urlencode nofilter}>">注册账号</a></span>
			</div>
			<input type="text" class="form-control" name="username" id="username" placeholder="请输入用户名..." value="<{old('username')}>" tabindex="1">
		</div>
		<div class="form-group">
			<div class="row">
				<label for="password" class="col-xs-6">密码</label>
				<span class="col-xs-6 text-right"><a href="<{'reset/password'|url nofilter}>">找回密码</a></span>
				<div class="clearfix"></div>
			</div>
			<input type="password" class="form-control" name="password" id="password" placeholder="请输入密码..." tabindex="2">
		</div>
		<div class="checkbox">
			<label>
				<input type="checkbox" name="remember" value="true" checked="checked" tabindex="3"> 记住我
			</label>
		</div>
		<button type="submit" class="btn btn-success btn-block" tabindex="4">登录</button>
	</form>

	<div class="row">
		<div class="col-xs-12 text-center" style="position: relative;border-bottom: 1px solid #eee;margin: 30px 0;">
			<small class="" style="position: absolute; top: -10px; text-align: center;left: calc(50% - 42px);background-color: #fff;color: #777;">
				更多登录方式
			</small>
		</div>
		<div class="col-xs-12 text-center">
			<{foreach $_socialites as $item}>
			<a class="" href="<{'socialite/login'|url nofilter}>/<{$item['id']}>">
				<span class="icon-sn-<{$item->socialite_type->name}>" title="<{$item['name']}>"></span>
			</a>
			<{/foreach}>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 text-center" style="margin-top: 30px;">
			<small>登录即表示您同意网站的《服务条款》</small>
		</div>
	</div>
</div>
<{/block}>
