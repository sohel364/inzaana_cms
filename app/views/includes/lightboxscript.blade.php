<script>
    var controller				=	'<% $controller %>';
	var action					=	'<% $action %>';
	var csrfTkn					=	'<% csrf_token() %>';
	var baseUrl					=	'<% URL::to('/'); %>';
	var onChangeFunction		=	'';
	var listingUrl				=	'';
	/*
	 * Displaying maste data result
	 * based on action name
	 */
	if(action == "getDesignation"){
		listingUrl	=	baseUrl+'/master/designationlistingajax';		
	}else if(action == "getMenu"){
		listingUrl	=	baseUrl+'/master/menulistingajax';		
	}
	$(document).ready(function() {
		$( document ).on( 'focus', ':input', function(){
			$( this ).attr( 'autocomplete', 'off' );
		});
	});
	@if($action == 'getStudentdetail')	
        var totalOneClass						=	"<% $totalOneClass %>";
		var totalMultiClass						=	"<% $totalMultiClass %>";
		var amnt_paid							=	0;
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
		
		function autoSelectReligiousClass(grdVal){	
			if(grdVal != ''){
				$("#Religious").prop("checked", true);				
				$('input[id=religionId][value='+grdVal+']').prop('checked',true)
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
		
        $(document).ready(function() {
			$('.classTypeCls').click(function(){				
				$('.classTypeCls').each(function(){
					calculateTotalPaidAmount();
					if($(this).val() == '2'){
						if($(this).prop('checked')){
							//$('.languageCls').prop('checked',true);
						}else{
							$('.languageCls').prop('checked',true);
							calculateTotalPaidAmount();
						}
					}
				});
				calculateTotalPaidAmount();
			});
			$('.languageCls').click(function(e){
				if($('#Language').prop('checked')){
					calculateTotalPaidAmount();
				}else{		
					var checked = $("input[class=languageCls]:checked").length;
					if (checked == 0) {
						//$('#total_amount').val(0);
						//alert("Please Select atleast one Class");
						calculateTotalPaidAmount();
					}else{
						calculateTotalPaidAmount();
					}
					calculateTotal();
				}				
				calculateTotal();
			});
			$('#religsId').click(function(e){
				if($(this).prop('checked')==false){
					e.preventDefault();	
					$(".religiousCls").prop('checked', false)
					$(".religsCls").prop('checked', false)
					calculateTotalPaidAmount();
				}
				calculateTotal();
			});
			$(document).ready(function(){
				var genders = document.getElementsByClassName("religiousCls");
				var selectedGender = '';
				for(var i = 0; i < genders.length; i++) {
					if(genders[i].checked == true) {
						calculateTotalPaidAmount();
						selectedGender = genders[i].value;
					}
				}	 
				$('.religiousCls').click(function(e){
					if($('#religsId').prop('checked')==true){
						calculateTotalPaidAmount();
						if (selectedGender == '') {
							calculateTotalPaidAmount();
						}else{
							e.preventDefault();	
							$('#total_amount').val(0);
							calculateTotal();
							alert('You have no permission to change the Religious class');
						}
					}else if($('#religsId').prop('checked')==false){
						alert('Please Select the Religious class check box first');
						e.preventDefault();	
						$(".religiousCls").prop('checked', false)
						$('#additional_amount').val(0);
						//calculateTotalPaidAmount();
						calculateTotal();
					}
				});
				calculateTotalPaidAmount();
				calculateTotal();
			});	
			calculateTotal();
			//amnt_paid = $('total_amount_paid').val();
		});
		
		function calculateTotal(){
			var sub_total				=	0;
			var total					=	0;
			var total_amount_paid_tot	=	0;
			var extra_amount_tot		=	0;
			var additional_amount_tot	=	0;
			var total_amount_tot		=	0;
			///if($('#total_amount_paid').val() == 200){ 
				/*if($('#total_amount_paid').val() != ''){
					total_amount_paid_tot =  parseFloat($('#total_amount_paid').val());
				}
				if($('#extra_amount').val() != ''){
					extra_amount_tot =  parseFloat($('#extra_amount').val());
				}
				if($('#additional_amount').val() != ''){
					additional_amount_tot =  parseFloat($('#additional_amount').val());
				}
				sub_total = parseFloat(extra_amount_tot) - parseFloat(total_amount_paid_tot);
				$('#additional_amount').val(sub_total);
				if($('#additional_amount').val() != ''){
					additional_amount_tot =  parseFloat($('#additional_amount').val());
				}
				if($('#total_amount').val() != ''){
					total_amount_tot =  parseFloat($('#total_amount').val());
				}
				total	  =	parseFloat(total_amount_paid_tot) + parseFloat(additional_amount_tot);// alert($('#additional_amount').val(sub_total));
				$('#total_amount').val(total);// alert(total);*/
			//}else if($('#total_amount_paid').val() == 150){ 
				if($('#total_amount_paid').val() != ''){
					total_amount_paid_tot =  parseFloat($('#total_amount_paid').val());
				} 
				if($('#additional_amount').val() != ''){
					additional_amount_tot =  parseFloat($('#additional_amount').val());
				}

				///sub_total = parseFloat(extra_amount_tot) - parseFloat(total_amount_paid_tot);
				//$('#additional_amount').val(sub_total);

				//additional_amount_tot =  $('#additional_amount').val(sub_total);

				//alert(sub_total);
				/*if($('#total_amount').val() != ''){
					total_amount_tot =  parseFloat($('#total_amount').val());
				}
				if($('#total_amount_paid').val() < 200){
					amount_paid_tot =  150;
				}else if($('#total_amount_paid').val() = 200){
					amount_paid_tot =  200;
				}*/
				//alert(total_amount_paid_tot); alert(sub_total);
				total	  =	total_amount_paid_tot + additional_amount_tot;// alert($('#additional_amount').val(sub_total));
				$('#total_amount').val(total); ///alert(total);
			//}
		}
		//$('#total_amount').val(total);
		function calculateTotalPaidAmount(){
			calculateTotal();
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
			//alert(totalClsSelected);
			if(totalClsSelected >= 2){
				//total	=	parseInt(totalMultiClass,10);
				total	=	100;
				calculateTotal();
			}else{
				total	=	0;
				calculateTotal();
			}
			//$('.totalClassCnt').html(totalClsSelected);
			//alert(total);
			$('#additional_amount').val(total);
		}
		function addNewEnrollment(){
			$('.frmbtngroup').prop('disabled',true);
			$('.error-message').remove();
			$('.allerror').hide('slow');
			$('#loddingImage').show();
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});        
			$.ajax({
				url:baseUrl+'/auth/addnewenrolment',
				type: 'post',
				cache: false,
				data:{
					'formdata':$('#entryFrm').serialize(),
				},
				success: function(res) {					
					var resp		=   res.split('****');
					$('.frmbtngroup').prop('disabled',false);
					$('#loddingImage').hide();
					if(resp[1] == 'SUCCESS'){ 
						$('.text-success').html(resp[2]).show('slow');
						$('#enrolTotalAmount').html(resp[3]);
						if(resp[3] != 0){
							submitRegForm();
						}
                        
					}else if(resp[1] == 'FAILURE'){
					   showJsonErrors(resp[2]);
					}else if(resp[1] == 'ERROR'){
					   $('.text-error').html(resp[2]).show('slow');
					}
				},
				error: function(xhr, textStatus, thrownError) {					
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
        function submitRegForm(){
            if($('#temple_account_no').val() == ''){				
				$('#templeAccount').modal('show');
			}else{
				document.forms['entryFrm'].submit();
			}
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
		function deleteenrol(id){
			if(confirm('Are you sure to delete this enrol ?')){
				//$('.frmbtngroup').prop('disabled',true);
				$('#loddingImage').show();			
				$.ajaxSetup({
					headers: {
						'X-CSRF-Token': csrfTkn
					}
				});        
				$.ajax({
					url:baseUrl+'/auth/deleteenrol',
					type: 'post',
					cache: false,
					data:{
						'id':id,
					},
					success: function(res) {
						$('#loddingImage').hide();
						var resp		=   res.split('****');
						if(resp[1] == 'FAILURE'){
							alert(resp[2]);
							//$('.frmbtngroup').prop('disabled',false);						
						}else if(resp[1] == 'SUCCESS'){	
							$('#enroll'+id).hide('slow').remove();
						}
					},
					error: function(xhr, textStatus, thrownError) {	
						$('#loddingImage').hide();
						//alert('Something went to wrong.Please Try again later...');
					}
				});
			}
		}
		
		function addNewEnrol(){
			$('.frmbtngroup').prop('disabled',true);
			$('.error-message').remove();
			$('.allerror').hide('slow');
			$('#loddingImage').show();
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': csrfTkn
				}
			});        
			$.ajax({
				url:baseUrl+'/auth/addnewenrol',
				type: 'post',
				cache: false,
				data:{
					'formdata':$('#entryFrm').serialize(),
				},
				success: function(res) {					
					var resp		=   res.split('****');
					$('.frmbtngroup').prop('disabled',false);
					$('#loddingImage').hide();
					if(resp[1] == 'SUCCESS'){
						$('.text-success').html(resp[2]).show('slow');
						$('#enrolTotalAmount').html(resp[3]);
					}else if(resp[1] == 'FAILURE'){
					   showJsonErrors(resp[2]);
					}else if(resp[1] == 'ERROR'){
					   $('.text-error').html(resp[2]).show('slow');
					}
				},
				error: function(xhr, textStatus, thrownError) {					
					//alert('Something went to wrong.Please Try again later...');
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
		function deleteenrol(id){
			if(confirm('Are you sure to delete this enrol ?')){
				//$('.frmbtngroup').prop('disabled',true);
				$('#loddingImage').show();			
				$.ajaxSetup({
					headers: {
						'X-CSRF-Token': csrfTkn
					}
				});        
				$.ajax({
					url:baseUrl+'/auth/deleteenrol',
					type: 'post',
					cache: false,
					data:{
						'id':id,
					},
					success: function(res) {
						$('#loddingImage').hide();
						var resp		=   res.split('****');
						if(resp[1] == 'FAILURE'){
							alert(resp[2]);
							//$('.frmbtngroup').prop('disabled',false);						
						}else if(resp[1] == 'SUCCESS'){	
							$('#enroll'+id).hide('slow').remove();
						}
					},
					error: function(xhr, textStatus, thrownError) {	
						$('#loddingImage').hide();
						//alert('Something went to wrong.Please Try again later...');
					}
				});
			}
		}
	@endif
	@if($action == 'getRolemenu')
		function getDesignationWiseMenu(){
			$('#loddingImage').show();
			$.ajax({
				url:baseUrl+'/master/designationwisemenu',
				type: 'get',
				cache: false,
				//dataType: 'json',
				data:{
					'role_id':$('#role_id').val()
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
	@if($action == 'getClassdetail')
		var class_type_id	=	"<% $class_type_id %>";
		var class_id		=	"<% $class_id %>";//alert(class_type_id+"++"+class_id)
		function getClassList(class_type_id){
			showBlockDv();
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
					'on_change_function_name':onChangeFunction,
				},
				success: function(res) {
					$('#classResponseDv').html(res);
					$('#class_id').val(class_id);
					unBlockDv();
				},
				error: function(xhr, textStatus, thrownError) {
					unBlockDv();
					//alert('Something went to wrong.Please Try again later...');
				}
			});
		}
		if(class_type_id != 0){
			getClassList(class_type_id);
		}
	@endif
	// Common function load in all page
	function goToCurPage(obj){
		$('#loddingImage').show();
		urls = $(obj).attr('href');       
		$.ajax({
			url: urls,
			type: 'get',
			success:function(res){
				$('#listingTable').html(res);
				$('#loddingImage').hide();
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
	function addRemoveActiveClass(trObj,removeElement,className){
		$('.'+removeElement).removeClass(className);
		$(trObj).addClass(className);		
	}
	function resetFormVal(frmId,radVal){
		if(radVal == 1){
			$('#'+frmId).find('input:checkbox').removeAttr('checked').removeAttr('selected');	
		}else{
			$('#'+frmId).find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
		}			
		$('#'+frmId).find('input:text, input:file, select, textarea').val('');			
	}
	function showData(){
		$('#loddingImage').show();
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
		//datepicker
		$('.datepickerDate').datepicker({
			format: 'mm-dd-yyyy'
		});
	}
	$(document).ready(function(){
		$('.page-button').find('li > a').click(function(e) {			
			e.preventDefault();
			$url = $(this).attr('href');       
			$.ajax({
			   'url': $url
			}).done(function(data){
				 
			});
		});
		if(action != '' && listingUrl != ''){
			showData();
		}				
	});
</script>