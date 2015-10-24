<?php

class HomeController extends BaseController {
	protected $layout = "layouts.home";
	public function __construct() {
					
	}
	public function getLanddingpage(){
		$this->layout->content	=	View::make('home.landingpage');
	}
}
