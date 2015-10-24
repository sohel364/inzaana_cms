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
	$menuSubMenuArr				=	BaseController::getMenuSubmen();
	$roleMenuArrArr				=	BaseController::getRoleMenuAdminLeftPane();	
	
?>
<section id="col-left" class="col-left-nano">
	<div id="col-left-inner" class="col-left-nano-content">		
		<div class="collapse navbar-collapse navbar-ex1-collapse" id="sidebar-nav">
			@if(is_array($menuSubMenuArr) && count($menuSubMenuArr) > 0)			
				<ul class="nav nav-pills nav-stacked">
					@foreach($menuSubMenuArr as $menuKey=>$menuVal)					
						@if(is_array($menuVal['submenus']) && count($menuVal['submenus']) > 0)							
							@if(isset($roleMenuArrArr['editMenuList']) && is_array($roleMenuArrArr['editMenuList']) && in_array($menuVal['id'],$roleMenuArrArr['editMenuList']))								
								<li class="@if($menuVal['controller'] == $controller) active @endif">
									<a href="javascript:void(0);" class="dropdown-toggle">	
										<i class="<% BaseController::getConfigVal('menu_icon',$menuVal['menu_icon']) %>"></i>
										<span><% $menuVal['menu_name'] %></span>
										<i class="fa fa-chevron-circle-right drop-icon"></i>
									</a>								
									<ul class="submenu">
										@foreach($menuVal['submenus'] as $subMenuKey=>$subMenuVal)
											@if(isset($roleMenuArrArr['editSubMenuList']) && is_array($roleMenuArrArr['editSubMenuList']) && in_array($subMenuVal['id'],$roleMenuArrArr['editSubMenuList']))
												<li>
													<a href="<% URL::to($subMenuVal['sub_menu_url']); %>" class="@if($subMenuVal['action'] == $action) active @endif">												
														<% $subMenuVal['sub_menu_name'] %>
													</a>
												</li>
											@endif
										@endforeach
									</ul>
								</li>
							@endif
						@else						
							@if(isset($roleMenuArrArr['editMenuList']) && is_array($roleMenuArrArr['editMenuList']) && in_array($menuVal['id'],$roleMenuArrArr['editMenuList']))								
								<li class="@if($menuVal['controller'] == $controller) active @endif">
									<a href="<% URL::to($menuVal['menu_url']); %>">
										<i class="<% BaseController::getConfigVal('menu_icon',$menuVal['menu_icon']) %>"></i>
										<span><% $menuVal['menu_name'] %></span>
									</a>
								</li>
							@endif
						@endif
					@endforeach					
				</ul>
			@endif			
		</div>
	</div>
</section>