<script>
    var controller				=	'<% $controller %>';
	var action					=	'<% $action %>';
	var csrfTkn					=	'<% csrf_token() %>';
	var baseUrl					=	'<% URL::to('/'); %>';
	var onChangeFunction		=	'';
	var listingUrl				=	'';
	
	function showJsonErrors(errors){	
		if(errors != ''){
			resp = $.parseJSON(errors);
			var totErrorLen = resp.length;	
			for(var errCnt =0;errCnt <totErrorLen;errCnt++){
				var modelField         =   resp[errCnt]['modelField'];
				var modelErrorMsg      =   resp[errCnt]['modelErrorMsg'];
				$('[id="'+modelField+'"]').after('<div class="error-message">'+modelErrorMsg+'</div>'); 
			}
		}
	}
	// Common function load in all page	
	/* used for custom pagination listing
	*  in order fetch listing results when
	*  user click on the pagination link
	*/
	function goToCurPage(obj){
		$('#loddingImage').show();	
		$.ajax({
			url: $(obj).attr('href'),
			type: 'get',
			success:function(res){
				$('#listingTable').html(res);
				$('#loddingImage').hide();
				ajaxCompleteFunc();
			}
		});
		return false;
	}
	/*
	* Group Delete confirmation
	*/
   function checkConfirmation(){
	   if(confirm("Are you sure to Delete ?")){
		   return true;
	   }else{
		   return false;
	   }
   }
   /*
	* Delete of Group
	*/
   function deleteFrm(master_id){
	   if (checkConfirmation()) {
		   var formId = 'deleteFrm'+master_id;
		   document.forms[formId].submit();
		   showBlockUI();
	   }		
   }
   /*
	* Block UI
	*/
	$(document).submit(function() {
		/*var isBlock = true;
		if(document.getElementById('pageUI_BlockStatus')){	
			var pageBlockStatus = $('#pageUI_BlockStatus').val();
			if(pageBlockStatus == 1){
				isBlock	= false;    
			}
		}
		if(isBlock){
			showBlockUI();
		}else{
			setTimeout('allowBlock',5);
		}*/showBlockUI();		
	});//showBlockUI();
	function showBlockUI(){
		$.blockUI({
				message: '<div class="blockUiDv">Please wait...</div>',
				css: { 
					border: 'none', 
					padding:'10px 0',
					backgroundColor: '#FFFFFF',				
					'border-radius': '10px',
					'font-size': '20px',
					opacity: 1, 
					color: '#000000'
				}
			});
	}
	function allowBlock(){
		$('#pageUI_BlockStatus').val('0');    
	}
	function showBlockDv(){
		$('.blockUiDv').block({ 
			message: null,
			css: { 
				border: '1 px', 
				'border-color':'#e7ebee',				
				'border-radius': '3px',
				opacity: 1,
			} 
		});
	}
	function unBlockDv(){
		$('.blockUiDv').unblock(); 
	}
	function capitaliseFirstLetter(str){
		return str.charAt(0).toUpperCase() + str.slice(1);
	}
	@if($action == 'getStudent' || $action == 'getPendingstudentlist' || $action == 'getSummercampstudent' || $action == 'getPendingsummercamp' || $action == 'getUploadclassfile' || $action == 'getClass')
		function getClassList(class_type_id){			
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});        
			$.ajax({
				url:baseUrl+'/auth/classlist',
				type: 'post',
				cache: false,
				data:{
					'class_type_id':class_type_id,							
					'on_change_function_name':'',
				},
				success: function(res) {
					$('#classResponseDv').html(res);
					@if(isset($class_id) && $class_id != '')
						$('#class_id').val(<% $class_id %>);
					@endif
				},
				error: function(xhr, textStatus, thrownError) {
					unBlockDv();
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
	@endif
	//Common function for all page
	@if($action == 'getStudent' || $action == 'getPendingstudentlist')
		var autoCplUrl		=	baseUrl+'/auth/studentautocpl';
		var is_approved			=	'Y';
		@if($action == 'getPendingstudentlist')
			is_approved			=	'N';
		@endif		
		$(document).ready(function(){
			$('#studetname').typeahead({				
				source: function (q, process) {
					return $.get(autoCplUrl, {
						term: q,
						is_approved: is_approved
					}, function (response) {
						var data = [];
						if(response != ''){
							var resp			=	$.parseJSON(response);
							var totErrorLen		=	resp.length;					
							for(var i =0;i < totErrorLen;i++){
								data.push(resp[i]['id'] + "#" + resp[i]['first_name'] + "#" + resp[i]['last_name'] + "#" + resp[i]['father_name']);
							}
						}						
						return process(data);
					});
				},
				highlighter: function (item) {
					var parts	=	item.split('#'),
					html		=	'<div class="typeahead">';
					//html		+=	'<div class="pull-left"><img src="img/' + parts[2] + '" width="32" height="32" class="img-rounded"></div>';
					html		+=	'<div class="pull-left margin-small">';
					html		+=	'<div class="text-left"><strong>' + parts[1] +" "+ parts[2] + '</strong></div>';
					html		+=	'<div class="text-left">' + parts[3] + '</div>';
					html		+=	'</div>';
					html		+=	'<div class="clearfix"></div>';
					html		+=	'</div>';
					return html;
				},
				updater: function (item) {
					var parts	=	item.split('#');
					$("#student_id").val(parts[0]);
					return parts[1]+" "+parts[2];
				},
			});
			showFileUpload();
		});		
		function resetFileUpload(){
			$('#fileUploadDv').hide();
			$('#photo_name').val('');
			$('#photo_size').val('');
			$('.file-preview').hide();
			$('#photo').fileinput('reset');
			$('#photo_selected_cnt').val('');
		}
		function showFileUpload(){
			$('#fileUploadDv').show();
		}
	@endif
	//Common function for all page
	@if($action == 'getSummercampstudent' || $action == 'getPendingsummercamp')
		var autoCplUrl		=	baseUrl+'/auth/summercampautocpl';
		var is_approved		=	'Y';
		@if($action == 'getPendingsummercamp')
			is_approved		=	'N';
		@endif		
		$(document).ready(function(){
			$('#studetname').typeahead({				
				source: function (q, process) {
					return $.get(autoCplUrl, {
						term: q,
						is_approved: is_approved
					}, function (response) {
						var data = [];
						if(response != ''){
							var resp			=	$.parseJSON(response);
							var totErrorLen		=	resp.length;					
							for(var i =0;i < totErrorLen;i++){
								data.push(resp[i]['id'] + "#" + resp[i]['first_name'] + "#" + resp[i]['last_name'] + "#" + resp[i]['father_name']);
							}
						}						
						return process(data);
					});
				},
				highlighter: function (item) {
					var parts	=	item.split('#'),
					html		=	'<div class="typeahead">';
					//html		+=	'<div class="pull-left"><img src="img/' + parts[2] + '" width="32" height="32" class="img-rounded"></div>';
					html		+=	'<div class="pull-left margin-small">';
					html		+=	'<div class="text-left"><strong>' + parts[1] +" "+ parts[2] + '</strong></div>';
					html		+=	'<div class="text-left">' + parts[3] + '</div>';
					html		+=	'</div>';
					html		+=	'<div class="clearfix"></div>';
					html		+=	'</div>';
					return html;
				},
				updater: function (item) {
					var parts	=	item.split('#');
					$("#student_id").val(parts[0]);
					return parts[1]+" "+parts[2];
				},
			});			
		});						
	@endif
	@if($action == 'getStudent')
		var photo_selected_cnt	=	0;
		var photo_name			=	'';
		var photo_size			=	'';
		function validateForm(){
			$('.frmbtngroup').prop('disabled',true);			
			$('#loddingImage').show();
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});
			$.ajax({
				url:baseUrl+'/auth/validateform',
				type: 'post',
				cache: false,					
				data:{
					"formdata": $('#entryFrm').serialize(),
				},
				success: function(res){		
					$('.error-message').remove();
					var resp		=   res.split('****');
					if(resp[1] == 'FAILURE'){
						$('.frmbtngroup').prop('disabled',false);
						$('#loddingImage').hide();
					   showJsonErrors(resp[2]);
					}else if(resp[1] == 'SUCCESS'){
						if(photo_selected_cnt == 0){
							submitForm();
						}else{
							$('.kv-fileinput-upload').click();
						}					
					}			
				},
				error: function(xhr, textStatus, thrownError) {
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}		
		function submitForm(){
			document.forms['entryFrm'].submit();
		}
		$("#photo").fileinput({        
			uploadUrl: baseUrl+'/auth/uploadimage',
			//dropZoneEnabled:true,
			dropZoneTitle:'',
			//showUpload: false,
			//showCaption: false,
			showRemove:false,
			showCancel:false,
			//uploadAsync:true,
			maxFileSize:2048,
			maxFileCount: 1,
			allowedFileExtensions : ['jpg', 'png'],    
			elErrorContainer:'#file_error',
			previewSettings:{
				image: {width: "80px", height: "55px"}
			},
			uploadExtraData: {
				'X-CSRF-Token': csrfTkn,
				'type':''
			},
		});
		$('#photo').on('fileloaded', function(event, file, previewId, index, reader) {
			$('.file-preview').show();
			photo_selected_cnt++;
			$('#photo_selected_cnt').val(photo_selected_cnt);			
		});
		$('#photo').on('filecleared', function(event) {
			$('.file-preview').hide();
			$('#photo_selected_cnt').val('');
		});
		$('#photo').on('fileuploaded', function(event, data, previewId, index) {               
			response        =   data.response, reader = data.reader;
			var respPart    =   response.success.split('@');
			photo_name	    +=	respPart[0]+',';
			photo_size	    +=	respPart[1]+',';
			setTimeout(function(){$('.kv-upload-progress').hide('slow');},5000);
		});
		$('#photo').on('filebatchuploadcomplete', function(event, data, previewId, index) {               
			$('#photo_name').val(photo_name);
			$('#photo_size').val(photo_size);
			photo_selected_cnt = 0;
			submitForm();
		});		
	@endif
	@if($action == 'getTransactionlist' || $action == 'getParentdetail')
		@if($action == 'getTransactionlist')
			$(document).ready(function() {
				$(".datepkr").datepicker({
							format: 'mm/dd/yyyy',
							autoclose:true,
							endDate:new Date()
				});
				$(".datepkrNoRestrict").datepicker({
							format: 'mm/dd/yyyy',
							autoclose:true,
							//endDate:new Date()
				});
			});
		@endif
		var autoCplUrl		=	baseUrl+'/auth/parentautocpl';		
		$(document).ready(function(){
			$('#email').typeahead({
				source: function (q, process) {
					return $.get(autoCplUrl, {
						term: q,						
					}, function (response) {
						var data = [];
						if(response != ''){
							var resp			=	$.parseJSON(response);
							var totErrorLen		=	resp.length;					
							for(var i =0;i < totErrorLen;i++){
								data.push(resp[i]['id'] + "#" + resp[i]['email']);
							}
						}						
						return process(data);
					});
				},
				split: /(\,?\s)/,
				highlighter: function (item) {
					var parts	=	item.split('#'),
					html		=	'<div class="typeahead">';
					//html		+=	'<div class="pull-left"><img src="img/' + parts[2] + '" width="32" height="32" class="img-rounded"></div>';
					html		+=	'<div class="pull-left margin-small">';
							html		+=	'<div class="text-left"><strong>' + parts[1] + '</strong></div>';
							//html		+=	'<div class="text-left">' + parts[3] + '</div>';
					html		+=	'</div>';
					html		+=	'<div class="clearfix"></div>';
					html		+=	'</div>';
					return html;
				},
				updater: function (item) {
					var parts	=	item.split('#');
					$("#user_id").val(parts[0]);
					return parts[1];
				},
			});
		});		
	@endif
	@if($action == 'getRolemenu')
		function getDesignationWiseMenu(role_id){
			$('#loddingImage').show();
			$.ajax({
				url:baseUrl+'/master/designationwisemenu',
				type: 'get',
				cache: false,
				//dataType: 'json',
				data:{
					'role_id':role_id
				},
				success: function(res) {
					$('#listingTable').html(res);
					$('#loddingImage').hide();
				},
				error: function(xhr, textStatus, thrownError) {
					$('#loddingImage').hide();
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
	@endif	
	@if($action == 'getClass')
		function copyClassName(){ //alert(1);
			var key=$("#class_name").val();
			//key=key.replace(" ","_");
			key=key.replace(/ /g,"-");
			$("#class_slug").val(key);
			
		}
		var autoCplUrl		=	baseUrl+'/auth/teacherautocpl';
		$(document).ready(function(){
			$('#teacher_id_autocpl').typeahead({
				source: function (q, process) {
					return $.get(autoCplUrl, {
						term: q
					}, function (response) {
						var data = [];
						if(response != ''){
							var resp			=	$.parseJSON(response);
							var totErrorLen		=	resp.length;					
							for(var i =0;i < totErrorLen;i++){
								data.push(resp[i]['id'] + "#" + resp[i]['teacher_name'] + "#" + resp[i]['email']);
							}
						}						
						return process(data);
					});
				},
				highlighter: function (item) {
					var parts	=	item.split('#'),
					html		=	'<div class="typeahead">';
					//html		+=	'<div class="pull-left"><img src="img/' + parts[2] + '" width="32" height="32" class="img-rounded"></div>';
					html		+=	'<div class="pull-left margin-small">';
					html		+=	'<div class="text-left"><strong>' + parts[1] + '</strong></div>';
					html		+=	'<div class="text-left">' + parts[2] + '</div>';
					html		+=	'</div>';
					html		+=	'<div class="clearfix"></div>';
					html		+=	'</div>';
					return html;
				},
				updater: function (item) {
					var parts	=	item.split('#');
					$("#teacher_id").val(parts[0]);
					return parts[1];
				},
			});			
		});
	@endif
	@if($action == 'getCalenderlist')
		$(document).ready(function() {
			$(".datepkr").datepicker({
						format: 'mm/dd/yyyy',
						autoclose:true,
						endDate:new Date()
			});
			$(".datepkrNoRestrict").datepicker({
						format: 'mm/dd/yyyy',
						autoclose:true,
						//endDate:new Date()
			});
		});
		function calenderPopup(calObj){//alert(calObj.format('YYYY/MM/DD HH:mm:ss'))
			$('#calenderid').val('');
			resetFormVal('entryFrm',0);
			$('.error-message').remove();
			$('#sucMsgDiv').hide('slow');
			$('#failMsgDiv').hide('slow');
			$('#loddingImage').hide();
			$('#failMsgDiv').addClass('text-none');
			$('#sucMsgDiv').addClass('text-none');
			var start_date				=	'';
			var start_time				=	'';
			var end_date				=	'';
			var end_time				=	'';
			if(calObj.format()){				
				start_date					=	calObj.format('MM/DD/YYYY');
				end_date					=	calObj.format('MM/DD/YYYY');
				$('#calender_end_date_hidden').val(calObj.format('YYYY-MM-DD'));
				if(calObj.hasTime()){
					start_time				=	calObj.format('YYYY-MM-DD HH:mm:ss');
					end_time				=	calObj.format('YYYY-MM-DD HH:mm:ss');
				}
			}			
			$('#start_date').val(start_date);
			$('#end_date').val(end_date);
			$('#start_time').val(start_time);
			$('#end_time').val(end_time);			
			$('#calenderPopup').modal('show');
		}
		function dragResizeSave(calObj){
			$('#loddingImage').show();
			var start_date				=	'';
			var start_time				=	'';
			var end_date				=	'';
			var end_time				=	'';
			if(calObj.start){				
				start_date					=	calObj.start.format('MM/DD/YYYY');	
				$('#calender_end_date_hidden').val(calObj.start.format('YYYY-MM-DD'));
				if(calObj.start.hasTime()){
					start_time				=	calObj.start.format('YYYY-MM-DD HH:mm:ss');					
				}
			}
			if(calObj.end){
				end_date					=	calObj.end.format('MM/DD/YYYY');
				$('#calender_end_date_hidden').val(calObj.end.format('YYYY-MM-DD'));
				if(calObj.end.hasTime()){
					end_time				=	calObj.end.format('YYYY-MM-DD HH:mm:ss');
				}
			}else{
				end_date					=	start_date;
				end_time					=	start_time;
			}			
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});
			$.ajax({
				url:baseUrl+'/calender/dragresizesave',
				type: 'post',
				cache: false,					
				data:{
					"id": calObj.id,
					"event_title": calObj.title,
					"event_description": calObj.event_description,
					"start_date": start_date,
					"end_date": end_date,
					"start_time": start_time,
					"end_time": end_time,
				},
				success: function(res){
					$('#loddingImage').hide();
					var resp		=   res.split('****');
					if(resp[1] == 'SUCCESS'){
						showData('calender');
					}else if(resp[1] == 'FAILURE'){
																							
					}else if(resp[1] == 'ERROR'){
						$('#failMsgDiv').removeClass('text-none');
						$('.failmsgdiv').html(resp[2]);
						$('#failMsgDiv').show('slow');
					}		
				},
				error: function(xhr, textStatus, thrownError) {
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		function saveCalenderEvent(){
			$('.frmbtngroup').prop('disabled',true);			
			$('#loddingImage').show();
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});
			$.ajax({
				url:baseUrl+'/calender/savecalenderevent',
				type: 'post',
				cache: false,					
				data:{
					"formdata": $('#entryFrm').serialize(),
				},
				success: function(res){	
					$('.frmbtngroup').prop('disabled',false);
					$('.error-message').remove();
					$('#sucMsgDiv').hide('slow');
					$('#failMsgDiv').hide('slow');
					$('#loddingImage').hide();
					$('#failMsgDiv').addClass('text-none');
					$('#sucMsgDiv').addClass('text-none');
					var resp		=   res.split('****');
					if(resp[1] == 'SUCCESS'){						
						$('#sucMsgDiv').removeClass('text-none');
						$('.sucmsgdiv').html(resp[2]);
						$('#sucMsgDiv').show('slow');
						showData('calender');						
						$('#calenderPopup').modal('hide');
						resetFormVal('entryFrm',0);
					}else if(resp[1] == 'FAILURE'){
						showJsonErrors(resp[2]);																		
					}else if(resp[1] == 'ERROR'){
						$('#failMsgDiv').removeClass('text-none');
						$('.failmsgdiv').html(resp[2]);
						$('#failMsgDiv').show('slow');
					}		
				},
				error: function(xhr, textStatus, thrownError) {
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		function deleteCalenderEvent(){
			if(confirm('Are you sure to delete ?')){
				$('.frmbtngroup').prop('disabled',true);			
				$('#loddingImage').show();
				$.ajaxSetup({
					headers: {
						'X-CSRF-Token': csrfTkn
					}
				});
				$.ajax({
					url:baseUrl+'/calender/deletecalenderevent',
					type: 'post',
					cache: false,					
					data:{
						"id": $('#calenderid').val(),
					},
					success: function(res){	
						$('.frmbtngroup').prop('disabled',false);
						$('.error-message').remove();
						$('#sucMsgDiv').hide('slow');
						$('#failMsgDiv').hide('slow');
						$('#loddingImage').hide();
						$('#failMsgDiv').addClass('text-none');
						$('#sucMsgDiv').addClass('text-none');
						var resp		=   res.split('****');
						if(resp[1] == 'SUCCESS'){						
							$('#sucMsgDiv').removeClass('text-none');
							$('.sucmsgdiv').html(resp[2]);
							$('#sucMsgDiv').show('slow');
							showData();						
							$('#calenderPopup').modal('hide');
							resetFormVal('entryFrm',0);
						}else if(resp[1] == 'ERROR'){
							$('#failMsgDiv').removeClass('text-none');
							$('.failmsgdiv').html(resp[2]);
							$('#failMsgDiv').show('slow');
						}		
					},
					error: function(xhr, textStatus, thrownError) {
						//alert('Something went to wrong.Please Try again later...');
					}
				});
			}			
		}
	@endif
	
	function fetchStudentDetailAjax(user_id){
		$('#loddingImage').show();
		$('.childlisttr').remove();
		$.ajax({
			url:baseUrl+'/parent/fetchstudentdetailajax',
			type: 'get',
			cache: false,
			data:{
				'user_id':user_id,	
			},
			success: function(res) {					
				$('#loddingImage').hide();
				if($('.childlisttr').length == 0){
					$('#childlistajaxTr'+user_id).after(res);
					$('.childlisttr').show('slow');
					ajaxCompleteFunc();					
				}					
			},
			error: function(xhr, textStatus, thrownError) {
				$('#loddingImage').hide();
				//alert('Something went to wrong.Please Try again later...');
			}
		});
	}
	
	@if($action == 'getTransactionlist')
		function payerDetailAjax(transaction_detail_id){
			$('#loddingImage').show();
			$('.childlisttr').remove();
			$.ajax({
				url:baseUrl+'/master/payerdetailajax',
				type: 'get',
				cache: false,
				data:{
					'transaction_detail_id':transaction_detail_id,	
				},
				success: function(res) {					
					$('#loddingImage').hide();
					$('#childlistajaxTr'+transaction_detail_id).after(res);
					$('.childlisttr').show('slow');
				},
				error: function(xhr, textStatus, thrownError) {
					$('#loddingImage').hide();
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
	@endif
	@if($action == 'getPendingstudentlist')		
		function fetchPendingStudentDetail(student_id){
			$('#loddingImage').show();
			$('.childlisttr').remove();
			$.ajax({
				url:baseUrl+'/master/pendingenrolstudetnlistajax',
				type: 'get',
				cache: false,
				data:{
					'student_id':student_id,	
				},
				success: function(res) {
					$('#loddingImage').hide();
					$('#childlistajaxTr'+student_id).after(res);
					$('.childlisttr').show('slow');
				},
				error: function(xhr, textStatus, thrownError) {
					$('#loddingImage').hide();
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		function showPopupWindow(student_id_enrol,class_id_enrol){			
			$('#student_id_enrol').val(student_id_enrol);
			$('#class_id_enrol').val(class_id_enrol);
			$('#ganeshTemplePopup').modal('show');
		}
		function callApproveStudentEnrol(){			
			var student_id_enrol	=	$('#student_id_enrol').val();
			var class_id_enrol		=	$('#class_id_enrol').val();
			var trans_id			=	$('#trans_id').val();
			if(trans_id){
				$('.frmbtngroup').prop('disabled',true);			
				$('#loddingImage').show();
				approveStudentEnrol(student_id_enrol,class_id_enrol,trans_id);
			}else{
				alert('Please enter Transaction No.');
			}			
		}
		function approveStudentEnrol(student_id,class_id,trans_id){
			$('#loddingImage').show();
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});        
			$.ajax({
				url:baseUrl+'/auth/approvestudentenrol',
				type: 'post',
				cache: false,
				data:{
					'student_id':student_id,
					'class_id':class_id,
					'trans_id':trans_id,
				},
				success: function(res) {
					$('#loddingImage').hide();
					$('.frmbtngroup').prop('disabled',false);
					$('.error-message').remove();					
					var resp    =   res.split('****');				
					if(resp[1] == 'FAILURE'){
						alert(resp[2]);
					}else if(resp[1] == 'SUCCESS'){
						$('#trans_id').val('');
						$('#class_id_enrol').val('');
						$('#student_id_enrol').val('');
						showData();
						alert(resp[2]);
						$('#ganeshTemplePopup').modal('hide');
					}
				},
				error: function(xhr, textStatus, thrownError) {
					unBlockDv();
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		function rejectStudentEnrol(student_id,class_id,trans_id){
			$('#loddingImage').show();
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});        
			$.ajax({
				url:baseUrl+'/auth/rejectstudentenrol',
				type: 'post',
				cache: false,
				data:{
					'student_id':student_id,
					'class_id':class_id,
					'trans_id':trans_id,
				},
				success: function(res) {
					$('#loddingImage').hide();
					$('.frmbtngroup').prop('disabled',false);
					$('.error-message').remove();					
					var resp    =   res.split('****');				
					if(resp[1] == 'FAILURE'){
						alert(resp[2]);
					}else if(resp[1] == 'SUCCESS'){
						$('#trans_id').val('');
						$('#class_id_enrol').val('');
						$('#student_id_enrol').val('');
						showData();
						alert(resp[2]);
					}
				},
				error: function(xhr, textStatus, thrownError) {
					unBlockDv();
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
	@endif
	@if($action == 'getPendingsummercamp')
		function showPopupWindowSmrcmp(student_id_enrol,class_id_enrol){			
			$('#student_id_enrol').val(student_id_enrol);
			$('#class_id_enrol').val(class_id_enrol);
			$('#ganeshTemplePopup').modal('show');
		}
		function callApproveStudentSmrcmp(){			
			var student_id_enrol	=	$('#student_id_enrol').val();
			var class_id_enrol		=	$('#class_id_enrol').val();
			var trans_id			=	$('#trans_id').val();
			if(trans_id){
				$('.frmbtngroup').prop('disabled',true);			
				$('#loddingImage').show();
				approveStudentSmrcmp(student_id_enrol,class_id_enrol,trans_id);
			}else{
				alert('Please enter Transaction No.');
			}			
		}
		function approveStudentSmrcmp(student_id,class_id,trans_id){
			$('#loddingImage').show();
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});        
			$.ajax({
				url:baseUrl+'/auth/approvestudentsmrcmp',
				type: 'post',
				cache: false,
				data:{
					'student_id':student_id,
					'class_id':class_id,
					'trans_id':trans_id,
				},
				success: function(res) {
					$('#loddingImage').hide();
					$('.frmbtngroup').prop('disabled',false);
					$('.error-message').remove();					
					var resp    =   res.split('****');				
					if(resp[1] == 'FAILURE'){
						alert(resp[2]);
					}else if(resp[1] == 'SUCCESS'){
						$('#trans_id').val('');
						$('#class_id_enrol').val('');
						$('#student_id_enrol').val('');
						showData();
						alert(resp[2]);
						$('#ganeshTemplePopup').modal('hide');
					}
				},
				error: function(xhr, textStatus, thrownError) {
					unBlockDv();
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		function rejectStudentSumrCmp(student_id,class_id,trans_id){
			$('#loddingImage').show();
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});        
			$.ajax({
				url:baseUrl+'/auth/rejectstudentsmrcmp',
				type: 'post',
				cache: false,
				data:{
					'student_id':student_id,
					'class_id':class_id,
					'trans_id':trans_id,
				},
				success: function(res) {
					$('#loddingImage').hide();
					$('.frmbtngroup').prop('disabled',false);
					$('.error-message').remove();					
					var resp    =   res.split('****');				
					if(resp[1] == 'FAILURE'){
						alert(resp[2]);
					}else if(resp[1] == 'SUCCESS'){
						$('#trans_id').val('');
						$('#class_id_enrol').val('');
						$('#student_id_enrol').val('');
						showData();
						alert(resp[2]);
					}
				},
				error: function(xhr, textStatus, thrownError) {
					unBlockDv();
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
	@endif
	@if($action == 'getCreateuser')		
		function manageUser(){
			$('.frmbtngroup').prop('disabled',true);			
			$('#loddingImage').show();
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});
			$.ajax({
				url:baseUrl+'/users/manageuser',
				type: 'post',
				cache: false,					
				data:{
					"formdata": $('#entryFrm').serialize(),
				},
				success: function(res){	
					$('.frmbtngroup').prop('disabled',false);
					$('.error-message').remove();
					$('#sucMsgDiv').hide('slow');
					$('#failMsgDiv').hide('slow');
					$('#loddingImage').hide();
					var resp		=   res.split('****');
					if(resp[1] == 'SUCCESS'){
						resetFormVal('entryFrm',0);
						$('.sucmsgdiv').html(resp[2]);
						$('#sucMsgDiv').show('slow');			
					}else if(resp[1] == 'FAILURE'){
						showJsonErrors(resp[2]);																		
					}else if(resp[1] == 'ERROR'){
						$('.failmsgdiv').html(resp[2]);
						$('#failMsgDiv').show('slow');
					}		
				},
				error: function(xhr, textStatus, thrownError) {
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}		
		function showUserListing(){
			$('#loddingImage').show();
			$.ajax({
				url:listingUrl,
				type: 'get',
				cache: false,
				data:{
					'id':$('#user_id').val(),
					'user_email':$('#user_email').val(),
					'temple_account_no':$('#temple_acc_no').val(),
					'role_id':$('#role_id').val(),
				},
				success: function(res) {
					$('#loddingImage').hide();
					$('#listingTable').html(res);
					$('.editLink').bind("click", function(e){			
						showBlockUI();
					});
					ajaxCompleteFunc();
				},
				error: function(xhr, textStatus, thrownError) {
					$('#loddingImage').hide();
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}		
	@endif
	@if($action == 'getProfile')
		getProfileinfoAjax();
		function changePassword(){
			$('.frmbtngroup').prop('disabled',true);			
			$('#loddingImage').show();
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});
			$.ajax({
				url:baseUrl+'/users/changepassword',
				type: 'post',
				cache: false,					
				data:{
					"formdata": $('#entryFrmChangePassword').serialize(),
				},
				success: function(res){	
					$('.frmbtngroup').prop('disabled',false);
					$('.error-message').remove();
					$('#sucMsgDiv').hide('slow');
					$('#failMsgDiv').hide('slow');
					$('#loddingImage').hide();
					$('#failMsgDiv').addClass('text-none');
					$('#sucMsgDiv').addClass('text-none');
					var resp		=   res.split('****');
					if(resp[1] == 'SUCCESS'){
						resetFormVal('entryFrm',0);
						$('#sucMsgDiv').removeClass('text-none');
						$('.sucmsgdiv').html(resp[2]);
						$('#sucMsgDiv').show('slow');			
					}else if(resp[1] == 'FAILURE'){
						showJsonErrors(resp[2]);																		
					}else if(resp[1] == 'ERROR'){
						$('#failMsgDiv').removeClass('text-none');
						$('.failmsgdiv').html(resp[2]);
						$('#failMsgDiv').show('slow');
					}		
				},
				error: function(xhr, textStatus, thrownError) {
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		function profile(){
			$('.frmbtngroup').prop('disabled',true);			
			$('#loddingImage').show();
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});
			$.ajax({
				url:baseUrl+'/users/profile',
				type: 'post',
				cache: false,					
				data:{
					"formdata": $('#entryFrmProfile').serialize(),
				},
				success: function(res){	
					$('.frmbtngroup').prop('disabled',false);
					$('.error-message').remove();
					$('#sucMsgDiv').hide('slow');
					$('#loddingImage').hide();
					var resp		=   res.split('****');
					if(resp[1] == 'SUCCESS'){
						$('#old_password').val('');
						$('#new_password').val('');
						$('#new_password_confirmation').val('');
						$('.sucmsgdiv').html(resp[2]);
						$('#sucMsgDiv').show('slow');
						getProfileinfoAjax();
					}else if(resp[1] == 'FAILURE'){
						showJsonErrors(resp[2]);																		
					}else if(resp[1] == 'ERROR'){						
						$('.failmsgdiv').html(resp[2]);
						$('#failMsgDiv').show('slow');
					}		
				},
				error: function(xhr, textStatus, thrownError) {
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		function getProfileinfoAjax(){
			listingUrl			=	baseUrl+'/users/profileinfo';
			$.ajax({
				url:listingUrl,
				type: 'get',
				cache: false,			
				success: function(res) {
					$('#loddingImage').hide();
					$('#listingTable').html(res);
					$('.editLink').bind("click", function(e){			
						showBlockUI();
					});
					ajaxCompleteFunc();
				},
				error: function(xhr, textStatus, thrownError) {
					$('#loddingImage').hide();
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
	@endif
	@if($action == 'getSetting')
		function saveSetting(frmId){
			$('.frmbtngroup').prop('disabled',true);			
			$('#loddingImage').show();
			$('.error-message').remove();
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});
			$.ajax({
				url:baseUrl+'/master/savesetting',
				type: 'post',
				cache: false,					
				data:{
					"formdata": $('#'+frmId).serialize(),
				},
				success: function(res){	
					$('.frmbtngroup').prop('disabled',false);					
					$('#sucMsgDiv').hide('slow');
					$('#loddingImage').hide();
					var resp		=   res.split('****');
					if(resp[1] == 'SUCCESS'){												
						$('.sucmsgdiv').html(resp[2]);
						$('#sucMsgDiv').show('slow');			
					}else if(resp[1] == 'FAILURE'){
						showJsonErrors(resp[2]);																		
					}else if(resp[1] == 'ERROR'){						
						$('.failmsgdiv').html(resp[2]);
						$('#failMsgDiv').show('slow');
					}		
				},
				error: function(xhr, textStatus, thrownError) {
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
	@endif
	@if($action == 'getClasstypegraph')
		function getClassTypeGraph(){
			//$('.ajaxresultbtn').prop('disabled',true);
			$('#loddingImage').show();
			var classTypeGraphUrl	=	baseUrl+'/graph/classtypegraphajax';
			$.ajax({
				url:classTypeGraphUrl,
				type: 'get',
				cache: false,
				data:{
					"class_type_id": $('#class_type_id').val(),
					"acad_year": $('#acad_year').val()
				},
				success: function(res) {
					$('.ajaxresultbtn').prop('disabled',false);
					$('#loddingImage').hide();
					$('#listingTable').html(res);
					ajaxCompleteFunc();
				},
				error: function(xhr, textStatus, thrownError) {
					$('#loddingImage').hide();
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		$(document).ready(function(){
			$("#class_type_id_multiselect").multiselect({
				height:"auto",
				header: "Select Class Type",
				/*
				 * Used before closing the multiselect widget
				 * Checking if any check option is checked
				 * then we are storing that value in t_sts_state_id
				 * hidden field other wise we will empty it.
				 */
				beforeclose:function(event, ui){
					if($('#class_type_id_multiselect').val() != ''){
						if($('#class_type_id_multiselect').val() == null){
							$('#class_type_id').val('');	
						}else{
							$('#class_type_id').val($('#class_type_id_multiselect').val());	
						}					
					}
				}
			});
			$("#acad_year_multiselect").multiselect({
				height:"300px",
				header: "Select Academic Year",
				/*
				 * Used before closing the multiselect widget
				 * Checking if any check option is checked
				 * then we are storing that value in t_sts_state_id
				 * hidden field other wise we will empty it.
				 */
				beforeclose:function(event, ui){
					if($('#acad_year_multiselect').val() != ''){
						if($('#acad_year_multiselect').val() == null){
							$('#acad_year').val('');	
						}else{
							$('#acad_year').val($('#acad_year_multiselect').val());	
						}					
					}
				}
			});
		});
	@endif
	@if($action == 'getClassgraph')
		function getClassGraph(){
			//$('.ajaxresultbtn').prop('disabled',true);
			$('#loddingImage').show();
			var classTypeGraphUrl	=	baseUrl+'/graph/classgraphajax';
			$.ajax({
				url:classTypeGraphUrl,
				type: 'get',
				cache: false,
				data:{
					"class_id": $('#class_id').val(),
					"acad_year": $('#acad_year').val()
				},
				success: function(res) {
					$('.ajaxresultbtn').prop('disabled',false);
					$('#loddingImage').hide();
					$('#listingTable').html(res);
					ajaxCompleteFunc();
				},
				error: function(xhr, textStatus, thrownError) {
					$('#loddingImage').hide();
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		function getClassList(class_type_id){			
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});        
			$.ajax({
				url:baseUrl+'/auth/classlist',
				type: 'post',
				cache: false,
				data:{
					'class_type_id':class_type_id,							
					'on_change_function_name':'',
					'type':'multiselect'
				},
				success: function(res) {
					$('#classResponseDv').html(res);
					$("#class_id_multiselect option[value='']").remove();
					$("#class_id_multiselect").multiselect({
						height:"300px",
						header: "Select Class",
						/*
						 * Used before closing the multiselect widget
						 * Checking if any check option is checked
						 * then we are storing that value in t_sts_state_id
						 * hidden field other wise we will empty it.
						 */
						beforeclose:function(event, ui){
							if($('#class_id_multiselect').val() != ''){
								if($('#class_id_multiselect').val() == null){
									$('#class_id').val('');	
								}else{
									$('#class_id').val($('#class_id_multiselect').val());	
								}					
							}
						}
					});
				},
				error: function(xhr, textStatus, thrownError) {
					unBlockDv();
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		$(document).ready(function(){
			$("#class_id_multiselect").multiselect({
				height:"auto",
				header: "Select Class",
				/*
				 * Used before closing the multiselect widget
				 * Checking if any check option is checked
				 * then we are storing that value in t_sts_state_id
				 * hidden field other wise we will empty it.
				 */
				beforeclose:function(event, ui){
					if($('#class_id_multiselect').val() != ''){
						if($('#class_id_multiselect').val() == null){
							$('#class_id').val('');	
						}else{
							$('#class_id').val($('#class_id_multiselect').val());	
						}					
					}
				}
			});
			$("#acad_year_multiselect").multiselect({
				height:"300px",
				header: "Select Academic Year",
				/*
				 * Used before closing the multiselect widget
				 * Checking if any check option is checked
				 * then we are storing that value in t_sts_state_id
				 * hidden field other wise we will empty it.
				 */
				beforeclose:function(event, ui){
					if($('#acad_year_multiselect').val() != ''){
						if($('#acad_year_multiselect').val() == null){
							$('#acad_year').val('');	
						}else{
							$('#acad_year').val($('#acad_year_multiselect').val());	
						}					
					}
				}
			});
		});
	@endif
	@if($action == 'getArchievelist')
		var photo_selected_cnt	=	0;
		var photo_name			=	'';
		var photo_size			=	'';
		var photo_download_name			=	'';
		function validateArchieveForm(){
			$('.frmbtngroup').prop('disabled',true);			
			$('#loddingImage').show();
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});
			$.ajax({
				url:baseUrl+'/auth/validatearchieveform',
				type: 'post',
				cache: false,					
				data:{
					"formdata": $('#entryFrm').serialize(),
				},
				success: function(res){		
					$('.error-message').remove();
					var resp		=   res.split('****');
					if(resp[1] == 'FAILURE'){
						$('.frmbtngroup').prop('disabled',false);
						$('#loddingImage').hide();
					   showJsonErrors(resp[2]);					   
					}else if(resp[1] == 'SUCCESS'){
						if(photo_selected_cnt == 0){
							submitForm();
						}else{
							$('.kv-fileinput-upload').click();
						}					
					}			
				},
				error: function(xhr, textStatus, thrownError) {
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}		
		function submitForm(){
			document.forms['entryFrm'].submit();
		}
		$(document).ready(function(){
			$("#archieve_upload").fileinput({        
				uploadUrl: baseUrl+'/auth/uploadfile',
				//dropZoneEnabled:true,
				dropZoneTitle:'',
				//showPreview:false,
				//showUpload:false,
				//showCaption: false,
				showRemove:false,
				showCancel:false,
				//uploadAsync:true,
				maxFileSize:20480,
				maxFilesNum: 2,
				allowedFileExtensions : ['pdf'],  
				msgInvalidFileExtension:'Please choose only pdf file',
				elErrorContainer:'#file_error',
				/*previewSettings:{
					image: {width: "80px", height: "55px"}
				},*/
				uploadExtraData: {
					'X-CSRF-Token': csrfTkn,
					'upload_folder_name':'archieve'
				}
			});
			$('#archieve_upload').on('filebrowse', function(event) {
				photo_selected_cnt++;
				$('#photo_selected_cnt').val(photo_selected_cnt);
			});
			$('#archieve_upload').on('fileuploaded', function(event, data, previewId, index) {            
				response        =   data.response, reader = data.reader;
				var respPart    =   response.success.split('@');
				photo_name	    =	respPart[0];
				photo_size	    =	respPart[1];
				photo_download_name	    =	respPart[2];
				$('#photo_name').val(photo_name);
				$('#photo_size').val(photo_size);
				$('#photo_download_name').val(photo_download_name);
				$('.kv-upload-progress').hide('slow');
				photo_selected_cnt = 0;
				submitForm();
			});
			/*$('#archieve_upload').on('filebatchuploadcomplete', function(event, files, extra) {
				$('.kv-upload-progress').hide('slow');
				$('#photo_name').val(photo_name);
				$('#photo_size').val(photo_size);			
				photo_selected_cnt = 0;
				submitForm();
			});*/
		});		
	@endif
	@if($action == 'getArchieveclassfile')
		var ischecked = false;
		function checkAll(){
			if (ischecked == false) {
				$(".classfileid").prop("checked",true);
				ischecked = true;
			} else if(ischecked == true){
				$(".classfileid").prop("checked",false);
				ischecked = false;
			}
		}
		
		function getClassesList(class_type_id){			
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});        
			$.ajax({
				url:baseUrl+'/auth/classeslist',
				type: 'post',
				cache: false,
				data:{
					'class_type_id':class_type_id,							
					'on_change_function_name':'',
				},
				success: function(res) {
					$('#classRespDv').html(res);
					@if(isset($class_id) && $class_id != '')
						$('#class_id').val(<% $class_id %>);
					@endif
				},
				error: function(xhr, textStatus, thrownError) {
					unBlockDv();
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		function getClassFileList(class_type_id){			
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});        
			$.ajax({
				url:baseUrl+'/auth/classfilelist',
				type: 'post',
				cache: false,
				data:{
					'class_type_id':class_type_id,							
					'on_change_function_name':'',
				},
				success: function(res) {
					$('#classResponseDv').html(res);
					@if(isset($class_id) && $class_id != '')
						$('#class_id').val(<% $class_id %>);
					@endif
				},
				error: function(xhr, textStatus, thrownError) {
					unBlockDv();
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		function validateSearch(){ 
			var errMsg = '';
			$(".error-message").remove();
			if($("#class_type_id").val() == ''){
				errMsg = 'Please Select Class Type';
				$("#class_type_id").after('<div class="error-message">'+errMsg+'</div>');
			}
			if($("#class_id").val() == ''){
				errMsg = 'Please Select Class';
				$("#class_id").after('<div class="error-message">'+errMsg+'</div>');
			}
			if(errMsg == ''){
				archiveClassUploadFile();
			}
		}
		function getFileuploadList(){
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});        
			$.ajax({
				url:baseUrl+'/classmngt/showarchiveclassfile',
				type: 'get',
				cache: false,
				data:{
					"class_type_id" : $('#class_type_id').val(),							
					"class_id" : $('#class_id').val(),
				},
				success: function(res) {
					$('#classFileDv').html(res);
					//getCategoryList();
					//$('#slctCategory').show();
				},
				error: function(xhr, textStatus, thrownError) {
					unBlockDv();
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		function archiveClassUploadFile(){
			$('.frmbtngroup').prop('disabled',true);
			$('#loddingImage').show();
			var classFileId		=	'';
			$('.classfileid').each(function(){
				if($(this).prop('checked')){
					classFileId	+=	$(this).val()+',';
				}
			})
			//if(classFileId != ''){
				$.ajaxSetup({
					headers: {
						'X-CSRF-Token': csrfTkn
					}
				});
				$.ajax({
					url:baseUrl+'/classmngt/archiveclassuploadfile',
					type: 'post',
					cache: false,					
					data:{
						"classFileId":classFileId,
						"class_type_id" : $('#class_type_id').val(),							
						"class_id" : $('#class_id').val(),
					},
					success: function(res){
						var resp		=   res.split('****');						
						$('.frmbtngroup').prop('disabled',false);
						if(resp[1] == 'FAILURE' || resp[1] == 'ERROR'){											
							$('#failMsgDiv').removeClass('text-none');
							$('.failmsgdiv').html(resp[2]);
							$('#failMsgDiv').show('slow');	
							$('#classFileDv').html();
							setTimeout(function(){ $('#failMsgDiv').fadeOut('slow'); }, 5000);
							window.location.replace(baseUrl+"/classmngt/archieveclassfile"); 
							//$('#classFileDv').hide();
						}else if(resp[1] == 'SUCCESS'){
							$('#sucMsgDiv').removeClass('text-none');
							$('.sucmsgdiv').html(resp[2]);
							$('#sucMsgDiv').show('slow');												
							resetFormVal('entryFrm',0,1);
							setTimeout(function(){ $('#sucMsgDiv').fadeOut('slow'); }, 5000);
							window.location.replace(baseUrl+"/classmngt/archieveclassfile"); 
							showData();
						}						
					},
					error: function(xhr, textStatus, thrownError) {
						//alert('Something went to wrong.Please Try again later...');
					}
				});				
			/*}else{
				alert('Please select at least one class file');
			}*/						
		}
		function getCategoryList(class_id){
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});        
			$.ajax({
				url:baseUrl+'/auth/categorylist',
				type: 'post',
				cache: false,
				data:{
					'class_type_id':$('#class_type_id').val(),
					'class_id':class_id,
				},
				success: function(res) {
					$('#slctCategory').show();
					$('#slctCategoryDiv').html(res);
					
				},
				error: function(xhr, textStatus, thrownError) {
					unBlockDv();
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		function validateMoveFile(){ 
			var errMsg = '';
			$(".error-message").remove();
			if($("#class_type_id").val() == ''){
				errMsg = 'Please Select Class Type';
				$("#class_type_id").after('<div class="error-message">'+errMsg+'</div>');
			}
			if($("#class_id").val() == ''){
				errMsg = 'Please Select Class';
				$("#class_id").after('<div class="error-message">'+errMsg+'</div>');
			}
			if($("#class_file_id").val() == ''){
				errMsg = 'Please Select Category';
				$("#class_file_id").after('<div class="error-message">'+errMsg+'</div>');
			}
			if(errMsg == ''){
				moveClassUploadFile();
			}
		}
		function moveClassUploadFile(){
			$('.frmbtngroup').prop('disabled',true);
			$('#loddingImage').show();
			var classmovefileid		=	'';
			$('.classmovefileid').each(function(){
				if($(this).prop('checked')){
					classmovefileid	+=	$(this).val()+',';
				}
			})
			if(classmovefileid != ''){
				$.ajaxSetup({
					headers: {
						'X-CSRF-Token': csrfTkn
					}
				});
				$.ajax({
					url:baseUrl+'/classmngt/moveclassuploadfile',
					type: 'post',
					cache: false,					
					data:{
						"classFileId":classmovefileid,
						"class_file_id":$("#class_file_id").val(),
					},
					success: function(res){
						var resp		=   res.split('****');						
						$('.frmbtngroup').prop('disabled',false);
						if(resp[1] == 'FAILURE' || resp[1] == 'ERROR'){											
							$('#failMsgDiv').removeClass('text-none');
							$('.failmsgdiv').html(resp[2]);
							$('#failMsgDiv').show('slow');	
							$('#classFileDv').html();
							//$('#classFileDv').hide();
							setTimeout(function(){ $('#failMsgDiv').fadeOut('slow'); }, 5000);
							window.location.replace(baseUrl+"/classmngt/archieveclassfile"); 
							$('#loddingImage').hide();
							//$('#classFileDv').hide();
						}else if(resp[1] == 'SUCCESS'){
							$('#sucMsgDiv').removeClass('text-none');
							$('.sucmsgdiv').html(resp[2]);
							$('#sucMsgDiv').show('slow');		
							$('#classFileDv').hide();
							resetFormVal('entryFrm',0,1);
							setTimeout(function(){ $('#sucMsgDiv').fadeOut('slow'); }, 5000);
							window.location.replace(baseUrl+"/classmngt/archieveclassfile"); 
							showData();
						}						
					},
					error: function(xhr, textStatus, thrownError) {
						//alert('Something went to wrong.Please Try again later...');
					}
				});				
			}else{
				alert('Please select at least one class file');
				$('#loddingImage').hide();
				$('.frmbtngroup').prop('disabled',false);
			}						
		}
	@endif
	@if($action == 'getUploadclassfile')
		var photo_selected_cnt	=	0;
		var photo_name			=	$('#photo_name').val();
		var photo_size			=	$('#photo_size').val();
		var photo_download_name	=	$('#photo_download_name').val();
		function validateUploadClassFileForm(){			
			$('#sucMsgDiv').hide('slow');
			$('#failMsgDiv').hide('slow');					
			$('#failMsgDiv').addClass('text-none');
			$('#sucMsgDiv').addClass('text-none');
			$('.frmbtngroup').prop('disabled',true);			
			$('#loddingImage').show();
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});
			$.ajax({
				url:baseUrl+'/auth/validateuploadclassfileform',
				type: 'post',
				cache: false,					
				data:{
					"formdata": $('#entryFrm').serialize(),
				},
				success: function(res){		
					$('.error-message').remove();
					var resp		=   res.split('****');
					if(resp[1] == 'FAILURE'){
						$('.frmbtngroup').prop('disabled',false);
						$('#loddingImage').hide();
					   showJsonErrors(resp[2]);					   
					}else if(resp[1] == 'SUCCESS'){						
						@if(isset($class_id) && $class_id != '')
							if($('#photo_edit_cnt').val() == 1){
								$('.kv-fileinput-upload').click();
							}else{
								saveUploadedClassFile();
							}							
						@else
							$('.kv-fileinput-upload').click();
						@endif					
					}			
				},
				error: function(xhr, textStatus, thrownError) {
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		function saveUploadedClassFile(){			
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});
			$.ajax({
				url:baseUrl+'/auth/saveuploadedclassfile',
				type: 'post',
				cache: false,					
				data:{
					"formdata": $('#entryFrm').serialize(),
				},
				success: function(res){					
					var resp		=   res.split('****');
					$('#loddingImage').hide();
					$('.frmbtngroup').prop('disabled',false);
					if(resp[1] == 'FAILURE' || resp[1] == 'ERROR'){											
						$('#failMsgDiv').removeClass('text-none');
						$('.failmsgdiv').html(resp[2]);
						$('#failMsgDiv').show('slow');		   
					}else if(resp[1] == 'SUCCESS'){
						$('#sucMsgDiv').removeClass('text-none');
						$('.sucmsgdiv').html(resp[2]);
						$('#sucMsgDiv').show('slow');												
						//getClassFileList();
						showData();
						resetFormVal('entryFrm',0,1);
					}		
				},
				error: function(xhr, textStatus, thrownError) {
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		function getClassFileList(){			
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});
			$.ajax({
				url:baseUrl+'/teacher/classfilelist',
				type: 'post',
				cache: false,					
				data:{
					"class_file_id": $('#id').val(),
				},
				success: function(res){
					$('#classFileDtlDv').html(res);
				},
				error: function(xhr, textStatus, thrownError) {
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		$(document).ready(function(){
			$("#file_upload").fileinput({        
				uploadUrl: baseUrl+'/auth/uploadfile',
				//dropZoneEnabled:true,
				dropZoneTitle:'',
				//showPreview:true,
				//showUpload:false,
				//showCaption: false,
				showRemove:false,
				showCancel:false,
				//uploadAsync:true,
				//maxFileSize:10240,
				maxFileCount: 50,
				//allowedFileExtensions : ['pdf'],  
				//msgInvalidFileExtension:'Please choose only pdf file',
				elErrorContainer:'#file_error',
				/*previewSettings:{
					image: {width: "80px", height: "55px"}
				},*/
				uploadExtraData: {
					'X-CSRF-Token': csrfTkn,
					'upload_folder_name':'classfile'
				}
			});
			$('#file_upload').on('fileloaded', function(event, file, previewId, index, reader) {
				photo_selected_cnt++;
				$('#photo_selected_cnt').val(photo_selected_cnt);
				@if(isset($class_id) && $class_id != '')
					$('#photo_edit_cnt').val(1);
				@endif
			});
			$('#file_upload').on('fileuploaded', function(event, data, previewId, index) {            
				response				=   data.response, reader = data.reader;
				var respPart			=   response.success.split('@');
				photo_name				+=	respPart[0]+',';
				photo_size				+=	respPart[1]+',';
				photo_download_name	    +=	respPart[2]+',';
			});
			$('#file_upload').on('filebatchuploadcomplete', function(event, files, extra) {	
				$('.kv-upload-progress').hide('slow');
				$('#photo_name').val(photo_name);
				$('#photo_size').val(photo_size);
				$('#photo_download_name').val(photo_download_name);
				photo_selected_cnt = 0;				
				saveUploadedClassFile();
			});
			$('#file_upload').on('fileloaded', function(event, file, previewId, index, reader) {
				var appendTrObj		=	'<tr><td width="3%">&nbsp</td>';
				appendTrObj			+=	'<td width="20%"><input type="text" name="viewlink[]" class="form-control"></td>';
				appendTrObj			+=	'<td width="10%"></td></tr>';
				if($('#classFileDtlDv').find('table').find('tbody').find('tr:last').length == 0){
					$('#classFileDtlDv').find('table').find('tbody').append(appendTrObj);
				}else{
					$('#classFileDtlDv').find('table').find('tbody').find('tr:last').after(appendTrObj);
				}
				//$('#classFileDtlDv').find('table').find('tbody').find('tr:last').after
				//console.log(index);
			});
			@if(isset($class_id) && $class_id != '')
				getClassList(<% $class_type_id %>);
			@endif
			getClassFileList();
		});
		var ischecked = false;
		function checkAll(){
			if (ischecked == false) {
				$(".classfileid").prop("checked",true);
				ischecked = true;
			} else if(ischecked == true){
				$(".classfileid").prop("checked",false);
				ischecked = false;
			}
		}	
		function deleteClassFileDetails(){			
			var classFileId		=	'';
			$('.classfileid').each(function(){
				if($(this).prop('checked')){
					classFileId	+=	$(this).val()+',';
				}
			})
			if(classFileId != ''){
				if(confirm('Are you sure to delete ?')){
					$('.frmbtngroup').prop('disabled',true);
					$('#loddingImage').show();
					$.ajaxSetup({
						headers: {
							'X-CSRF-Token': csrfTkn
						}
					});
					$.ajax({
						url:baseUrl+'/teacher/deletefilelist',
						type: 'post',
						cache: false,					
						data:{
							"classFileId":classFileId,
						},
						success: function(res){
							var resp		=   res.split('****');						
							$('.frmbtngroup').prop('disabled',false);
							if(resp[1] == 'FAILURE' || resp[1] == 'ERROR'){											
								$('#failMsgDiv').removeClass('text-none');
								$('.failmsgdiv').html(resp[2]);
								$('#failMsgDiv').show('slow');		   
							}else if(resp[1] == 'SUCCESS'){
								$('#sucMsgDiv').removeClass('text-none');
								$('.sucmsgdiv').html(resp[2]);
								$('#sucMsgDiv').show('slow');												
								getClassFileList();
								showData();
							}							
						},
						error: function(xhr, textStatus, thrownError) {
							//alert('Something went to wrong.Please Try again later...');
						}
					});
				}
			}else{
				alert('Please select at least one class file');
			}						
		}
		function deleteClassFile(classFileId){
			if(confirm('Are you sure to delete ?')){
				if(confirm('If you delete this record all file associated with it also deleted.')){
					$('.frmbtngroup').prop('disabled',true);
					$('#loddingImage').show();
					$.ajaxSetup({
						headers: {
							'X-CSRF-Token': csrfTkn
						}
					});
					$.ajax({
						url:baseUrl+'/teacher/deleteclassfile',
						type: 'post',
						cache: false,					
						data:{
							"classFileId":classFileId,
						},
						success: function(res){
							var resp		=   res.split('****');						
							$('.frmbtngroup').prop('disabled',false);
							if(resp[1] == 'FAILURE' || resp[1] == 'ERROR'){											
								$('#failMsgDiv').removeClass('text-none');
								$('.failmsgdiv').html(resp[2]);
								$('#failMsgDiv').show('slow');		   
							}else if(resp[1] == 'SUCCESS'){
								$('#sucMsgDiv').removeClass('text-none');
								$('.sucmsgdiv').html(resp[2]);
								$('#sucMsgDiv').show('slow');												
								resetFormVal('entryFrm',0,1);
								showData();
							}							
						},
						error: function(xhr, textStatus, thrownError) {
							//alert('Something went to wrong.Please Try again later...');
						}
					});
				}				
			}									
		}
		
		function archiveClassFile(){
			$('.frmbtngroup').prop('disabled',true);
			$('#loddingImage').show();
			var classFileId		=	'';
			$('.classfileid').each(function(){
				if($(this).prop('checked')){
					classFileId	+=	$(this).val()+',';
				}
			})
			//if(classFileId != ''){
				$.ajaxSetup({
					headers: {
						'X-CSRF-Token': csrfTkn
					}
				});
				$.ajax({
					url:baseUrl+'/teacher/archiveclassfile',
					type: 'post',
					cache: false,					
					data:{
						"classFileId":classFileId,
					},
					success: function(res){
						var resp		=   res.split('****');						
						$('.frmbtngroup').prop('disabled',false);
						if(resp[1] == 'FAILURE' || resp[1] == 'ERROR'){											
							$('#failMsgDiv').removeClass('text-none');
							$('.failmsgdiv').html(resp[2]);
							$('#failMsgDiv').show('slow');		   
						}else if(resp[1] == 'SUCCESS'){
							$('#sucMsgDiv').removeClass('text-none');
							$('.sucmsgdiv').html(resp[2]);
							$('#sucMsgDiv').show('slow');												
							resetFormVal('entryFrm',0,1);
							showData();
						}						
					},
					error: function(xhr, textStatus, thrownError) {
						//alert('Something went to wrong.Please Try again later...');
					}
				});				
			/*}else{
				alert('Please select at least one class file');
			}*/						
		}
		function getClassesList(class_type_id){			
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});        
			$.ajax({
				url:baseUrl+'/auth/classeslist',
				type: 'post',
				cache: false,
				data:{
					'class_type_id':class_type_id,							
					'on_change_function_name':'',
				},
				success: function(res) {
					$('#classRespDv').html(res);
					@if(isset($class_id) && $class_id != '')
						$('#class_id').val(<% $class_id %>);
					@endif
				},
				error: function(xhr, textStatus, thrownError) {
					unBlockDv();
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
	@endif
	@if($action == 'getGallerylist')
		var photo_selected_cnt	=	0;
		var photo_name			=	$('#photo_name').val();
		var photo_size			=	$('#photo_size').val();
		var photo_download_name	=	$('#photo_download_name').val();
		function validateGallery(){
			$('#sucMsgDiv').hide('slow');
			$('#failMsgDiv').hide('slow');					
			$('#failMsgDiv').addClass('text-none');
			$('#sucMsgDiv').addClass('text-none');
			$('.frmbtngroup').prop('disabled',true);			
			$('#loddingImage').show();
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});
			$.ajax({
				url:baseUrl+'/auth/validategallery',
				type: 'post',
				cache: false,					
				data:{
					"formdata": $('#entryFrm').serialize(),
				},
				success: function(res){		
					$('.error-message').remove();
					var resp		=   res.split('****');
					if(resp[1] == 'FAILURE'){
						$('.frmbtngroup').prop('disabled',false);
						$('#loddingImage').hide();
					   showJsonErrors(resp[2]);					   
					}else if(resp[1] == 'SUCCESS'){	
						@if(isset($modelid) && $modelid != '')
							if($('#photo_edit_cnt').val() == 1){
								$('.kv-fileinput-upload').click();
							}else{
								saveUploadedGallery();
							}							
						@else
							$('.kv-fileinput-upload').click();
						@endif			
					}			
				},
				error: function(xhr, textStatus, thrownError) {
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		function saveUploadedGallery(){			
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});
			$.ajax({
				url:baseUrl+'/auth/saveuploadedgallery',
				type: 'post',
				cache: false,					
				data:{
					"formdata": $('#entryFrm').serialize(),
				},
				success: function(res){					
					var resp		=   res.split('****');
					$('#loddingImage').hide();
					$('.frmbtngroup').prop('disabled',false);
					if(resp[1] == 'FAILURE' || resp[1] == 'ERROR'){											
						$('#failMsgDiv').removeClass('text-none');
						$('.failmsgdiv').html(resp[2]);
						$('#failMsgDiv').show('slow');		   
					}else if(resp[1] == 'SUCCESS'){
						$('#sucMsgDiv').removeClass('text-none');
						$('.sucmsgdiv').html(resp[2]);
						$('#sucMsgDiv').show('slow');
						resetFormVal('entryFrm',0,1);
						showData();
					}		
				},
				error: function(xhr, textStatus, thrownError) {
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		function getGalleryFileList(){			
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});
			$.ajax({
				url:baseUrl+'/gallery/galleryfilelist',
				type: 'post',
				cache: false,					
				data:{
					"gallery_id": $('#id').val(),
				},
				success: function(res){
					$('#galleryFileDtlDv').html(res);
				},
				error: function(xhr, textStatus, thrownError) {
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		var ischecked = false;
		function checkAll(){
			if (ischecked == false) {
				$(".galleryfileid").prop("checked",true);
				ischecked = true;
			} else if(ischecked == true){
				$(".galleryfileid").prop("checked",false);
				ischecked = false;
			}
		}	
		function deleteGalleryFileDetails(){			
			var galleryfileid		=	'';
			$('.galleryfileid').each(function(){
				if($(this).prop('checked')){
					galleryfileid	+=	$(this).val()+',';
				}
			})
			if(galleryfileid != ''){
				if(confirm('Are you sure to delete ?')){
					$('.frmbtngroup').prop('disabled',true);
					$('#loddingImage').show();
					$.ajaxSetup({
						headers: {
							'X-CSRF-Token': csrfTkn
						}
					});
					$.ajax({
						url:baseUrl+'/gallery/deletegalleryfilelist',
						type: 'post',
						cache: false,					
						data:{
							"gallery_id":galleryfileid,
						},
						success: function(res){
							var resp		=   res.split('****');						
							$('.frmbtngroup').prop('disabled',false);
							if(resp[1] == 'FAILURE' || resp[1] == 'ERROR'){											
								$('#failMsgDiv').removeClass('text-none');
								$('.failmsgdiv').html(resp[2]);
								$('#failMsgDiv').show('slow');		   
							}else if(resp[1] == 'SUCCESS'){
								$('#sucMsgDiv').removeClass('text-none');
								$('.sucmsgdiv').html(resp[2]);
								$('#sucMsgDiv').show('slow');												
								getGalleryFileList();
								showData();
							}							
						},
						error: function(xhr, textStatus, thrownError) {
							//alert('Something went to wrong.Please Try again later...');
						}
					});
				}
			}else{
				alert('Please select at least one class file');
			}						
		}
		function deleteGalleryFile(galleryfileid){
			if(confirm('Are you sure to delete ?')){
				if(confirm('If you delete this record all file associated with it also deleted.')){
					$('.frmbtngroup').prop('disabled',true);
					$('#loddingImage').show();
					$.ajaxSetup({
						headers: {
							'X-CSRF-Token': csrfTkn
						}
					});
					$.ajax({
						url:baseUrl+'/gallery/deletegalleryfile',
						type: 'post',
						cache: false,					
						data:{
							"gallery_id":galleryfileid,
						},
						success: function(res){
							var resp		=   res.split('****');						
							$('.frmbtngroup').prop('disabled',false);
							if(resp[1] == 'FAILURE' || resp[1] == 'ERROR'){											
								$('#failMsgDiv').removeClass('text-none');
								$('.failmsgdiv').html(resp[2]);
								$('#failMsgDiv').show('slow');		   
							}else if(resp[1] == 'SUCCESS'){
								$('#sucMsgDiv').removeClass('text-none');
								$('.sucmsgdiv').html(resp[2]);
								$('#sucMsgDiv').show('slow');												
								resetFormVal('entryFrm',0,1);
								showData();
							}							
						},
						error: function(xhr, textStatus, thrownError) {
							//alert('Something went to wrong.Please Try again later...');
						}
					});
				}				
			}									
		}
		function sendGalleryNotificationMail(galleryid){			
			$('.frmbtngroup').prop('disabled',true);
			$('#loddingImage').show();
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});
			$.ajax({
				url:baseUrl+'/gallery/gallerynotificationmail',
				type: 'post',
				cache: false,					
				data:{
					"id":galleryid,
				},
				success: function(res){
					var resp		=   res.split('****');						
					$('.frmbtngroup').prop('disabled',false);
					if(resp[1] == 'FAILURE' || resp[1] == 'ERROR'){											
						$('#failMsgDiv').removeClass('text-none');
						$('.failmsgdiv').html(resp[2]);
						$('#failMsgDiv').show('slow');		   
					}else if(resp[1] == 'SUCCESS'){
						$('#sucMsgDiv').removeClass('text-none');
						$('.sucmsgdiv').html(resp[2]);
						$('#sucMsgDiv').show('slow');												
						resetFormVal('entryFrm',0,1);
						showData();
					}							
				},
				error: function(xhr, textStatus, thrownError) {
					//alert('Something went to wrong.Please Try again later...');
				}
			});						
		}
		$(document).ready(function(){
			$("#file_upload").fileinput({        
				uploadUrl: baseUrl+'/auth/uploadfile',
				//dropZoneEnabled:true,
				dropZoneTitle:'',
				showPreview:true,
				previewSettings:{image: {width: "100px", height: "80px"},},
				initialPreviewConfig: {width: '30px'},
				//showUpload:false,
				//showCaption: false,
				showRemove:false,
				showCancel:false,
				//uploadAsync:true,
				maxFileSize:20480,
				maxFileCount: 20,
				//allowedFileExtensions : ['pdf'],  
				//msgInvalidFileExtension:'Please choose only pdf file',
				elErrorContainer:'#file_error',
				/*previewSettings:{
					image: {width: "80px", height: "55px"}
				},*/
				uploadExtraData: {
					'X-CSRF-Token': csrfTkn,
					'upload_folder_name':'gallery'
				}
			});
			$('#file_upload').on('fileloaded', function(event, file, previewId, index, reader) {
				photo_selected_cnt++;								
				$('#photo_selected_cnt').val(photo_selected_cnt);
				@if(isset($modelid) && $modelid != '')
					$('#photo_edit_cnt').val(1);
				@endif
			});
			$('#file_upload').on('fileuploaded', function(event, data, previewId, index) {            
				response				=   data.response, reader = data.reader;
				var respPart			=   response.success.split('@');
				photo_name				=	respPart[0];
				photo_size				=	respPart[1];
				photo_download_name	    =	respPart[2];
			});
			$('#file_upload').on('filebatchuploadcomplete', function(event, files, extra) {	
				$('.kv-upload-progress').hide('slow');
				/*if(photo_name != ''){
					photo_name	=	photo_name.substring(0,(photo_name.length-1));
				}
				if(photo_size != ''){
					photo_size	=	photo_size.substring(0,(photo_size.length-1));
				}
				if(photo_download_name != ''){
					photo_download_name	=	photo_download_name.substring(0,(photo_download_name.length-1));
				}*/
				$('#photo_name').val(photo_name);
				$('#photo_size').val(photo_size);
				$('#photo_download_name').val(photo_download_name);
				photo_selected_cnt = 0;				
				saveUploadedGallery();
			});			
		});		
	@endif
	@if($action == 'getComposemail')
		var photo_selected_cnt	=	0;
		var photo_name			=	$('#photo_name').val();
		var photo_size			=	$('#photo_size').val();
		var photo_download_name	=	$('#photo_download_name').val();
		$(document).ready(function(){
			$("#file_upload").fileinput({        
				uploadUrl: baseUrl+'/auth/uploadmailfile',
				//dropZoneEnabled:true,
				dropZoneTitle:'',
				showPreview:true,
				previewSettings:{image: {width: "100px", height: "80px"},},
				initialPreviewConfig: {width: '30px'},
				//showUpload:false,
				//showCaption: false,
				showRemove:false,
				showCancel:false,
				//uploadAsync:true,
				maxFileSize:20480,
				maxFileCount: 20,
				//allowedFileExtensions : ['pdf'],  
				//msgInvalidFileExtension:'Please choose only pdf file',
				elErrorContainer:'#file_error',
				/*previewSettings:{
					image: {width: "80px", height: "55px"}
				},*/
				uploadExtraData: {
					'X-CSRF-Token': csrfTkn,
					'upload_folder_name':'attachment'
				}
			});
			$('#file_upload').on('fileloaded', function(event, file, previewId, index, reader) {
				photo_selected_cnt++;								
				$('#photo_selected_cnt').val(photo_selected_cnt);
				@if(isset($modelid) && $modelid != '')
					$('#photo_edit_cnt').val(1);
				@endif
			});
			$('#file_upload').on('fileuploaded', function(event, data, previewId, index) {            
				response				=   data.response, reader = data.reader;
				var respPart			=   response.success.split('@');
				photo_name				=	respPart[0];
				photo_size				=	respPart[1];
				photo_download_name	    =	respPart[2];
				path_file				=	respPart[3];
			});
			$('#file_upload').on('filebatchuploadcomplete', function(event, files, extra) {	
				$('.kv-upload-progress').hide('slow');
				/*if(photo_name != ''){
					photo_name	=	photo_name.substring(0,(photo_name.length-1));
				}
				if(photo_size != ''){
					photo_size	=	photo_size.substring(0,(photo_size.length-1));
				}
				if(photo_download_name != ''){
					photo_download_name	=	photo_download_name.substring(0,(photo_download_name.length-1));
				}*/
				$('#photo_name').val(photo_name);
				$('#photo_size').val(photo_size);
				$('#photo_download_name').val(photo_download_name);
				$('#path_file').val(path_file);
				photo_selected_cnt = 0;				
				sendMassMessage();
			});			
		});	
		$(function () {			
			//bootstrap WYSIHTML5 - text editor
			$(".textarea").wysihtml5();			
			$("#acad_year_multiselect").multiselect({
				height:"auto",
				header: "Select Accademic Year",
				width:'90%',
				/*
				 * Used before closing the multiselect widget
				 * Checking if any check option is checked
				 * then we are storing that value in t_sts_state_id
				 * hidden field other wise we will empty it.
				 */
				beforeclose:function(event, ui){
					if($('#acad_year_multiselect').val() != ''){
						if($('#acad_year_multiselect').val() == null){
							$('#acad_year').val('');	
						}else{
							$('#acad_year').val($('#acad_year_multiselect').val());	
						}					
					}
				}
			});
			/*$("#class_type_multiselect").multiselect({
				height:"auto",
				header: "Select Class Type",
				width:'90%',
				/*
				 * Used before closing the multiselect widget
				 * Checking if any check option is checked
				 * then we are storing that value in t_sts_state_id
				 * hidden field other wise we will empty it.
				 */
				/*beforeclose:function(event, ui){
					if($('#class_type_multiselect').val() != ''){
						if($('#class_type_multiselect').val() == null){
							$('#class_type_id').val('');	
						}else{
							$('#class_type_id').val($('#class_type_multiselect').val());	
						}					
					}
				}
			});*/
			$("#class_id_multiselect").multiselect({
				height:"auto",
				header: "Select Class",
				width:'90%',
				/*
				 * Used before closing the multiselect widget
				 * Checking if any check option is checked
				 * then we are storing that value in t_sts_state_id
				 * hidden field other wise we will empty it.
				 */
				beforeclose:function(event, ui){
					if($('#class_id_multiselect').val() != ''){
						if($('#class_id_multiselect').val() == null){
							$('#class_id').val('');	
						}else{
							$('#class_id').val($('#class_id_multiselect').val());	
						}					
					}
				}
			});
		});
		function getClassList(class_type_id){			
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});        
			$.ajax({
				url:baseUrl+'/auth/classlist',
				type: 'post',
				cache: false,
				data:{
					'class_type_id':class_type_id,							
					'on_change_function_name':'',
					'type':'multiselect'
				},
				success: function(res) {
					$('#classResponseDv').html(res);
					$("#class_id_multiselect option[value='']").remove();
					$("#class_id_multiselect").multiselect({
						height:"300px",
						header: "Select Class",
						/*
						 * Used before closing the multiselect widget
						 * Checking if any check option is checked
						 * then we are storing that value in t_sts_state_id
						 * hidden field other wise we will empty it.
						 */
						beforeclose:function(event, ui){
							if($('#class_id_multiselect').val() != ''){
								if($('#class_id_multiselect').val() == null){
									$('#class_id').val('');	
								}else{
									$('#class_id').val($('#class_id_multiselect').val());	
								}					
							}
						}
					});
				},
				error: function(xhr, textStatus, thrownError) {
					unBlockDv();
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		
		function sendMassMessage(){			
			$('.frmbtngroup').prop('disabled',true);
			$('.kv-fileinput-upload').click();
			$('#loddingImage').show();
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});
			$.ajax({
				url:baseUrl+'/mail/sendmassmessage',
				type: 'post',
				cache: false,					
				data:{
					"formdata": $('#entryFrm').serialize(),
				},
				success: function(res){
					$('.frmbtngroup').prop('disabled',false);
					$('.error-message').remove();
					$('#sucMsgDiv').hide('slow');
					$('#failMsgDiv').hide('slow');
					$('#loddingImage').hide();
					$('#failMsgDiv').addClass('text-none');
					$('#sucMsgDiv').addClass('text-none');
					var resp		=   res.split('****'); 
					if(resp[1] == 'SUCCESS'){						
						$('#sucMsgDiv').removeClass('text-none');
						$('.sucmsgdiv').html(resp[2]);
						$('#sucMsgDiv').show('slow');	
						window.location.replace(baseUrl+"/mail/composemail");
						resetFormVal('entryFrm',0);
					}else if(resp[1] == 'FAILURE'){
						showJsonErrors(resp[2]);																		
					}else if(resp[1] == 'ERROR'){
						$('#failMsgDiv').removeClass('text-none');
						$('.failmsgdiv').html(resp[2]);
						$('#failMsgDiv').show('slow');
					}						
				},
				error: function(xhr, textStatus, thrownError) {
					//alert('Something went to wrong.Please Try again later...');
				}
			});						
		}
	@endif
	@if($action == 'getTechercomposemail')
		$(function () {			
			//bootstrap WYSIHTML5 - text editor
			$(".textarea").wysihtml5();		
		});
		function getClassList(class_type_id){			
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});        
			$.ajax({
				url:baseUrl+'/auth/classlist',
				type: 'post',
				cache: false,
				data:{
					'class_type_id':class_type_id,							
					'on_change_function_name':'',
					'type':'multiselect'
				},
				success: function(res) {
					$('#classResponseDv').html(res);
					$("#class_id_multiselect option[value='']").remove();
					$("#class_id_multiselect").multiselect({
						height:"300px",
						header: "Select Class",
						/*
						 * Used before closing the multiselect widget
						 * Checking if any check option is checked
						 * then we are storing that value in t_sts_state_id
						 * hidden field other wise we will empty it.
						 */
						beforeclose:function(event, ui){
							if($('#class_id_multiselect').val() != ''){
								if($('#class_id_multiselect').val() == null){
									$('#class_id').val('');	
								}else{
									$('#class_id').val($('#class_id_multiselect').val());	
								}					
							}
						}
					});
				},
				error: function(xhr, textStatus, thrownError) {
					unBlockDv();
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		
		function sendTeacherMassMessage(){			
			$('.frmbtngroup').prop('disabled',true);
			$('.kv-fileinput-upload').click();
			$('#loddingImage').show();
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});
			$.ajax({
				url:baseUrl+'/mail/sendteachermassmessage',
				type: 'post',
				cache: false,					
				data:{
					"formdata": $('#entryFrm').serialize(),
				},
				success: function(res){
					$('.frmbtngroup').prop('disabled',false);
					$('.error-message').remove();
					$('#sucMsgDiv').hide('slow');
					$('#failMsgDiv').hide('slow');
					$('#loddingImage').hide();
					$('#failMsgDiv').addClass('text-none');
					$('#sucMsgDiv').addClass('text-none');
					var resp		=   res.split('****'); 
					if(resp[1] == 'SUCCESS'){						
						$('#sucMsgDiv').removeClass('text-none');
						$('.sucmsgdiv').html(resp[2]);
						$('#sucMsgDiv').show('slow');	
						window.location.replace(baseUrl+"/mail/techercomposemail");
						resetFormVal('entryFrm',0);
					}else if(resp[1] == 'FAILURE'){
						showJsonErrors(resp[2]);																		
					}else if(resp[1] == 'ERROR'){
						$('#failMsgDiv').removeClass('text-none');
						$('.failmsgdiv').html(resp[2]);
						$('#failMsgDiv').show('slow');
					}						
				},
				error: function(xhr, textStatus, thrownError) {
					//alert('Something went to wrong.Please Try again later...');
				}
			});						
		}
	@endif
	@if($action == 'getParentdetail')
		function addnewEnrollStudent(){
			var parent_id = '';
			var id			=	'';
			$('.parentActiveCls').each(function(){
				id = $(this).attr('id');
				parent_id = id.substring(15,25)
			});	
			if(parent_id != ''){
				$('.frmbtngroup').prop('disabled',true);
				$('#loddingImage').show();
				$.ajaxSetup({
					headers: {
						'X-CSRF-Token': csrfTkn
					}
				});
				$.ajax({
					url:baseUrl+'/registration/',
					type: 'get',
					cache: false,					
					data:{
						"formdata": $('#entryFrm').serialize(),
						'parent_id': parent_id,
					},
					success: function(res){
						$('.frmbtngroup').prop('disabled',false);
						$('.error-message').remove();
						$('#sucMsgDiv').hide('slow');
						$('#failMsgDiv').hide('slow');
						$('#loddingImage').hide();
						$('#failMsgDiv').addClass('text-none');
						$('#sucMsgDiv').addClass('text-none');
						var resp		=   res.split('****');
						if(resp[1] == 'SUCCESS'){						
							$('#sucMsgDiv').removeClass('text-none');
							$('.sucmsgdiv').html(resp[2]);
							$('#sucMsgDiv').show('slow');						
							resetFormVal('entryFrm',0);
						}else if(resp[1] == 'FAILURE'){
							showJsonErrors(resp[2]);																		
						}else if(resp[1] == 'ERROR'){
							$('#failMsgDiv').removeClass('text-none');
							$('.failmsgdiv').html(resp[2]);
							$('#failMsgDiv').show('slow');
						}						
					},
					error: function(xhr, textStatus, thrownError) {
						//alert('Something went to wrong.Please Try again later...');
					}
				});	
			}else{
				alert("Please Select Parent to Enroll New Child");
			}
			
		}
	@endif
	function addRemoveActiveClass(trObj,removeElement,className){
		$('.'+removeElement).removeClass(className);
		$(trObj).addClass(className);		
	}	
	function editProfile(){
		document.forms['userProfile'].submit();
	}
	function resetFormVal(frmId,radVal,hidVal){		
		if(radVal == 1){
			$('#'+frmId).find('input:checkbox').removeAttr('checked').removeAttr('selected');
			$('.'+frmId).find('input:checkbox').removeAttr('checked').removeAttr('selected');
		}else{
			$('#'+frmId).find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
			$('.'+frmId).find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');			
		}
		if(hidVal == 1){
			$('#'+frmId).find('input:hidden').val('');
		}
		$('#'+frmId).find('input:password,input:text, input:file, select, textarea').val('');	
		$('.'+frmId).find('input:password,input:text, input:file, select, textarea').val('');
		$('.error-message').remove();
		//resetting file upload content
		@if($action == 'getStudent')
			$('.file-preview').hide();
			$('.emptyDv').hide();
			$('#photo').fileinput('reset');
			$('#photo_name').val('');
			$('#photo_size').val('');
			$('#photo_selected_cnt').val('');
		@elseif($action == 'getArchievelist')
			$('#archieve_upload').fileinput('reset');
			$('#photo_selected_cnt').val(0);
		@elseif($action == 'getComposemail')
			$('#file_upload').fileinput('reset');
			$('#photo_selected_cnt').val(0);
			$('#subject').val('');
			$('#messagearea').val('');
		@elseif($action == 'getUploadclassfile' || $action == 'getGallerylist')
			$('#file_upload').fileinput('reset');
			$('#file_upload').fileinput('unlock');
			@if($action == 'getUploadclassfile')
				$('#classFileDtlDv').html('');	
			@endif
			@if($action == 'getGallerylist')
				$('#galleryFileDtlDv').html('');
			@endif
			$('.editClassBtn').hide();
			photo_name			=	'';
			photo_size			=	'';
			photo_download_name	=	'';
		@endif
		//$('.formError').remove();
	}
	//Ajax complete function
	function ajaxCompleteFunc(){
		setColorBoxDisplay();
	}
	function setColorBoxDisplay(){
		$(".iframe").colorbox({iframe:true,fixed:true, width:"900px", height:"600px",opacity:0.2,transition:'elastic'});
		$(".iframeD").colorbox({iframe:true,fixed:true, width:"700px", height:"600px",opacity:0.2,transition:'elastic'});
		$(".iframeLarge").colorbox({iframe:true,fixed:true, width:"98%", height:"90%",opacity:0.2,transition:'elastic'});
		$(".iframeSml").colorbox({iframe:true,fixed:true, width:"90%", height:"70%",opacity:0.2,transition:'elastic'});
		$(".iframeXSml").colorbox({iframe:true,fixed:true, width:"500px", height:"300px",opacity:0.2,transition:'elastic'});
		$(".group1").colorbox({rel:'group1',slideshow:true,height:"90%"});
		$(".ajax").colorbox();
	}	
	@if($action == 'getRole')
		listingUrl	=	baseUrl+'/master/rolelistingajax';
	@elseif($action == 'getMenu')
		listingUrl	=	baseUrl+'/master/menulistingajax';
	@elseif($action == 'getSubmenu')
		listingUrl	=	baseUrl+'/master/submenulistingajax';
	@elseif($action == 'getStudent')
		listingUrl			=	baseUrl+'/master/studentlistingajax';
	@elseif($action == 'getSummercampstudent')
		listingUrl			=	baseUrl+'/master/summercampstudentlistingajax';
	@elseif($action == 'getPendingstudentlist')
		listingUrl			=	baseUrl+'/master/pendingstudentlistajaxlisting';
	@elseif($action == 'getPendingsummercamp')
		listingUrl			=	baseUrl+'/master/pendingsummercampajaxlisting';
	@elseif($action == 'getTransactionlist')
		listingUrl			=	baseUrl+'/master/transactionlistajaxlisting';	
	@elseif($action == 'getCreateuser')
		listingUrl			=	baseUrl+'/users/userlistingajax';
	@elseif($action == 'getClasstype')
		listingUrl			=	baseUrl+'/classmngt/classtypelistingajax';
	@elseif($action == 'getClass')
		listingUrl			=	baseUrl+'/classmngt/classlistingajax';
	@elseif($action == 'getClassdetail')
		listingUrl			=	baseUrl+'/classmngt/classdetaillistingajax';
	@elseif($action == 'getCalenderlist')
		listingUrl			=	baseUrl+'/calender/calenderlistingajax';
	@elseif($action == 'getParentdetail')
		listingUrl			=	baseUrl+'/parent/parentdetailajaxlisting';
	@elseif($action == 'getTeachermngt')
		listingUrl			=	baseUrl+'/teacher/teacherlistingajax';	
	@elseif($action == 'getUploadclassfile')
		listingUrl			=	baseUrl+'/teacher/uploadclassfilelistingalax';
	@elseif($action == 'getArchieveclassfile')
		listingUrl			=	baseUrl+'/classmngt/uploadarchiveclassfilelistingalax';
	@elseif($action == 'getArchievelist')
		listingUrl			=	baseUrl+'/archieve/archievelistingajax';
	@elseif($action == 'getGallerylist')
		listingUrl			=	baseUrl+'/gallery/gallerylistingajax';
	@endif
	/*
	 * Displaying maste data result
	 * based on action name
	 */	
	function showData(calenderFlag){	
		$('#loddingImage').show();
		$('.ajaxresultbtn').prop('disabled',true);
		@if($action == 'getRole')
			listingUrl	=	baseUrl+'/master/rolelistingajax';
		@elseif($action == 'getMenu')
			listingUrl	=	baseUrl+'/master/menulistingajax';
		@elseif($action == 'getStudent' || $action == 'getPendingstudentlist')			
			@if($action == 'getStudent')
				listingUrl			=	baseUrl+'/master/studentlistingajax';
				listingUrl			+=	'?id='+$('#student_id').val();
				listingUrl			+=	'&class_type_id='+$('#class_type_id').val();
				listingUrl			+=	'&class_id='+$('#class_id').val();
				listingUrl			+=	'&acad_year='+$('#acad_year').val();
				listingUrl			+=	'&status='+$('#status').val();
			@elseif($action == 'getPendingstudentlist')
				listingUrl			=	baseUrl+'/master/pendingstudentlistajaxlisting';
				listingUrl			+=	'?id='+$('#student_id').val();
				listingUrl			+=	'&class_type_id='+$('#class_type_id').val();
				listingUrl			+=	'&class_id='+$('#class_id').val();
				listingUrl			+=	'&acad_year='+$('#acad_year').val();
				listingUrl			+=	'&fatherMotherName='+$('#fatherMotherName').val();
			@endif
			
		@elseif($action == 'getSummercampstudent')
			listingUrl			=	baseUrl+'/master/summercampstudentlistingajax';
			listingUrl			+=	'?id='+$('#student_id').val();
			listingUrl			+=	'&acad_year='+$('#acad_year').val();
		@elseif($action == 'getPendingsummercamp')
			listingUrl			=	baseUrl+'/master/pendingsummercampajaxlisting';
			listingUrl			+=	'?id='+$('#student_id').val();
			listingUrl			+=	'&class_id='+$('#class_id').val();
			listingUrl			+=	'&acad_year='+$('#acad_year').val();
		@elseif($action == 'getTransactionlist')
			listingUrl			=	baseUrl+'/master/transactionlistajaxlisting';
			listingUrl			+=	'?user_id='+$('#user_id').val();
			listingUrl			+=	'&trans_id='+$('#trans_id').val();
			listingUrl			+=	'&registration_type='+$('#registration_type').val();
			listingUrl			+=	'&created_at='+$('#created_at').val();
		@elseif($action == 'getCreateuser')
			listingUrl			=	baseUrl+'/users/userlistingajax';
			listingUrl			+=	'?user_id='+$('#user_id').val();
			listingUrl			+=	'&user_email='+$('#user_email').val();
			listingUrl			+=	'&temple_account_no='+$('#temple_acc_no').val();
			listingUrl			+=	'&role_id='+$('#role_id_listing').val();
		@elseif($action == 'getClasstype')
			listingUrl			=	baseUrl+'/classmngt/classtypelistingajax';
		@elseif($action == 'getClass')
			listingUrl			=	baseUrl+'/classmngt/classlistingajax';
			listingUrl			+=	'?class_type_id='+$('#class_type_id_list').val();
			listingUrl			+=	'&class_id='+$('#class_id').val();
		@elseif($action == 'getClassdetail')
			listingUrl			=	baseUrl+'/classmngt/classdetaillistingajax';
		@elseif($action == 'getCalenderlist')
			listingUrl			=	baseUrl+'/calender/calenderlistingajax';
		@elseif($action == 'getParentdetail')
			listingUrl			=	baseUrl+'/parent/parentdetailajaxlisting';
			var userid = $('#user_id').val();
			if(userid == undefined){
				userid = '';
			}
			listingUrl			+=	'?user_id='+userid;
		@elseif($action == 'getTeachermngt')
			listingUrl			=	baseUrl+'/teacher/teacherlistingajax';	
			listingUrl			+=	'?teacher_name='+$('#teacher_name_list').val();
			listingUrl			+=	'&email='+$('#email_list').val();
		@elseif($action == 'getTransactiondetail')
			listingUrl			=	baseUrl+'/master/transactiondetailajaxlisting';
		@elseif($action == 'getUploadclassfile')
			listingUrl			=	baseUrl+'/teacher/uploadclassfilelistingalax';	
			listingUrl			+=	'?class_type_id='+$('#class_type_id_list').val();
			listingUrl			+=	'&class_id='+$('#class_id_list').val();
		@elseif($action == 'getArchievelistingajax')
			listingUrl			=	baseUrl+'/archieve/archievelistingajax';
			listingUrl			+=	'?user_id='+$('#user_id').val();
			listingUrl			+=	'&user_email='+$('#user_email').val();
			listingUrl			+=	'&temple_account_no='+$('#temple_acc_no').val();
			listingUrl			+=	'&role_id='+$('#role_id').val();
		@elseif($action == 'getGallerylist')
			listingUrl			=	baseUrl+'/gallery/gallerylistingajax';
			/*listingUrl			+=	'?user_id='+$('#user_id').val();
			listingUrl			+=	'&user_email='+$('#user_email').val();
			listingUrl			+=	'&temple_account_no='+$('#temple_acc_no').val();
			listingUrl			+=	'&role_id='+$('#role_id').val();*/
		@elseif($action == 'getArchieveclassfile')
		listingUrl			=	baseUrl+'/classmngt/uploadarchiveclassfilelistingalax';
		listingUrl			+=	'?class_type_id='+$('#class_type_id_list').val();
		listingUrl			+=	'&class_id='+$('#class_id_list').val();
		@endif
		$.ajax({
			url:listingUrl,
			type: 'get',
			cache: false,			
			success: function(res) {
				$('.ajaxresultbtn').prop('disabled',false);
				$('#loddingImage').hide();
				$('#listingTable').html(res);
				$('.editLink').bind("click", function(e){			
					showBlockUI();
				});
				ajaxCompleteFunc();
				if(calenderFlag == 'calender'){
					var endDt	= $('#calender_end_date_hidden').val();					
					$('#calendar').fullCalendar('gotoDate',endDt);
				}	
				@if(Auth::user()->role_id == BaseController::getConfigVal('user_designation','parent') && BaseController::getUserId() != 0)				
					fetchStudentDetailAjax(<% BaseController::getUserId(); %>);
					$('.parentListTr').addClass('parentActiveCls');	
				@endif	
			},
			error: function(xhr, textStatus, thrownError) {
				$('#loddingImage').hide();
				//alert('Something went to wrong.Please Try again later...');
			}
		});
		//datepicker
		$('.datepickerDate').datepicker({
			format: 'mm/dd/yyyy'
		});
	}
	$(document).ready(function(){		
		if(action != '' && listingUrl != ''){
			showData();
		}
		//Light Box
		$(".iframe").colorbox({iframe:true,fixed:true, width:"900px", height:"600px",opacity:0.2,transition:'elastic'});
		$(".iframeD").colorbox({iframe:true,fixed:true, width:"700px", height:"600px",opacity:0.2,transition:'elastic'});
		$(".iframeLarge").colorbox({iframe:true,fixed:true, width:"90%", height:"90%",opacity:0.2,transition:'elastic'});
		$(".iframeSml").colorbox({iframe:true,fixed:true, width:"800px", height:"500px",opacity:0.2,transition:'elastic'});
		$(".iframeXSml").colorbox({iframe:true,fixed:true, width:"500px", height:"300px",opacity:0.2,transition:'elastic'});
		$(".group1").colorbox({rel:'group1'});
		$(".ajax").colorbox();		
		@if($action == 'getClass' || $action == 'getStudent' || $action == 'getSetting')			
			$(".datepkr").datepicker({
						format: 'mm/dd/yyyy',
						autoclose:true,
						endDate:new Date()
			});
			$(".datepkrNoRestrict").datepicker({
						format: 'mm/dd/yyyy',
						autoclose:true,
						//endDate:new Date()
			});
			$(".maskedDate").mask("99/99/9999");
			$(".maskedPhone").mask("999-999-9999");
			$(".maskedzip").mask("99999");
			
			//timepicker
			$('.timepicker').timepicker({
				minuteStep: 5,
				showSeconds: false,
				showMeridian: true,
				disableFocus: false,
				showWidget: true,
				defaultTime:false
			}).focus(function() {
				$(this).next().trigger('click');
			});
			@if($action == 'getStudent')
				$('.radioCls').click(function(){
					if($(this).val() == 'N'){
						$('#photo_name').val('');
						$('#photo_size').val('');
						$('.emptyDv').html('');
					}
				})
			@endif
		@endif
		@if($action == 'getClass')			
			function getClassList(class_type_id){			
				$.ajaxSetup({
					headers: {
						'X-CSRF-Token': csrfTkn
					}
				});        
				$.ajax({
					url:baseUrl+'/auth/classlist',
					type: 'post',
					cache: false,
					data:{
						'class_type_id':class_type_id,							
						'on_change_function_name':'',
						'type':'multiselect'
					},
					success: function(res) {
						$('#classResponseDv').html(res);
						$("#teacher_id_multiselect option[value='']").remove();
						$("#teacher_id_multiselect").multiselect({
							height:"300px",
							header: "Select Teacher",
							/*
							 * Used before closing the multiselect widget
							 * Checking if any check option is checked
							 * then we are storing that value in t_sts_state_id
							 * hidden field other wise we will empty it.
							 */
							beforeclose:function(event, ui){
								if($('#teacher_id_multiselect').val() != ''){
									if($('#teacher_id_multiselect').val() == null){
										$('#teacher_id').val('');	
									}else{
										$('#teacher_id').val($('#teacher_id_multiselect').val());	
									}					
								}
							}
						});
					},
					error: function(xhr, textStatus, thrownError) {
						unBlockDv();
						//alert('Something went to wrong.Please Try again later...');
					}
				});
			}
			$(document).ready(function(){				
				$("#teacher_id_multiselect").multiselect({
					height:"300px",
					header: "Select Teacher",
					/*
					 * Used before closing the multiselect widget
					 * Checking if any check option is checked
					 * then we are storing that value in t_sts_state_id
					 * hidden field other wise we will empty it.
					 */
					beforeclose:function(event, ui){
						if($('#teacher_id_multiselect').val() != ''){
							if($('#teacher_id_multiselect').val() == null){
								$('#teacher_id').val('');	
							}else{
								$('#teacher_id').val($('#teacher_id_multiselect').val());	
							}					
						}
					}//please send your kid name checque no amount  class
				});
				var teacher_id		=	"<% $teacher_id %>";
				var teacherIdArr	=	new Array();
				if(teacher_id != ''){
					/*
					* Used for manually checked checkbox value
					* matched with id stored in database.
					* Comparing each value with data base ids
					* if match it will return the position so we
					* manually checked that checkbox.
					*/
				   if(teacher_id.search(',') != -1){
					   teacherIdArr		=	teacher_id.split(',')
				   }else{
					   teacherIdArr		=	new Array(teacher_id);
				   }				   			   
				   $("#teacher_id_multiselect").multiselect("widget").find(":checkbox").each(function(){
					   var searchVal = $(this).val();
					   if($.inArray($(this).val(),teacherIdArr) != -1){
						   this.click();
					   } 
				   });
				}
			});
		@endif
		/*
		* Empty the value of autocpl hidden value 
		*/
		$('.autoCpl').bind("keydown keypress keyup", function(e){
			if( e.which == 8 || e.which == 46){
				$('.autoCplIdHidden').val('');			
			}		
		});
		$( document ).on( 'focus', ':input', function(){
			$( this ).attr( 'autocomplete', 'off' );
		});
	});
</script>
@if(isset($viewDataObj) && is_object($viewDataObj))
	@foreach($viewDataObj as $modelKey=>$modelVal)
		<script>
			var elementId		=	"<% $modelKey %>";
			var elementVal		=	"<% $modelVal %>";
			$('.datepicker').datepicker('hide');
			$('.datepickerDate').datepicker('hide');
			if($('#'+elementId).length > 0){
				if($('.radioCls').length > 0 && (elementVal == 'Y' || elementVal == 'N')){
					$('input[type=radio][value='+elementVal+']').prop('checked',true)
				}else{
					$('#'+elementId).val(elementVal);
				}	
			}
		</script>
	@endforeach
@endif