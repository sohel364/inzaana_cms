<?php
	class CensorController extends BaseController {
        protected $layout = "layouts.ajax";
        public function __construct() {
            $this->beforeFilter('csrf', array('on'=>'post'));
        }
		/**
		* Used for validating all form
		* 
		* @param string $term
		* 
		* @return json $dataArr
		*/
	}
?>