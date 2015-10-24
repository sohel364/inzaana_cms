<script>
	var controller		=	"<?php echo $controller; ?>";
	var action			=	"<?php echo $action; ?>";
	var csrfTkn			=	"<% csrf_token() %>";
	var baseUrl			=	"<% URL::to('/'); %>";
	var modalAcntCnt	=	0;
	$(document).ready(function() {
		$(".datepkr").datepicker({
					format: 'mm-dd-yyyy',
					autoclose:true,
					endDate:new Date()
		});
		$(".datepkrNoRestrict").datepicker({
					format: 'mm-dd-yyyy',
					autoclose:true,
					//endDate:new Date()
		});
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
		})
	});
	function resetPassword(){
		$('.frmbtngroup').prop('disabled',true);			
		$('.registerBtn').prop('disabled',true);
		$('.regBtnTxt').html('Please wait...');
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
					resetFormVal('entryFrm',0);			
				}else if(resp[1] == 'FAILURE'){
					showJsonErrors(resp[2]);																		
				}else if(resp[1] == 'ERROR'){
					alert(resp[2]);
				}		
			},
			error: function(xhr, textStatus, thrownError) {
				alert('Something went to wrong.Please Try again later...');
			}
		});
	}
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
	function resetFormVal(frmId,radVal){
		if(radVal == 1){
			$('#'+frmId).find('input:checkbox').removeAttr('checked').removeAttr('selected');	
		}else{
			$('#'+frmId).find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
		}			
		$('#'+frmId).find('input:password,input:text, input:file, select, textarea').val('');
		$('.totalClassCnt').html(0);
	}
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
							$('#religiousDv').show('slow');
						}else{
							$('#religiousDv').hide('slow');
							$('.religiousCls').prop('checked',false);
							$('#school_grade').val('');
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
			$('.submitBtn').html('Please wait...');
			$('.imgLoader').show();
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
					$('.error-message').remove();
					var resp		=   res.split('****');
					modalAcntCnt	=	0;
					if(resp[1] == 'ERROR'){
						alert(resp[2]);
						$('.registerBtn').prop('disabled',false);						
						$('.imgLoader').hide();
						$('.submitBtn').html('REGISTRATION');
					}else{
						if(resp[1] == 'FAILURE'){
							$('.registerBtn').prop('disabled',false);
							$('.imgLoader').hide();
							$('.submitBtn').html('REGISTRATION');
						   showJsonErrors(resp[2]);
						}else if(resp[1] == 'SUCCESS'){
							if(resp[2] == "CLASSFAILURE"){
								$('.registerBtn').prop('disabled',false);
								$('.imgLoader').hide();
								$('.submitBtn').html('REGISTRATION');
								alert(resp[3]);							
							}else if(resp[2] == "CLASSSUCCESS"){
								alert(resp[3]);
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
					alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		function submitRegForm(){
            if($('#temple_account_no').val() == ''){				
				$('#templeAccount').modal('show');
			}else{
				document.forms['registrationFrm'].submit();
			}
		}
		function calculateTotal(){
			
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
			maxFilesNum: 1,
			allowedFileExtensions : ['jpg', 'png','gif'],                               
			previewSettings:{
				image: {width: "80px", height: "55px"}
			},
			uploadExtraData: {
				'X-CSRF-Token': csrfTkn,
				'sub_category_id':""
			},
			/*overwriteInitial: false, 
			//allowedFileTypes: ['image', 'video', 'flash'],
			slugCallback: function(filename) {
				return filename.replace('(', '_').replace(']', '_');
			}*/
			fileActionSettings:{
								uploadIcon: '<i class="glyphicon glyphicon-upload text-info"></i>',
								uploadClass: 'btn btn-xs btn-default',
								uploadTitle: 'Upload file'
							}
		});
		$('#photo').on('fileuploaded', function(event, data, previewId, index) {               
			response        =   data.response, reader = data.reader;
			var respPart    =   response.success.split('@');
			photo_name	    +=	respPart[0]+',';
			photo_size	    +=	respPart[1]+',';
			setTimeout(function(){$('.kv-upload-progress').hide('slow');},5000);
		});
		$('#photo').on('fileuploaderror', function(event, data, previewId, index) {
			var form = data.form, files = data.files, extra = data.extra, 
			response = data.response, reader = data.reader;
			//console.log(response);
		});
		$('#photo').on('fileimageloaded', function(event, previewId) {
			$('.file-preview').show();
			photo_selected_cnt++;
			$('#photo_selected_cnt').val(photo_selected_cnt);
			//$('.kv-fileinput-upload').click();
			//$('.kv-file-upload').click();
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
			$('.regBtnTxt').html('REGISTRATION');
		}
	@endif
	function validateSummerCampRegForm(){
		$('.registerBtn').prop('disabled',true);
		$('.regBtnTxt').html('Please wait...');
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
				var resp    =   res.split('****');
				$('.error-message').remove();
				if(resp[1] == 'ERROR'){
					alert(resp[2]);
					$('.registerBtn').prop('disabled',false);
					$('.error-message').remove();
					$('.imgLoader').hide();
					$('.regBtnTxt').html('REGISTRATION');
				}else{
					if(resp[1] == 'FAILURE'){
						$('.registerBtn').prop('disabled',false);
						$('.error-message').remove();
						$('.imgLoader').hide();
						$('.regBtnTxt').html('REGISTRATION');
					   showJsonErrors(resp[2]);
					}else if(resp[1] == 'SUCCESS'){						
						document.forms['summercampregistrationFrm'].submit();
					}
				}				
			},
			error: function(xhr, textStatus, thrownError) {
				alert('Something went to wrong.Please Try again later...');
			}
		});
	}
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
	function showChecqueField(){
		if($('#payment_method').val() == 2){
			$('#chequeDivId').removeClass('text-none');
		}else{
			$('#chequeDivId').addClass('text-none');
		}
	}
</script>