<script>
	var controller		=	"<?php echo $controller; ?>";
	var action			=	"<?php echo $action; ?>";
	var csrfTkn			=	"<% csrf_token() %>";
	var baseUrl			=	"<% URL::to('/'); %>";
	var modalAcntCnt	=	0;
	$(document).ready(function() {
		$( document ).on( 'focus', ':input', function(){
			$( this ).attr( 'autocomplete', 'off' );
		});
		@if($action == 'getLandingPage' || $action == 'getGallery')			   
			$("#owl-demo").owlCarousel({
				items:1,
				singleItem:true,				
				autoPlay : true,
				//autoHeight : true,
			});			
		@endif
		@if($action == 'getRegistrationfrm' || $action == 'getSummercampfrm' || $action == 'getNewregistration')			
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
			showFileUpload();
		@endif
		/*
		 * Used for navigation menu 
		 * present above slider in 
		 * home page
		 */
		"use strict";
		//add some elements with animate effect
		$(".box").hover(
			function () {
			$(this).find('span.badge').addClass("animated fadeInLeft");
			$(this).find('.ico').addClass("animated fadeIn");
			},
			function () {
			$(this).find('span.badge').removeClass("animated fadeInLeft");
			$(this).find('.ico').removeClass("animated fadeIn");
			}
		);		
		(function() {
			var $menu = $('.navigation nav'),
				optionsList = '<option value="" selected>Go to..</option>';

			$menu.find('li').each(function() {
				var $this   = $(this),
					$anchor = $this.children('a'),
					depth   = $this.parents('ul').length - 1,
					indent  = '';

				if( depth ) {
					while( depth > 0 ) {
						indent += ' - ';
						depth--;
					}

				}
				$(".nav li").parent().addClass("bold");

				optionsList += '<option value="' + $anchor.attr('href') + '">' + indent + ' ' + $anchor.text() + '</option>';
			}).end()
			.after('<select class="selectmenu">' + optionsList + '</select>');

			$('select.selectmenu').on('change', function() {
				window.location = $(this).val();
			});			
		})();
		//Navi hover
		$('ul.nav li.dropdown').hover(function () {
			$(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn();
		}, function () {
			$(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut();
		});
		$('#templeAccount').on('hide.bs.modal', function (e) {	
			if(modalAcntCnt == 0){
				document.forms['registrationFrm'].submit();
			}			
		});
	});
	/*
	* Used for resetting password 
	*/
	@if($action == 'getProfile' || $action == 'getResetpassword')		
		function resetPassword(){
			$('.frmbtngroup').prop('disabled',true);			
			$('.registerBtn').prop('disabled',true);
			$('.regBtnTxt').html('Please wait...');
			$('#SucMsg').hide();
			$('.imgLoader').show();
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});
			$.ajax({
				url:baseUrl+'/users/resetpassword',
				type: 'post',
				cache: false,					
				data:{
					"formdata": $('#entryFrm').serialize(),
				},
				success: function(res){					
					$('.error-message').remove();
					$('.registerBtn').prop('disabled',false);						
					$('.imgLoader').hide();
					$('.regBtnTxt').html('SUBMIT');
					var resp		=   res.split('****');
					if(resp[1] == 'SUCCESS'){
						$('#SucMsg').show();
						resetFormVal('entryFrm',0);			
					}else if(resp[1] == 'FAILURE'){
						showJsonErrors(resp[2]);																		
					}else if(resp[1] == 'ERROR'){
						alert(resp[2]);
					}		
				},
				error: function(xhr, textStatus, thrownError) {
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
	@endif
	
	/*
	* Used for showing ajax validation 
	* error
	*/
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
	/*
	* Used for resetting form value 
	*/
	function resetFormVal(frmId,radVal){
		if(radVal == 1){
			$('#'+frmId).find('input:checkbox').removeAttr('checked').removeAttr('selected');	
		}else{
			$('#'+frmId).find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
		}			
		$('#'+frmId).find('input:password,input:text, input:file, select, textarea').val('');
		
		@if($action == 'getRegistrationfrm' || $action == 'getNewregistration')
			$('.totalClassCnt').html(0);
			$('#photo_name').val('');
			$('#photo_size').val('');
			$('.file-preview').hide();
			$('.error-message').remove();
			$('#photo').fileinput('reset');
			$('#photo_selected_cnt').val('');
		@endif
	}
	@if($action == 'getRegistrationfrm' || $action == 'getNewregistration' || $action == 'getSummercampfrm')
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
	/*
	* Used for only registration form
	*/
	@if($action == 'getNewregistration')
		var photo_name          =   '';
        var photo_size          =   '';
        var photo_selected_cnt  =   0;
		function validateNewRegForm(){
			$('.registerBtn').prop('disabled',true);
			$('#regBtnId').html('Please wait...');
			$('.imgLoader').show();
			$('.error-message').remove();
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});
			$.ajax({
				url:baseUrl+'/auth/validatenewregistrationfrm',
				type: 'post',
				cache: false,					
				data:{
					"formData": $('#registrationFrm').serialize(),
				},
				success: function(res){		
					$('#regBtnId').html('REGISTRATION');
					$('.imgLoader').hide();
					var resp		=   res.split('****');
					modalAcntCnt	=	0;
					if(resp[1] == 'ERROR'){
						alert(resp[2]);
						$('.registerBtn').prop('disabled',false);
					}else{
						if(resp[1] == 'FAILURE'){
							$('.registerBtn').prop('disabled',false);
						   showJsonErrors(resp[2]);
						}else if(resp[1] == 'SUCCESS'){
							if(resp[2] == "CLASSFAILURE"){
								$('.registerBtn').prop('disabled',false);
								alert(resp[3]);							
							}else if(resp[2] == "CLASSSUCCESS"){
								//alert(resp[3]);
								if(photo_selected_cnt == 0){
									submitRegForm();
								}else{
									$('.kv-fileinput-upload').click();
								}
							}else{
								if(photo_selected_cnt == 0){
									submitRegForm();
								}else{
									$('.kv-fileinput-upload').click();
								}
							}						
						}
					}				
				},
				error: function(xhr, textStatus, thrownError) {
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		function submitRegForm(){
            /*if($('#temple_account_no').val() == ''){				
				$('#templeAccount').modal('show');
			}else{*/
				document.forms['registrationFrm'].submit();
			/*}*/
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
			elErrorContainer:'#file_error',
			//allowedFileTypes:['image'],
			//msgInvalidFileType:'error',
			allowedFileExtensions : ['jpg','png'],                               
			previewSettings:{
				image: {width: "80px", height: "55px"}
			},
			uploadExtraData: {
				'X-CSRF-Token': csrfTkn,
				'sub_category_id':""
			}
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
			submitRegForm();
		});		
		/*
		* used for closing the modal and
		* also focus the account no and 
		* will show the message in red
		* after the temple account no and
		* incrementing the modalAcntCnt flag
		*/
		function templeAccountNoValidation(){
			modalAcntCnt++;
			$('#templeAccount').modal('hide');
			$('#temple_account_no').focus();
			$('#temple_account_no').after('<div class="error-message">Enter Temple Account No</div>');
			$('.registerBtn').prop('disabled',false);			
			$('.imgLoader').hide();
		}
	@endif
	/*
	* Used for only registration form
	*/
	@if($action == 'getRegistrationfrm')		
		var totalOneClass						=	"<% $totalOneClass %>";
		var totalMultiClass						=	"<% $totalMultiClass %>";
		var photo_name          =   '';
        var photo_size          =   '';
        var photo_selected_cnt  =   0;
		$(document).ready(function() {
			$('.classTypeCls').click(function(){				
				$('.classTypeCls').each(function(){
					if($(this).val() == '2'){
						if($(this).prop('checked')){

						}else{
							$('.languageCls').prop('checked',false);
						}
					}else if($(this).val() == '1'){
						if($(this).prop('checked')){							
							if($('#school_grade').val() != ''){
								var grdVal	=	$('#school_grade').val()
								$('input[name=selected_religious][value='+grdVal+']').prop('checked',true);
							}else{
								$('.religiousCls').prop('checked',false);
							}
							$('#religiousDv').show('slow');
						}else{
							$('#religiousDv').hide('slow');
							$('.religiousCls').prop('checked',false);
							$('.religiousCls').removeClass('radioInactive');
						}						
					}
				});
				calculateTotalPaidAmount();
			});
			$('.languageCls').click(function(e){
				if($('#Language').prop('checked')){
					calculateTotalPaidAmount();
				}else{					
					e.preventDefault();	
					alert('Please select Language class');
				}				
			});
			$('.religiousCls').click(function(e){
				if($('#Religious').prop('checked')){
					calculateTotalPaidAmount();
				}else{					
					e.preventDefault();	
					alert('Please select Religious class');
				}				
			});			
		});
		
		function calculateTotalPaidAmount(){
			var totalClsSelected	=	0;
			$('.languageCls').each(function(){
				if($(this).prop('checked')){
					totalClsSelected++;
				}						
			});
			$('.religiousCls').each(function(){
				if($(this).prop('checked')){
					totalClsSelected++;
				}						
			});
			if($('#yoga').prop('checked')){
				//totalClsSelected++;
			}
			if($('#adult_class').val() != ''){
				totalClsSelected++;
			}
			if(totalClsSelected == 1){
				total	=	parseInt(totalOneClass,10);
			}else if(totalClsSelected >= 2){
				total	=	parseInt(totalMultiClass,10);
			}else{
				total	=	0;
			}
			$('.totalClassCnt').html(totalClsSelected);
			$('#total_amount').val(total);
		}
		function validateRegForm(){			
			$('.registerBtn').prop('disabled',true);
			$('#regBtnId').html('Please wait...');
			$('.imgLoader').show();
			$('.error-message').remove();
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});
			$.ajax({
				url:baseUrl+'/auth/validateregistrationfrm',
				type: 'post',
				cache: false,					
				data:{
					"formData": $('#registrationFrm').serialize(),
				},
				success: function(res){		
					$('#regBtnId').html('REGISTRATION');
					$('.imgLoader').hide();
					var resp		=   res.split('****');
					modalAcntCnt	=	0;
					if(resp[1] == 'ERROR'){
						alert(resp[2]);
						$('.registerBtn').prop('disabled',false);
					}else{
						if(resp[1] == 'FAILURE'){
							$('.registerBtn').prop('disabled',false);
						   showJsonErrors(resp[2]);
						}else if(resp[1] == 'SUCCESS'){
							if($('#payment_method').val() == 1){
								alert('Currentlly paypal method is unavailable.');
								$('.registerBtn').prop('disabled',false);
							}else if($('#payment_method').val() == 2 || $('#payment_method').val() == 3){
								if(resp[2] == "CLASSFAILURE"){
									$('.registerBtn').prop('disabled',false);
									alert(resp[3]);							
								}else if(resp[2] == "CLASSSUCCESS"){
									//alert(resp[3]);
									$('.registerBtn').prop('disabled',false);
									if($('#payment_method').val() == 2){
										if(confirm('Cheque method might take more time for approval.')){
											if(photo_selected_cnt == 0){
												submitRegForm();
											}else{
												$('.kv-fileinput-upload').click();
											}
										}										
									}else{
										if(photo_selected_cnt == 0){
											submitRegForm();
										}else{
											$('.kv-fileinput-upload').click();
										}
									}									
								}else{
									$('.registerBtn').prop('disabled',false);
									if($('#payment_method').val() == 2){
										if(confirm('Cheque method might take more time for approval.')){
											if(photo_selected_cnt == 0){
												submitRegForm();
											}else{
												$('.kv-fileinput-upload').click();
											}
										}										
									}else{
										if(photo_selected_cnt == 0){
											submitRegForm();
										}else{
											$('.kv-fileinput-upload').click();
										}
									}									
								}
							}													
						}
					}				
				},
				error: function(xhr, textStatus, thrownError) {
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		
		function submitRegForm(){
            /*if($('#temple_account_no').val() == ''){				
				$('#templeAccount').modal('show');
			}else{*/
				document.forms['registrationFrm'].submit();
			/*}*/
		}
		/*
		 * Used for auto selectin religious class
		 * based on grade value
		 * 
		 * @param {type} grdVal
		 * @returns {void}
		 * 
		 */
		function autoSelectReligiousClass(grdVal){				
			if(grdVal != ''){
				$("#Religious").prop("checked", true);				
				$('input[name=selected_religious][value='+grdVal+']').prop('checked',true)
				$('.religiousCls').addClass('radioInactive');
				$('#religiousDv').show('slow');
				$('.radioInactive').on("click", function(e){
					e.preventDefault();
					return false;
				});				
			}else{
				$("#Religious").prop("checked", false);
				$('#religiousDv').hide('slow');
				$('.religiousCls').prop('checked',false);
			}
			calculateTotalPaidAmount();
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
			elErrorContainer:'#file_error',
			allowedFileExtensions : ['jpg', 'png'],                               
			previewSettings:{
				image: {width: "80px", height: "55px"}
			},
			uploadExtraData: {
				'X-CSRF-Token': csrfTkn,
				'sub_category_id':""
			}
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
			submitRegForm();
		});
		/*
		* used for closing the modal and
		* also focus the account no and 
		* will show the message in red
		* after the temple account no and
		* incrementing the modalAcntCnt flag
		*/
		function templeAccountNoValidation(){
			modalAcntCnt++;
			$('#templeAccount').modal('hide');
			$('#temple_account_no').focus();
			$('#temple_account_no').after('<div class="error-message">Enter Temple Account No</div>');
			$('.registerBtn').prop('disabled',false);			
			$('.imgLoader').hide();
		}
	@endif
	/*
	* Used for only summer camp 
	* registration form
	*/
	@if($action == 'getSummercampfrm')
		function validateSummerCampRegForm(){
			$('.registerBtnCls').prop('disabled',true);
			$('.error-message').remove();
			$('#regBtnId').html('Please wait...');
			$('.imgLoader').show();
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});
			$.ajax({
				url:baseUrl+'/auth/validatesummercampregistrationfrm',
				type: 'post',
				cache: false,					
				data:{
					"formData": $('#summercampregistrationFrm').serialize(),
				},
				success: function(res) {	
					$('#regBtnId').html('REGISTRATION');
					$('.imgLoader').hide();
					var resp    =   res.split('****');
					if(resp[1] == 'ERROR'){
						alert(resp[2]);
						$('.registerBtnCls').prop('disabled',false);
						$('.error-message').remove();
						$('.imgLoader').hide();
					}else{
						if(resp[1] == 'FAILURE'){
							$('.registerBtn').prop('disabled',false);
							$('.error-message').remove();
							$('.imgLoader').hide();
							showJsonErrors(resp[2]);
						}else if(resp[1] == 'SUCCESS'){
							if($('#payment_method').val() == 1){
								$('.registerBtn').prop('disabled',false);
								alert('Currentlly paypal method is unavailable.');
							}else if($('#payment_method').val() == 2 || $('#payment_method').val() == 3){
								if(resp[2] == "CLASSFAILURE"){
									$('.registerBtnCls').prop('disabled',false);
									$('.error-message').remove();
									$('.imgLoader').hide();																		
									alert(resp[3]);							
								}else if(resp[2] == "CLASSSUCCESS"){
									alert(resp[3]);	
									$('.registerBtn').prop('disabled',false);
									if($('#payment_method').val() == 2){
										if(confirm('Cheque method might take more time for approval.')){
											document.forms['summercampregistrationFrm'].submit();
										}										
									}else{
										document.forms['summercampregistrationFrm'].submit();
									}									
								}else{
									$('.registerBtn').prop('disabled',false);
									if($('#payment_method').val() == 2){
										if(confirm('Cheque method might take more time for approval.')){
											document.forms['summercampregistrationFrm'].submit();
										}										
									}else{
										document.forms['summercampregistrationFrm'].submit();
									}
								}
							}							
						}
					}
				},
				error: function(xhr, textStatus, thrownError) {
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
	@endif
	/*
	*	Used for showing check no text field on
	*	change of payment method 
	*/
	@if($action == 'getRegistrationfrm' || $action == 'getSummercampfrm')
		function showChecqueField(){
			if($('#payment_method').val() == 2){
				$('#chequeDivId').removeClass('text-none');
			}else{
				$('#chequeDivId').addClass('text-none');
				if($('#payment_method').val() == 1){
					alert('Currentlly paypal method is unavailable.');
				}				
			}
		}
	@endif
</script>