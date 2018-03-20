<{block "head-scripts-laravel"}>
	<script>var Laravel = { csrfToken: '<{csrf_token()}>', baseuri: '<{""|url nofilter}>' };</script>
<{/block}>
<{block "head-scripts-jquery"}>
	<script src="<{'js/jquery-3.3.1.min.js'|static nofilter}>"></script>
	<script>if(typeof jQuery != 'undefined') jQuery.noConflict();</script>
<{/block}>

<{block "head-scripts-bootstrap"}>
	<script src="<{'js/bootstrap3/bootstrap.min.js'|static nofilter}>"></script>
<{/block}>

<{block "head-scripts-inner"}><{/block}>

<{block name="head-scripts-lp"}>
	<script src="<{'js/lp.min.js'|static nofilter}>"></script>
<{/block}>
