<div class="search_list_panel" style="padding: 10px 0px 10px 10px;">
    <p class="search_list_panel_head"><span>New!</span> Create your personalized Alert!</p>
    <div class="pad10top">
        <% Form::open(array('id'=>'addAlertFrm')) %>
            <!--input type="hidden" name="_token" value="<% csrf_token() %>" />-->
            <table width="100%" border="0" cellspacing="1" cellpadding="4">
                <tr>
                    <td>
                        <div class="styled-select" id="catErrorDV">
                            <?php echo Form::select('category_id',$categoryListArr,'',array('id'=>'category_id','class'=> 'prodInput','tabindex'=>'11','label'=>false,'div'=>false,'required'=>'false','autocomplete'=>'off','onchange'=>'getSubcategoryList(this.value,"AddAlert")')); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="styled-select" id="subCategoryResponseDv">
                            <?php echo Form::select('subcategory_id',array(''=>'Select'),'',array('id'=>'subcategory_id','class'=> 'prodInput','tabindex'=>'11','label'=>false,'div'=>false,'required'=>'false','autocomplete'=>'off')); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="styled-select" id="catAlertResponseDv">
                            <?php echo Form::select('alert_type',array(''=>'Select'),'',array('id'=>'alert_type','class'=> 'prodInput','tabindex'=>'11','label'=>false,'div'=>false,'required'=>'false','autocomplete'=>'off')); ?> 
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="styled-select" id="alertCitySpnError">                            
                            <?php echo Form::text('alert_citie_id_hid','',array('id'=>'alert_citie_id_hid','class'=> 'alertCity','tabindex'=>'11','label'=>false,'div'=>false,'required'=>'false','Placeholder'=>'Type City name'));?>
                            <?php echo Form::hidden('city_id','',array('id'=>'city_id','tabindex'=>'11','label'=>false,'div'=>false,'required'=>'false'));?>                            
                        </div>
                    </td>
                </tr>                
                <tr>
                    <td>
                        <div>
                            <?php
                                //$userEmail  =   $this->Session->read('Auth.User.email');
                                $userEmail  =   '';
                                if(isset($userEmail) && $userEmail != ''){
                                    echo Form::text('alert_email','',array('id'=>'alert_email','class'=> 'search_list_txt_bx','tabindex'=>'11','label'=>false,'div'=>false,'required'=>'false','autocomplete'=>'off','Placeholder'=>'Enter E-mail Id','value'=>$userEmail,));                                    
                                }else{
                                    echo Form::text('alert_email','',array('id'=>'alert_email','class'=> 'search_list_txt_bx','tabindex'=>'11','label'=>false,'div'=>false,'required'=>'false','autocomplete'=>'off','Placeholder'=>'Enter E-mail Id'));                                                                        
                                }                                
                            ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>
                            <?php
                                //$userMobile  =   $this->Session->read('Auth.User.mobile_number');
                                $userMobile  =   '';
                                if(isset($userMobile) && $userMobile != ''){
                                    echo Form::text('alert_mobile','',array('id'=>'alert_mobile','class'=> 'search_list_txt_bx','tabindex'=>'11','label'=>false,'div'=>false,'required'=>false,'autocomplete'=>'off','Placeholder'=>'Enter Mobile No.','maxlength'=>'10','value'=>$userMobile));                                    
                                }else{
                                    echo Form::text('alert_mobile','',array('id'=>'alert_mobile','class'=> 'search_list_txt_bx','tabindex'=>'11','label'=>false,'div'=>false,'required'=>false,'autocomplete'=>'off','Placeholder'=>'Enter Mobile No.','maxlength'=>'10'));                                                                        
                                }                             
                            ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <?php echo Form::button('Submit',array('class'=>'search_list_bt','onclick'=>'sendAddAlert();'));?>
                    </td>
                </tr>
            </table>
        <% Form::close() %>
    </div>
</div>