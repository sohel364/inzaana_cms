<script>
    $(document).ready(function(){
        $(".inline").colorbox({inline:true, width:"55%",height:"77%"});
        $(".signin").colorbox({inline:true, width:"35%",height:"40%"});
        $(".forgotPassword").colorbox({inline:true, width:"40%",height:"32%"});
    });			
    $(document).ready(function(){
        //Light Box
        $(".iframe").colorbox({iframe:true,fixed:true, width:"900px", height:"600px",opacity:0.2,transition:'elastic'});
        $(".iframeD").colorbox({iframe:true,fixed:true, width:"700px", height:"600px",opacity:0.2,transition:'elastic'});
        $(".iframeLarge").colorbox({iframe:true,fixed:true, width:"90%", height:"90%",opacity:0.2,transition:'elastic'});
        $(".iframeSml").colorbox({iframe:true,fixed:true, width:"800px", height:"500px",opacity:0.2,transition:'elastic'});	
    });
    //Ajax complete function
    function ajaxCompleteFunc(){
        setColorBoxDisplay();
    }
    function setColorBoxDisplay(){
        $(".iframe").colorbox({iframe:true,fixed:true, width:"900px", height:"600px",opacity:0.2,transition:'elastic'});
        $(".iframeD").colorbox({iframe:true,fixed:true, width:"700px", height:"600px",opacity:0.2,transition:'elastic'});
        $(".iframeLarge").colorbox({iframe:true,fixed:true, width:"90%", height:"90%",opacity:0.2,transition:'elastic'});
        $(".iframeSml").colorbox({iframe:true,fixed:true, width:"90%", height:"70%",opacity:0.2,transition:'elastic'});			
    }
    $(document).ready(function(){
        $("#searchAdd").magicSuggest({
            data: baseUrl+'/bazars/addautocomplete',
            method:'get',
            maxSelection: 1,
            renderer: function(data){
                return '<div style="padding: 5px; overflow:hidden;">' +
                    '<div style="float: left;"></div>' +
                    '<div style="float: left; margin-left: 5px">' +
                        '<div style="font-weight: bold; color: #333; font-size: 10px; line-height: 11px">' + data.name + '</div>' +
                        '<div style="color: #999; font-size: 9px"> in ' + data.category_name + '</div>' +
                    '</div>' +
                '</div><div style="clear:both;"></div>';
            }	
        });
        var cityObj =   $("#searchCity").magicSuggest({
                            data: baseUrl+'/bazars/cityautocomplete',
                            method:'get',
                            maxSelection: 1,
                            renderer: function(data){
                                return '<div style="padding: 5px; overflow:hidden;">' +
                                    '<div style="float: left;">' +
                                    '<div style="float: left; margin-left: 5px">' +
                                        '<div style="font-weight: bold; color: #333; font-size: 10px; line-height: 11px">' + data.name + '</div>' +                    
                                    '</div>' +
                                '</div><div style="clear:both;"></div>';
                            }	
                        });
        $(cityObj).on('selectionchange', function(e,m){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': csrfTkn
                }
            });
            if(JSON.stringify(this.getValue())){
                $.ajax({
                    url:baseUrl+'/auth/setcityid',
                    type: 'post',
                    cache: false,
                    dataType: 'json',
                    data:{
                        "city_id": JSON.stringify(this.getValue()),
                    },
                    success: function(data) {
                        if(data.success == false)
                        {
                           
                        } else {
                            if(controller == 'postadd'){
                                window.location.href	=	baseUrl;
                            }else{
                                //window.location.href	=	window.location.href;
                            }
                        }
                    },
                    error: function(xhr, textStatus, thrownError) {
                        alert('Something went to wrong.Please Try again later...');
                    }
                });
            }
        });    
    });
    function chooseCategory(){
        window.location.href	=	baseUrl+'/bazars/choosecategory';
    }
</script>