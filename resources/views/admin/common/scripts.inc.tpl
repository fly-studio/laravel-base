<!-- Remember to include excanvas for IE8 chart support -->
<!--[if IE 8]><script src="<{'js/excanvas.min.js'|static}>"></script><![endif]-->

<!-- Modernizr (browser feature detection library) & Respond.js (Enable responsive CSS code on browsers that don't support it, eg IE8) -->
<{block "head-scripts-debug"}>
<script src="<{'js/debug/eruda.debug.js'|static}>"></script>
<{/block}>
<{block "head-scripts-jquery"}>
<script src="<{'js/jquery-2.1.0.min.js'|static}>"></script>
<script>jQuery.noConflict();</script>
<script src="<{'js/jquery.cookie.min.js'|static}>"></script>

<{/block}>
<{block "head-scripts-bootstrap"}>
<script src="<{'js/bootstrap3/bootstrap.min.js'|static}>"></script>
<{/block}>
<{block "head-scripts-noty"}>
<script src="<{'js/noty/jquery.noty.packaged.min.js'|static}>"></script>
<script src="<{'js/noty/themes/default.js'|static}>"></script>
<script src="<{'js/common/noty.js'|static}>"></script>
<{/block}>

<{block "head-scripts-inner"}><{/block}>

<{block "head-scripts-bbq"}>
<script src="<{'js/jquery.bbq.min.js'|static}>"></script>
<{/block}>
<{block "head-scripts-common"}>
<script src="<{'js/common-2.0.js'|static}>"></script>
<{/block}>
<{block name="head-scripts-lp"}>
<script src="<{'js/laravel.lp.min.js'|static}>"></script>
<{/block}>
<{block name="head-scripts-select2"}>
<link rel="stylesheet" href="<{'js/select2/select2.min.css'|static}>"></script>
<script src="<{'js/select2/select2.min.js'|static}>"></script>
<script src="<{'js/select2/i18n/zh-CN.js'|static}>"></script>
<script src="<{'js/laravel.select.min.js'|static}>"></script>
<{/block}>
<{block "head-scripts-app"}>
<script src="<{'js/mousetrap.min.js'|static nofilter}>"></script>
<script src="<{'js/proui/app.min.js'|static}>"></script>
<{/block}>
<script>
(function($){
	//Theme auto
	var cookie_theme = $.cookie('proui-theme');
	if (cookie_theme)  $('<link id="theme-link" rel="stylesheet" href="' + cookie_theme + '">').appendTo('head');
})(jQuery);
</script>