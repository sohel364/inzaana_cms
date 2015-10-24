<div class="container">
	<a href="<% URL::to('/'); %>" id="logo" class="navbar-brand">		
		<div class="headerLogo">
			Sunday School
		</div>
	</a>
	<div class="clearfix">
		<button class="navbar-toggle" data-target=".navbar-ex1-collapse" data-toggle="collapse" type="button">
			<span class="sr-only">Toggle navigation</span>
			<span class="fa fa-bars"></span>
		</button>
		<div class="nav-no-collapse navbar-left pull-left hidden-sm hidden-xs">
			<ul class="nav navbar-nav pull-left">
				<li>
					<a class="btn" id="make-small-nav">
						<i class="fa fa-bars"></i>
					</a>
				</li>
			</ul>
		</div>
		<div class="nav-no-collapse pull-right" id="header-nav">
			<ul class="nav navbar-nav pull-right">
				<li class="dropdown profile-dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<span class="hidden-xs">Welcome @if(isset(Auth::user()->firstname)) <% Auth::user()->firstname %> @endif</span> <b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<li><a href="<% URL::to('/users/profile'); %>"><i class="fa fa-user"></i>Profile</a></li>
						@if(isset(Auth::user()->role_id) && Auth::user()->role_id == BaseController::getConfigVal('user_designation','admin'))
							<li><a href="<% URL::to('/master/setting'); %>"><i class="fa fa-cog"></i>Settings</a></li>
						@endif
						<li><a href="<% URL::to('/users/logout'); %>"><i class="fa fa-power-off"></i>Logout</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</div>