<!DOCTYPE html>
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if IE 9]>         <html class="no-js lt-ie10"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<{include file="common/title.inc.tpl"}>
	<meta name="csrf-token" content="<{csrf_token()}>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="renderer" content="webkit">
	<{include file="common/icons.inc.tpl"}>
	<{include file="admin/common/styles.inc.tpl"}>
	<{include file="admin/common/scripts.inc.tpl"}>
	<script src="<{'js/chart/flot/jquery.flot.min.js'|static}>"></script>
</head>

<body class="page-loading">
	<{include file="admin/common/loading.inc.tpl"}>
	<div id="page-container" class="sidebar-partial sidebar-visible-lg sidebar-no-animations">
		<{include file="admin/sidebar.inc.tpl"}>

		<!-- Main Container -->
		<div id="main-container">
			<{include file="admin/menubar.inc.tpl"}>

			<!-- Page content -->
			<div id="page-content">
				<!-- Dashboard Header -->
				<!-- For an image header add the class 'content-header-media' and an image as in the following example -->
				<div class="content-header content-header-media">
					<div class="header-section">
						<div class="row">
							<!-- Main Title (hidden on small devices for the statistics to fit) -->
							<div class="col-md-4 col-lg-6 hidden-xs hidden-sm">
								<h1>欢迎 <strong><{$_user.nickname}></strong> <{if $_user.gender.name=='male'}>先生<{else if $_user.gender.name=='female'}>女士<{/if}><br><small>学而时习之！</small></h1>
							</div>
							<!-- END Main Title -->

							
						</div>
					</div>
				</div>
				<!-- END Dashboard Header -->
			</div>
			<!-- END Page Content -->

			<{include file="admin/copyright.inc.tpl"}>
		</div>
		<!-- END Main Container -->
	</div>
	<!-- END Page Container -->

	<!-- Scroll to top link, initialized in js/app.js - scrollToTop() -->
	<a href="#" id="to-top"><i class="fa fa-angle-double-up"></i></a>

</body>
</html>
