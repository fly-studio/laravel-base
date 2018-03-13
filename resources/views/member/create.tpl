<{extends file="extends/main.block.tpl"}>



<{block "head-scripts-plus"}>
	<{include file="common/uploader.inc.tpl"}>
	<script type="text/javascript">
	(function($){
		$().ready(function(){
			$('#avatar_aid').uploader();
			<{call validate selector='#form'}>
		});
	})(jQuery);
	</script>
<{/block}>

<{block "body-container"}>
<div class="container">
	<h1 class="page-header">注册</h1>
</div>
<div class="container">
	<form action="<{'member'|url nofilter}>" class="" method="POST" autocomplete="off" id="form">
		<input type="hidden" name="_token" value="<{csrf_token()}>">
		<div class="form-group">
			<label for="username" class="control-label">用户名</label>
			<input type="text" class="form-control" name="username" id="username" placeholder="请输入用户名..." value="<{old('username')}>">
		</div>
		<div class="form-group">
			<label for="password" class="control-label">密码</label>
			<input type="password" class="form-control" name="password" id="password" placeholder="请输入密码...">
		</div>
		<div class="form-group">
			<label for="password_confirmation" class="control-label">密码确认</label>
			<input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="请再次确认密码...">
		</div>
		<div class="form-group radio">
		<{foreach $_fields.gender as $v}>
			<label><input type="radio" class="" name="gender" value="<{$v['id']}>"> <{$v['title']}></label>
		<{/foreach}>
			<div class="clearfix"></div>
		</div>
		<div class="form-group hidden">
			<label for="avatar_aid" class="control-label">上传图片</label>
			<input type="hidden" class="form-control" name="avatar_aid" id="avatar_aid" value="0">
		</div>

		<div class="form-group checkbox">
			<label>
				<input type="checkbox" class="" name="accept_license" id="accept_license" value="1"> 我已阅读并同意协议
			</label>
		</div>
		<button type="submit" class="btn btn-success btn-block">注册</button>
	</form>
</div>
<{/block}>
