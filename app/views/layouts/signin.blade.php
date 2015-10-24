<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>Login | Inzaana</title>
		<% HTML::style('public/css/bootstrap.min.css') %>	
		<% HTML::style('public/css/signIn.css') %>
		<% HTML::style('public/css/font-awesome-4.2.0/css/animations.css') %>
		<% HTML::style('public/css/css3-animate-it-master/css/animations.css') %>
	</head>
	<body>
		<div class="container-fluid">
			@yield('content')
		</div>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<% HTML::script('public/js/bootstrap.min.js') %>
		<% HTML::script('public/js/signIn.js') %>
		<% HTML::script('public/css/css3-animate-it-master/js/css3-animate-it.js') %>
		
		
	</body>
</html>
	