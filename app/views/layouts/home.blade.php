<?php
	$curRoute       =   Route::currentRouteAction();
	$controller     =   '';
	$action         =   '';
	if($curRoute != ''){
		if(strpos($curRoute,'@')){
			$routePartArr   =   explode('@',$curRoute);
			if(isset($routePartArr) && is_array($routePartArr) && count($routePartArr) > 0){
				if(isset($routePartArr[0])){
					$controller =   $routePartArr[0];
				}
				if(isset($routePartArr[1])){
					$action     =   $routePartArr[1];
				}
			}
		}
	}
 ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Inzaana</title>
			<% HTML::style('public/css/bootstrap.min.css') %>	
			<% HTML::style('public/css/main.css') %>
			<% HTML::style('public/css/style.css') %>
			
			<% HTML::style('public/css/bootstrap.css') %>
			<% HTML::style('public/css/theme-menu.css') %>
			<% HTML::style('public/css/fonts/fonts.css') %>
			<% HTML::style('public/css/font-awesome-4.2.0/css/font-awesome.min.css') %>
			
			<% HTML::style('public/css/css3-animate-it-master/css/animations.css') %>
			<% HTML::style('public/css/font-awesome-animation') %>
			<% HTML::style('public/css/select2.css') %>
		
	  <!--[if lte IE 9]>
			<% HTML::style('public/css/css3-animate-it-master/css/animations-ie-fix.css') %>
	<![endif]-->
	  <!--[if IE]>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
	  <![endif]-->

	</head>
	<body>
		@include('elements.header')
		@include('elements.navigationmenu')
		@include('elements.slider')
		<div class="clearfix"></div>
		@yield('content')
		<div class="clearfix"></div>
		@include('elements.footer')
		
		<!--Scroll To Top-->
		  <a href="#top" class="hc_scrollup"><i class="fa fa-chevron-up"></i></a>
		<!--/Scroll To Top-->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<% HTML::script('public/js/bootstrap.min.js') %>
		<% HTML::script('public/css/css3-animate-it-master/js/css3-animate-it.js') %>
		<% HTML::script('public/js/scroll.js') %>
		<% HTML::script('public/js/smothScrolling.js') %>
		
		<script>
			$('#nav').affix({
			  offset: {
				top: $('header').height()
			  }
			});

			$('#sidebar').affix({
			  offset: {
				top: 200
			  }
			});
		</script>
	</body>
</html>
	