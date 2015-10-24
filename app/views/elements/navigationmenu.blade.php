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
	$activeCls					= 	'active';
?>
<!-- Begin Navbar -->
<div id="nav">
    <div class="navbar navbar-default navbar-static animatedParent animateOnce">
		<div class="container animated fadeInDownShort">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<a class="navbar-brand" href="<% URL::to('/'); %>"><!-- <img alt="Logo" src="images/logo.png" class="img-responsive"> --><span>In</span>zaana</a>
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav navbar-right text-center">
					<li class="active"><a href="<% URL::to('/'); %>">Home</a></li>
					<li><a href="<% URL::to('/'); %>">About us</a></li>
					<li><a href="<% URL::to('/'); %>l">Templates</a></li>
					<li><a href="<% URL::to('/'); %>">Explore</a></li>
					<li><a href="<% URL::to('/'); %>">Support</a></li>
					<li><a href="<% URL::to('/'); %>">Contact</a></li>
					<div class="search">
						  <input type="text" class="form-control input-sm" maxlength="64" placeholder="Search" />
						  <button type="submit" class="btn btn1"><i class="fa fa-search"></i></button>
					</div>
				</ul>
			</div>
			<!-- /.navbar-collapse -->
		</div>
    </div>
    <!-- /.navbar -->
</div>
