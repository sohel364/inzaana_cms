<?php 
    class UsersController extends BaseController {
        protected $layout									=	"layouts.signin";
        public function __construct() {
            $this->beforeFilter('csrf', array('on'=>'post'));
			$this->beforeFilter('designationMenuCheck', array('on'=>'get'));
            $authAllowAction								=	array(
																	'getLogout',
																	'getCreateuser',
																	'getUserlistingajax',
																	'getProfile',
																	'postChangepassword',
																);
            $this->beforeFilter('auth', array('only'=>$authAllowAction));
        }
        public function getRegister() {
			$this->layout									=   View::make('layouts.signup');
			if(isset(Auth::user()->id) && Auth::user()->id != 0){
				return Redirect::to('censors/dashboard')->with('message', 'You are now logged in!');
			}else{
				$this->layout->content = View::make('users.register');
			}
        }        
        public function postCreate() {
			$formData										=   Input::all();
			$formData['formdata']['role_id']			=	$this->getConfigVal('user_designation','parent');
            $validator										=   Validator::make($formData['formdata'],User::$rules['signup'],User::$messages); 			
            if ($validator->passes()) {
				$formData['formdata']['created_at']			=	date('Y-m-d h:i:s');
				$formData['formdata']['updated_at']			=	date('Y-m-d h:i:s');
				$formData['formdata']['password']			=	Hash::make($formData['formdata']['password']);	
				unset($formData['formdata']['password_confirmation']);
				
				try {
					$id										=	DB::table('users')->insertGetId($formData['formdata']);
				} catch(ValidationException $e){

				} catch(\Exception $e){

				}
				if(isset($id) && $id != 0){
					//return Redirect::to('users/login')->with('message', 'Thanks for registering! Please use your Email & password for login');
					return Redirect::to('users/login')->with('message', 'Signed up successfully , Please continue to login with your email address and password');
					
				}else{
					return Redirect::to('users/register')->with('error', 'The following errors occurred')->withErrors($validator)->withInput();
				}                
            } else {
                return Redirect::to('users/register')->with('error', 'The following errors occurred')->withErrors($validator)->withInput();
            }
        }        
        public function getLogin() {
			if(isset(Auth::user()->id) && Auth::user()->id != 0){
				return Redirect::to('censors/dashboard')->with('message', 'You are now logged in!');
			}else{
				$this->layout->content = View::make('users.login');
			}		           
        }
        public function postSignin() {
			$userObj										=   DB::table('users')
																	->where('users.email','=',Input::get('email'))
																	->select('users.is_active','users.is_reset_req','users.expired_date','users.id')
																	->first();
			if(is_object($userObj)){
				if(isset($userObj->is_active) && $userObj->is_active == 'Y'){
					if(isset($userObj->is_reset_req) && $userObj->is_reset_req == 'N'){
						if(isset($userObj->expired_date)){
							if($userObj->expired_date == '0000-00-00'){
								if (Auth::attempt(array('email'=>Input::get('email'), 'password'=>Input::get('password')))) {
									if(Auth::user()->role_id == $this->getConfigVal('user_designation','parent')){
										return Redirect::to('parent/parentdetail');
									}else{
										return Redirect::to('censors/dashboard')->with('message', 'You are now logged in!');
									}									
								} else {
									return Redirect::to('users/login')
										->with('error', 'username/password combination was incorrect')
										->withInput();
								}
							}else{
								$currentDat						=	date('Y-m-d');
								$currentTime					=	strtotime($currentDat);
								$expiredTime					=	strtotime($userObj->expired_date);//echo $expiredTime.'++'.$currentTime;exit;
								if($expiredTime >= $currentTime){
									if (Auth::attempt(array('email'=>Input::get('email'), 'password'=>Input::get('password')))) {
										DB::table('users')
											->where('id',$userObj->id)
											->update(array('users.expired_date'=>'0000-00-00'));
										if(Auth::user()->role_id == $this->getConfigVal('user_designation','parent')){
											return Redirect::to('parent/parentdetail');
										}else{
											return Redirect::to('censors/dashboard')->with('message', 'You are now logged in!');
										}
									} else {
										return Redirect::to('users/login')
											->with('error', 'username/password combination was incorrect.')
											->withInput();
									}
								}else{
									return Redirect::to('users/login')
											->with('error', 'Your account is expired.')
											->withInput();
								}							
							}
						}
					}else{
						return Redirect::to('users/login')
								->with('error','Your have reset your password.Check your mail for password reset link.')
								->withInput();
					}					
				}else{
					return Redirect::to('users/login')
						->with('error','Your account is inactive.')
						->withInput();
				}
			}else{
				return Redirect::to('users/login')
                    ->with('error', 'Invalid login.')
                    ->withInput();
			}            
        }
        public function getLogout() {
            Auth::logout();
            return Redirect::to('/sundayadmin')->with('message', 'Your are now logged out!');
        }
		public function getCreateuser($id = 0){
			$this->layout									=   View::make('layouts.admin');
			$pageTitle										=   'Manage User';
            $frmBtn											=   'Save';
			$modelid										=	(int)$id;
			$viewDataObj									=	'';
            if(isset($modelid) && $modelid != 0){
                $viewDataObj								=   DB::table('users')
																	->where('users.id','=',$modelid)
																	->first();
				// Changing dob to d-m-y format before sending to view				
				if(isset($viewDataObj->expired_date) ){	
					if($viewDataObj->expired_date == '0000-00-00'){
						$viewDataObj->expired_date			=	'';
					}else{
						$viewDataObj->expired_date			=	$this->DB2Date($viewDataObj->expired_date);
					}					
				}
            }
			$currentDat										=	date('Y-m-d');
			$after15Days									=	date('m/d/Y',strtotime("+15 day $currentDat"));
			$designationArr									=	$this->getDesignatonList();
            $layoutArr										=   array(
																	'pageTitle'				=>  $pageTitle,
																	'frmBtn'				=>  $frmBtn,
																	'modelid'				=>  $modelid,															
																	'designationArr'        =>  $designationArr,
																	'viewDataObj'			=>  $viewDataObj,
																	'after15Days'			=>  $after15Days,
																);
            $this->layout->content							=	View::make('users.createuser',array('layoutArr'=>$layoutArr));
			/*
			 * Setting student object in edit case to view so that
			 * we can itterate it in layout page in order to prefill
			 * the form element value
			 */
			view::share(array('viewDataObj'=>$viewDataObj));
		}
		/**
		* Used for validating all form
		* 
		* @param string $term
		* @return json $dataArr
		*/
		public function postManageuser(){
			$valiationArr									=   array();
			if(isset(Auth::user()->id) && Auth::user()->id){
				$formData									=   Input::all();				
				$formDataArr								=   array();
				if(isset($formData['formdata']) && $formData['formdata'] != ''){ 
					parse_str($formData['formdata'],$formDataArr);					
					if(isset($formDataArr['formdata']) && is_array($formDataArr['formdata']) && count($formDataArr['formdata']) > 0){						
						if(isset($formDataArr['formdata']['id']) && $formDataArr['formdata']['id'] != 0){
							$validator						=   Validator::make($formDataArr['formdata'],User::$rules['edit'],User::$messages);
						}else{
							$validator						=   Validator::make($formDataArr['formdata'],User::$rules['create'],User::$messages);
						}				
						if ($validator->fails()){
							$errorArr						=   $validator->getMessageBag()->toArray();
							if(isset($errorArr) && is_array($errorArr) && count($errorArr) > 0){
								foreach($errorArr as $errorKey=>$errorVal){
									$valiationArr[]			=   array(
																	'modelField'            =>  $errorKey,
																	'modelErrorMsg'         =>  $errorVal[0],
																);
								}
							}
						}
						if(is_array($valiationArr) && count($valiationArr) > 0){
							echo '****FAILURE****'.json_encode($valiationArr);  
						}else{
							DB::beginTransaction();
							if(isset($formDataArr['formdata']['id']) && $formDataArr['formdata']['id'] != 0){
								$formDataArr['formdata']['updated_at']			=	date('Y-m-d h:i:s');
								$formDataArr['formdata']['expired_date']		=	$this->date2DB($formDataArr['formdata']['expired_date']);								
								$formDataArr['formdata']['is_active']			=	$formDataArr['formdata']['is_active'];
								try {
									DB::table('users')
										->where('id', $formDataArr['formdata']['id'])
										->update($formDataArr['formdata']);
									$id											=	$formDataArr['formdata']['id'];
								} catch(ValidationException $e){

								} catch(\Exception $e){

								}
							}else{
								global $password;
								$password										=	uniqid();
								$formDataArr['formdata']['password']			=	Hash::make($password);
								$formDataArr['formdata']['created_at']			=	date('Y-m-d h:i:s');
								$formDataArr['formdata']['updated_at']			=	date('Y-m-d h:i:s');
								$formDataArr['formdata']['expired_date']		=	$this->date2DB($formDataArr['formdata']['expired_date']);								
								$formDataArr['formdata']['is_active']			=	$formDataArr['formdata']['is_active'];
								try {
									$id												=	DB::table('users')->insertGetId($formDataArr['formdata']);
								} catch(ValidationException $e){

								} catch(\Exception $e){

								}
							}
							if(isset($id) && $id != 0){
								if(isset($formDataArr['formdata']['id']) && $formDataArr['formdata']['id'] == ''){
									global $to;	
									global $password;	
									$full_name = $formDataArr['formdata']['firstname'].' '.$formDataArr['formdata']['lastname'];
									$data											=   array(
																							'user_name'					=>  $formDataArr['formdata']['email'],
																							'password'					=>  $password,
																							'full_name'					=>  $full_name
																						);
									$to												=	$formDataArr['formdata']['email'];
									Mail::send('emails.newuser',$data,function($message){									
										global $to;
										$sunday_confg								=   Config::get('sundayschool');
										$from				                        =	$sunday_confg['mail']['from'];
										$subject			                        =	$sunday_confg['mail']['new_account'];
										$message->from($from);                    
										$message->to($to);
										//$message->cc('dillip.bestrayinfotech@gmail.com');
										$message->subject($subject);
									});
								}
								DB::commit();
								echo '****SUCCESS****User data saved successfully.';
							}else{
								DB::rollback();
								echo '****ERROR****Unable to create user.';
							}
						}
					}
				}else{
					echo '****ERROR****Invalid form submission.';	
				}			
			}else{
				echo '****ERROR****Please login to register.';
			}exit;
		}
        /**
		* Used for listing of all users
		* 
		* @param void
		* @return response
		*/
        public function getUserlistingajax() {		
			$this->layout									=   View::make('layouts.ajax');
			$inputArr										=	Input::all();			
			$user_id										=	'';	
			$user_email										=	'';
			$temple_account_no								=	'';
			$role_id								=	'';
			$query											=   DB::table('users')
																	->select(array('users.id','users.firstname','users.lastname','users.email','users.temple_account_no','users.role_id','users.is_active','users.expired_date'));
			if(isset($inputArr['user_id']) && $inputArr['user_id'] != ''){
				$user_id									=	$inputArr['user_id'];
				$query->where('users.id','=',$user_id);
			}
			if(isset($inputArr['user_email']) && $inputArr['user_email'] != ''){
				$user_email									=	$inputArr['user_email'];
				$query->where('users.email','LIKE',"%".$user_email."%");
			}
			if(isset($inputArr['temple_acc_no']) && $inputArr['temple_acc_no'] != ''){
				$temple_account_no							=	$inputArr['temple_acc_no'];
				$query->where('users.temple_account_no','=',$temple_account_no);
			}
			if(isset($inputArr['role_id']) && $inputArr['role_id'] != ''){
				$role_id							=	$inputArr['role_id'];
				$query->where('users.role_id','=',$role_id);
			}
            $custompaginatorres								=   $query->orderBy('users.id','desc')->paginate('10');
			$layoutArr										=	array(
																	'sortFilterArr'				=>	array(
																										'user_id'						=>	$user_id,
																										'user_email'					=>	$user_email,
																										'temple_account_no'				=>	$temple_account_no,
																										'role_id'			=>	$role_id,
																									),
																	'custompaginatorres'		=>	$custompaginatorres
																);
            $this->layout->content							=   View::make('users.userlistingajax',array('layoutArr'=>$layoutArr));			
        }
		/**
		* Used for showing profile info
		* 
		* @param void
		* @return void
		*/
		public function getProfile(){
			$this->layout									=   View::make('layouts.admin');
			$userArr										=	Auth::user();			
			$layoutArr										=	array(
																	'userArr'					=>	$userArr
																);
            $this->layout->content							=   View::make('users.profile',array('layoutArr'=>$layoutArr));
		}
		/**
		* Used for showing profile info ajax version
		* 
		* @param void
		* @return void
		*/
		public function getProfileinfo(){
			$this->layout									=   View::make('layouts.ajax');
			$userArr										=	Auth::user();			
			$layoutArr										=	array(
																	'userArr'					=>	$userArr
																);
            $this->layout->content							=   View::make('users.profileinfo',array('layoutArr'=>$layoutArr));
		}
		/**
		* Used for changing usesr password
		* 
		* @param void
		* @return void
		*/
		public function postChangepassword(){
			$valiationArr									=   array();
			if(isset(Auth::user()->id) && Auth::user()->id){
				$formData									=   Input::all();				
				$formDataArr								=   array();
				if(isset($formData['formdata']) && $formData['formdata'] != ''){ 
					parse_str($formData['formdata'],$formDataArr);					
					if(isset($formDataArr['formdata']) && is_array($formDataArr['formdata']) && count($formDataArr['formdata']) > 0){
						$validator							=   Validator::make($formDataArr['formdata'],User::$rules['changepassword']);										
						if ($validator->fails()){
							$errorArr   =   $validator->getMessageBag()->toArray();
							if(isset($errorArr) && is_array($errorArr) && count($errorArr) > 0){
								foreach($errorArr as $errorKey=>$errorVal){
									$valiationArr[]     	=   array(
																	'modelField'            =>  $errorKey,
																	'modelErrorMsg'         =>  $errorVal[0],
																);
								}
							}
						}
						if(isset($formDataArr['formdata']['old_password']) && $formDataArr['formdata']['old_password'] != ''){							
							$user 							= 	Auth::user();
							if(!(Hash::check($formDataArr['formdata']['old_password'],$user->getAuthPassword()))) {
								$valiationArr[]				=		array(
																		'modelField'            =>  'old_password',
																		'modelErrorMsg'         =>  'Old Password is incorrect.',
																	);
							}							
						}
						if(is_array($valiationArr) && count($valiationArr) > 0){
							echo '****FAILURE****'.json_encode($valiationArr);  
						}else{
							DB::beginTransaction();
							if(isset(Auth::user()->id) && Auth::user()->id != 0){
								$formDataArr['user']['updated_at']				=	date('Y-m-d h:i:s');
								$formDataArr['user']['password']				=	Hash::make($formDataArr['formdata']['new_password']);
								try {
									DB::table('users')
										->where('id',Auth::user()->id)
										->update($formDataArr['user']);
									DB::commit();
									echo '****SUCCESS****User password has been changed successfully.';
								} catch(ValidationException $e){
									DB::rollback();
									echo '****ERROR****Unable to change user password.';
								} catch(\Exception $e){
									DB::rollback();
									echo '****ERROR****Unable to change user password.';
								}
							}
						}
					}
				}else{
					echo '****ERROR****Invalid form submission.';	
				}			
			}else{
				echo '****ERROR****Please login to register.';
			}exit;
		}
		/**
		* Used for changing usesr password
		* 
		* @param void
		* @return void
		*/
		public function postSendresetpasswordlink(){
			$valiationArr									=   array();
			$formData										=   Input::all();				
			$formDataArr									=   array();
			if(isset($formData['formdata']) && $formData['formdata'] != ''){ 
				parse_str($formData['formdata'],$formDataArr);				
				if(isset($formDataArr['formdata']) && is_array($formDataArr['formdata']) && count($formDataArr['formdata']) > 0){
					$validator								=   Validator::make($formDataArr['formdata'],User::$rules['resetpasswordemail'],User::$messages);										
					if ($validator->fails()){
						$errorArr   =   $validator->getMessageBag()->toArray();
						if(isset($errorArr) && is_array($errorArr) && count($errorArr) > 0){
							foreach($errorArr as $errorKey=>$errorVal){
								$valiationArr[]				=   array(
																	'modelField'            =>  $errorKey,
																	'modelErrorMsg'         =>  $errorVal[0],
																);
							}
						}
					}else{
						if(isset($formDataArr['formdata']['email_forgot']) && $formDataArr['formdata']['email_forgot'] != ''){
							$userArr						=   DB::table('users')
																	->where('users.email','=',$formDataArr['formdata']['email_forgot'])
																	->select(array('users.id'))
																	->first();
							if(!is_object($userArr)){
								$valiationArr[]				=	array(
																	'modelField'            =>  'email_forgot',
																	'modelErrorMsg'         =>  'This email does not exist.',
																);
							}					
						}
					}					
					if(is_array($valiationArr) && count($valiationArr) > 0){
						echo '****FAILURE****'.json_encode($valiationArr);  
					}else{
						DB::beginTransaction();						
						if(isset($userArr->id) && $userArr->id != 0){
							global $to;
							$to												=	$formDataArr['formdata']['email_forgot'];
							$formDataArr['user']['updated_at']				=	date('Y-m-d h:i:s');
							$formDataArr['user']['remember_token']			=	csrf_token();
							$formDataArr['user']['is_reset_req']			=	'Y';
							try {
								DB::table('users')
									->where('id',$userArr->id)
									->update($formDataArr['user']);
								$data										=   array(
																					'csrf_token'					=>  csrf_token(),
																					'id'							=>  $userArr->id,
																				);						
								Mail::send('emails.resetpassword',$data,function($message){									
									global $to;
									$sunday_confg							=   Config::get('sundayschool');
									$from				                    =	$sunday_confg['mail']['from'];
									$subject			                    =	$sunday_confg['mail']['reset_password'];
									$message->from($from);                    
									$message->to($to);
									//$message->cc('dillip.bestrayinfotech@gmail.com');
									$message->subject($subject);
								});
								DB::commit();
								echo '****SUCCESS****User password sent successfully.';
							} catch(ValidationException $e){
								DB::rollback();
								echo '****ERROR****Unable to sent email.';
							} catch(\Exception $e){
								DB::rollback();
								throw $e;
								echo '****ERROR****Unable to sent.';
							}
						}
					}
				}
			}else{
				echo '****ERROR****Invalid form submission.';	
			}exit;
		}
		/**
		* Used for resetting usesr password
		* after user getting the reset email
		* 
		* @param void
		* @return void
		*/
		public function getResetpassword($token = '',$id = 0){
			$this->layout									=   View::make('layouts.home');
			$error											=	'';
			if($token != '' && (int)$id != 0){
				$userCnt									=   DB::table('users')
																	->where('users.id','=',(int)$id)
																	->where('users.is_reset_req','=','Y')
																	->where('users.remember_token','=',$token)																	
																	->count();
				if($userCnt == 0){
					$error									=	'This link is invalid.';
				}
			}	
			$layoutArr										=	array(
																	'token'		=>	$token,
																	'id'		=>	$id,
																	'error'		=>	$error
																);
            $this->layout->content							=   View::make('users.resetpassword',array('layoutArr'=>$layoutArr));			
		}
		/**
		* Managing reset password
		* 
		* @param void
		* @return void
		*/
		public function postResetpassword(){
			$valiationArr									=   array();
			$formData										=   Input::all();				
			$formDataArr									=   array();
			
			if(isset($formData['formdata']) && $formData['formdata'] != ''){ 
				parse_str($formData['formdata'],$formDataArr);				
				if(isset($formDataArr['formdata']) && is_array($formDataArr['formdata']) && count($formDataArr['formdata']) > 0){
					$validator								=   Validator::make($formDataArr['formdata'],User::$rules['resetpassword'],User::$messages);										
					if ($validator->fails()){
						$errorArr   =   $validator->getMessageBag()->toArray();
						if(isset($errorArr) && is_array($errorArr) && count($errorArr) > 0){
							foreach($errorArr as $errorKey=>$errorVal){
								$valiationArr[]				=   array(
																	'modelField'            =>  $errorKey,
																	'modelErrorMsg'         =>  $errorVal[0],
																);
							}
						}
					}else{
						if(isset($formDataArr['formdata']['id']) && $formDataArr['formdata']['id'] != ''){
							$userCnt						=   DB::table('users')
																	->where('users.id','=',$formDataArr['formdata']['id'])
																	->where('users.is_reset_req','=','Y')
																	->where('users.remember_token','=',$formDataArr['formdata']['token'])																	
																	->count();
							if($userCnt == 0){
								echo '****ERROR****This link is invalid';
							}					
						}
					}					
					if(is_array($valiationArr) && count($valiationArr) > 0){
						echo '****FAILURE****'.json_encode($valiationArr);  
					}else{
						DB::beginTransaction();						
						if(isset($userCnt) && $userCnt != 0){
							$formDataArr['user']['password']				=	Hash::make($formDataArr['formdata']['new_password']);
							$formDataArr['user']['updated_at']				=	date('Y-m-d h:i:s');
							$formDataArr['user']['remember_token']			=	'';
							$formDataArr['user']['is_reset_req']			=	'N';							
							try {
								DB::table('users')
									->where('id',$formDataArr['formdata']['id'])
									->update($formDataArr['user']);
								DB::commit();
								echo '****SUCCESS****Password reset successfully.';
							} catch(ValidationException $e){
								DB::rollback();
								echo '****ERROR****Unable to reset password.';
							} catch(\Exception $e){
								DB::rollback();
								echo '****ERROR****Unable to reset password.';
							}
						}
					}
				}
			}else{
				echo '****ERROR****Invalid form submission.';	
			}exit;
		}
		/**
		* Managing user profile
		* 
		* @param void
		* @return void
		*/
		public function postProfile(){
			$valiationArr									=   array();
			if(isset(Auth::user()->id) && Auth::user()->id){
				$formData									=   Input::all();				
				$formDataArr								=   array();
				if(isset($formData['formdata']) && $formData['formdata'] != ''){ 
					parse_str($formData['formdata'],$formDataArr);					
					if(isset($formDataArr['formdata']) && is_array($formDataArr['formdata']) && count($formDataArr['formdata']) > 0){
						$validator							=   Validator::make($formDataArr['formdata'],User::$rules['profile']);										
						if ($validator->fails()){
							$errorArr   =   $validator->getMessageBag()->toArray();
							if(isset($errorArr) && is_array($errorArr) && count($errorArr) > 0){
								foreach($errorArr as $errorKey=>$errorVal){
									$valiationArr[]     	=   array(
																	'modelField'            =>  $errorKey,
																	'modelErrorMsg'         =>  $errorVal[0],
																);
								}
							}
						}
						if(isset($formDataArr['formdata']['old_password']) && $formDataArr['formdata']['old_password'] != ''){							
							$user 							= 	Auth::user();
							if(!(Hash::check($formDataArr['formdata']['old_password'],$user->getAuthPassword()))) {
								$valiationArr[]				=		array(
																		'modelField'            =>  'old_password',
																		'modelErrorMsg'         =>  'Old Password is incorrect.',
																	);
							}							
						}
						if(is_array($valiationArr) && count($valiationArr) > 0){
							echo '****FAILURE****'.json_encode($valiationArr);  
						}else{
							DB::beginTransaction();
							if(isset(Auth::user()->id) && Auth::user()->id != 0){
								$formDataArr['user']['updated_at']				=	date('Y-m-d h:i:s');
								$formDataArr['user']['firstname']				=	$formDataArr['formdata']['firstname'];
								$formDataArr['user']['lastname']				=	$formDataArr['formdata']['lastname'];
								$formDataArr['user']['temple_account_no']		=	$formDataArr['formdata']['temple_account_no'];								
								try {
									DB::table('users')
										->where('id',Auth::user()->id)
										->update($formDataArr['user']);
									DB::commit();
									echo '****SUCCESS****User profile saved successfully.';
								} catch(ValidationException $e){
									DB::rollback();
									echo '****ERROR****Unable save user profile.';
								} catch(\Exception $e){
									DB::rollback();
									echo '****ERROR****Unable save user profile.';
								}
							}
						}
					}
				}else{
					echo '****ERROR****Invalid form submission.';	
				}			
			}else{
				echo '****ERROR****Please login to register.';
			}exit;
		}
    }    
?>