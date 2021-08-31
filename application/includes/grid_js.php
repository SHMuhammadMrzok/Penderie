<script type="text/javascript">
    var last_page;
    var old_search_word;
    var search_word;
    
    <?php if($module == 'users' && $controller == 'customer_groups'){?>
        var old_country_id;
        var country_id;
    <?php }?>
        
    function getAjaxData()
    {
        var page_number = parseInt($('#page_number').val());
        
        search_word  = $('#search_word').val();
                        
        /// Loading Effect
        Metronic.blockUI({
            target: '.portlet',
            animate: true
        });
            
        var postData = {
                        lang_id     : $('#lang_id').val(),
                        limit       : $('#limit').val(),
                        search_word : search_word
        };
        
        <?php if($module == 'users' && $controller == 'customer_groups'){?>
            country_id   = $('#country_id').val();
            postData.country_id = country_id;
        <?php }?>
        
        if(search_word == old_search_word <?php if($module == 'users' && $controller == 'customer_groups'){?> && country_id == old_country_id <?php }?>)
        {
            postData.page_number = page_number;
        }
        else
        {
            postData.page_number = 1;
        }
        
        
        
        $.post('<?php echo base_url().$module."/".$controller."/ajax_list";?>',postData ,function(data){
            
            
            old_search_word = data[2];
            old_country_id  = data[3];
            
            Metronic.unblockUI('.portlet');
            
            $('#result_data').html(data[0]);
            $('#result_data').find('.checkbox').uniform();
            
            applyPaginationData(data[1]);
        }, 'json');
        
       
    }
    
    function applyPaginationData(countAllResults)
    {
         //Apply pagination
        total_records = countAllResults;
        limit         = parseInt($('#limit').val());
        last_page     = Math.ceil(total_records/limit);
        

        
        $('#total_pages').html(last_page);
        
        var page_number = parseInt($('#page_number').val());
            
        //if the page_number input value is changed
        if(page_number > last_page)
        {
            page_number = last_page ;
        }
        if(page_number < 1)
        {
            page_number = 1 ;
        }
        
        $('#page_number').val(page_number);
        ///////////
        
        if(page_number == 1)
        {
            $('#prev_page').attr('disabled', true);
            $('#next_page').attr('disabled', false);
        }
        
        if(page_number == last_page)
        {
            $('#next_page').attr('disabled', true);
            $('#prev_page').attr('disabled', false);
        }
    }
    
    $(function(){
        
        getAjaxData();
        
        $('body').on('click', '#check_all', function(){
            $('.checkbox').attr('checked', $(this).is(':checked'));
            
            $.uniform.update();
            
        });
        
        $('body').on('click', '.checkbox', function(){
            
            var total_checkboxs_size = $('.checkbox').size();
            var total_checkedboxs_size = $('.checkbox:checked').size();
            var check_status;
            
            if(total_checkboxs_size == total_checkedboxs_size)
            {
                if(total_checkboxs_size == 0) check_status = false;
                else check_status = true;
                
                $('#check_all').attr('checked', check_status);
            }
            else
            {
                $('#check_all').attr('checked', false);
            }
            
            $.uniform.update();
        });
        
        $('#lang_id, #limit, #page_number, #country_id').change(function(){
            getAjaxData();
        });
        
        $('#search_word').keyup(function(){
            getAjaxData();
        });
        
        
        $('#next_page').click(function(e){
            e.preventDefault();
            
            var page_number = parseInt($('#page_number').val());
            
            if(page_number < last_page)
            {
                page_number++;
                
                $('#page_number').val(page_number);
                $('#page_number').trigger('change');
                
                if(page_number == last_page)
                {
                    $('#next_page').attr('disabled', true);
                }
            }
            else
            {
                $('#next_page').attr('disabled', true);
            }
            
            if(page_number > 1){
                $('#prev_page').attr('disabled', false);
            }
        });
        
        $('#prev_page').click(function(e){
            e.preventDefault();
            
            var page_number = parseInt($('#page_number').val());
            
            if(page_number > 1){
                page_number--;
            
                $('#page_number').val(page_number);
                $('#page_number').trigger('change');
                
                if(page_number == 1)
                {
                    $('#prev_page').attr('disabled', true);
                }
            }else{
                $('#prev_page').attr('disabled', true);
            }
            
            if(page_number < last_page){
                $('#next_page').attr('disabled', false);
            }
        });
        
        
        $('body').on('click','#submit_delete',function(e){
            e.preventDefault();
            var selected_action = $("#checked_action").val();
            
            if($(".checkbox:checked").size() > 0 && selected_action != '' )
            {
                bootbox.confirm('<?php echo lang('confirm_delete_msg');?>', function(result) {
                    
                   if($.trim(result) == 'true') 
                   {
                    
                        /// Loading Effect
                        Metronic.blockUI({
                            target: '.portlet',
                            animate: true
                        });
                        
                        var data_to_send = $(".checkbox").serializeArray();
                        var action       = $("#checked_action").val();
                        
                        $.post('<?php echo base_url().$module."/".$controller."/do_action";?>',{customer_group_id:data_to_send,action:action} ,function(data){
                            
                            getAjaxData();
                            showToast('<?php echo lang('records_deleted_successfully');?>','<?php echo lang('success_msg_title');?>','success');
                            
                        });
                   }
                   
                        
                }); 
            }
            else
            {
                showToast('<?php echo lang('delete_error_msg');?>','<?php echo lang('error');?>','error');
            }
        });
        
        $('body').on('click','.delete-btn',function(e){
            e.preventDefault();
            
            /// Loading Effect
            Metronic.blockUI({
                target: '.portlet',
                animate: true
            });
            
            
            var data_to_send = $(this).val();
            
            bootbox.confirm('<?php echo lang('confirm_delete_msg');?>', function(result) {
                
               if($.trim(result) == 'true') 
               {    
                
                   $.post('<?php echo base_url().$module."/".$controller."/test_delete";?>',{customer_group_id:data_to_send} ,function(data){     

                        getAjaxData();
                        showToast('<?php echo lang('record_deleted_successfully');?>','<?php echo lang('success_msg_title');?>','success');                 
                    });
               }
                    
            }); 
        });
            
    });
</script>