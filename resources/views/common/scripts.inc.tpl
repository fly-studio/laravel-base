<{block "head-scripts-laravel"}>
	<script>var Laravel = { csrfToken: '<{csrf_token()}>', baseuri: '<{""|url nofilter}>' };</script>
<{/block}>
<{block "head-scripts-debug"}>
	<script src="<{'js/debug/eruda.debug.js'|static nofilter}>"></script>
<{/block}>
<{block "head-scripts-jquery"}>
	<script src="<{'js/jquery-3.3.1.min.js'|static nofilter}>"></script>
	<script>if(typeof jQuery != 'undefined') jQuery.noConflict();</script>
<{/block}>

<{block "head-scripts-bootstrap"}>
	<script src="<{'js/bootstrap4/bootstrap.min.js'|static nofilter}>"></script>
<{/block}>

<{block "head-scripts-inner"}><{/block}>

<{block "head-scripts-common"}>
	<script src="<{'js/common-2.0.js'|static nofilter}>"></script>
<{/block}>
<{block name="head-scripts-lp"}>
	<script src="<{'js/laravel.lp.min.js'|static nofilter}>"></script>
<{/block}>
