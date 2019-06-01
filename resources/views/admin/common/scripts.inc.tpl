<{block "head-scripts-jquery"}>
<script src="<{'js/jquery-2.1.0.min.js'|static nofilter}>"></script>
<script>jQuery.noConflict();</script>
<script src="<{'js/jquery.cookie.min.js'|static nofilter}>"></script>

<{/block}>
<{block "head-scripts-bootstrap"}>
<script src="<{'js/bootstrap3/bootstrap.min.js'|static nofilter}>"></script>
<{/block}>
<{block "head-scripts-noty"}>
<script src="<{'js/noty/jquery.noty.packaged.min.js'|static nofilter}>"></script>
<script src="<{'js/noty/themes/default.js'|static nofilter}>"></script>
<{/block}>

<{block "head-scripts-inner"}><{/block}>

<{block "head-scripts-bbq"}>
<script src="<{'js/jquery.bbq.min.js'|static nofilter}>"></script>
<{/block}>
<{block "head-scripts-promise"}>
<script src="<{'js/es/promise.min.js'|static nofilter}>"></script>
<{/block}>
<{block "head-scripts-common"}>
<{/block}>
<{block "head-scripts-vue"}>
<script src="<{'js/vue/vue.min.js'|static nofilter}>"></script>
<script src="<{'js/vue/vuex.min.js'|static nofilter}>"></script>
<{/block}>
<{block name="head-scripts-lp"}>
<script src="<{'js/jsencrypt.min.js'|static nofilter}>"></script>
<script src="<{'js/lp.min.js'|static nofilter}>"></script>
<script src="<{'js/lp/noty.tip.js'|static nofilter}>"></script>

<{/block}>
<{block name="head-scripts-select2"}>
<link rel="stylesheet" href="<{'js/select2/select2.min.css'|static nofilter}>">
<script src="<{'js/select2/select2.min.js'|static nofilter}>"></script>
<script src="<{'js/select2/i18n/zh-CN.js'|static nofilter}>"></script>
<script src="<{'js/laravel.select.min.js'|static nofilter}>"></script>
<{/block}>
<{block "head-scripts-app"}>
<script src="<{'js/mousetrap.min.js'|static nofilter}>"></script>
<script src="<{'js/proui/app.min.js'|static nofilter}>"></script>
<{/block}>
<script>
(function($){
	//Theme auto
	var cookie_theme = $.cookie('proui-theme');
	if (cookie_theme)  $('<link id="theme-link" rel="stylesheet" href="' + cookie_theme + '">').appendTo('head');
})(jQuery);
</script>
