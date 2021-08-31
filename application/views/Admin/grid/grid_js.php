<?php /*if(1== 1){?>
        <script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places"></script>

        <script src="<?php echo base_url();?>assets/template/site/js/jquery.geocomplete.js"></script>

    <?php }*/?>

<script type="text/javascript">
    var last_page;
    var old_search_word;
    var search_word;
    var new_order;

    <?php
     if(isset($filters)){
        foreach($filters as $filter){?>
            var old_<?php echo $filter['filter_name'];?>;
            var <?php echo $filter['filter_name'];?>;
    <?php }
    }
    if(isset($date_filter)){?>
        var old_date_from;
        var old_date_to;
        var date_from;
        var date_to;

    <?php }
    if(isset($search_fields_data)){?>
        var old_search_field_id;
        var search_field_id;
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
                         search_word : search_word ,
                         order_by    : $('#order_by').val(),
                         order_state : $('#order_state').val()
                       };


        var filters = new Array();
        var filters_data = new Array();

        <?php
         if(isset($filters))
         {
            foreach($filters as $filter)
            {?>
                <?php echo $filter['filter_name'];?> = $('#<?php echo $filter['filter_name'];?>').val();
                filters.push('<?php echo $filter['filter_name'];?>');
                filters_data.push(<?php echo $filter['filter_name'];?>);
        <?php
            } ?>

         postData.filter = filters;
         postData.filter_data = filters_data;

        <?php
         }

         if(isset($date_filter)){?>
            var date_from  = $("#date_from").val();
            var date_to    = $("#date_to").val();

            postData.date_from = date_from;
            postData.date_to   = date_to;
         <?php }

         if(isset($search_fields_data)){?>
            var search_field_id      = $(".search_field_val option:selected").val();
            postData.search_field_id = search_field_id;
         <?php }?>



         if(search_word == old_search_word <?php if(isset($filters)){ foreach($filters as $filter){ echo ' && ' . $filter['filter_name'];?> == old_<?php echo $filter['filter_name'];}}?>)
         {
            postData.page_number = page_number;
         }
         else
         {
            postData.page_number = 1;
            $('#page_number').val(1);
         }

         <?php if(isset($seller_all_products)){?>
            postData.seller_all_products = '<?php echo $seller_all_products;?>';
         <?php }?>

         <?php if(isset($index_method_id)){?>
            postData.index_method_id = '<?php echo $index_method_id;?>';
         <?php }?>

         <?php if(isset($product_id)){?>
            postData.product_id = '<?php echo $product_id;?>';
         <?php }?>


         $.post('<?php echo base_url().$module."/".$controller."/ajax_list";?>',postData ,function(data){

            old_search_word = data[3];

            <?php if(isset($filters))
            {
                $data_key = 4;
                foreach($filters as $filter)
                {?>
                   old_<?php echo $filter['filter_name'];?>  = data[<?php echo $data_key;?>];
            <?php  $data_key++;
                }
            }?>

            Metronic.unblockUI('.portlet');

            $('#result_data').html(data[0]);
            $('#result_data').find('.checkbox').uniform();
            $('#result_data').find('.image-thumbnail').fancybox({
        		'transitionIn'	:	'elastic',
        		'transitionOut'	:	'elastic',
        		'speedIn'		:	600,
        		'speedOut'		:	200,
        		'overlayShow'	:	false
        	});

            if($('#order_by').val() != '<?php echo lang('sort');?>')
            {
                 $('#result_data').find('.sorting').addClass('nodrag');

            }

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

        $('body').on('change', '#lang_id, #limit, #page_number, .grid-filter, #order_by, #order_state, #date_from, #date_to, .search_field_val', function(){

           getAjaxData();
           <?php if($module == 'orders' && $controller == 'admin_order' && $method == 'index'){?>
             clearInterval(new_order);
           <?php }?>

        });

        $('#search_word').keyup(function(){
            getAjaxData();
        });


        $('#next_page').click(function(e){
           e.preventDefault();

         <?php
            if(isset($filters)){
                foreach($filters as $filter){?>
                    old_<?php echo $filter['filter_name'];?> = <?php echo $filter['filter_name'];?>;
            <?php }
            }

            if(isset($date_filter)){?>
                var old_date_from = date_from;
                var old_date_to   = date_to;
            <?php }?>

            old_search_word = search_word;

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

            if(page_number > 1)
            {
                $('#prev_page').attr('disabled', false);
            }


        });

        $('#prev_page').click(function(e){
            e.preventDefault();

         <?php
            if(isset($filters)){
                foreach($filters as $filter){?>
                    old_<?php echo $filter['filter_name'];?> = <?php echo $filter['filter_name'];?>;
            <?php }
            }

            if(isset($date_filter)){?>
                old_date_from = date_from;
                old_date_to = date_to;
            <?php }?>


            old_search_word = search_word;

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

                        $.post('<?php echo base_url().$module."/".$controller."/do_action";?>',{row_id:data_to_send,action:action} ,function(data){

                            getAjaxData();
                            if($.trim(data) >= 1)
                            {
                                showToast('<?php echo lang('records_deleted_successfully');?>','<?php echo lang('success_msg_title');?>','success');
                            }
                            else
                            {
                                showToast(data,'<?php echo lang('error');?>','error');
                            }

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

                   $.post('<?php echo base_url().$module."/".$controller."/delete";?>',{row_id:data_to_send} ,function(data){

                        getAjaxData();
                        if($.trim(data) =='1')
                        {
                            showToast('<?php echo lang('record_deleted_successfully');?>','<?php echo lang('success_msg_title');?>','success');
                        }
                        else
                        {
                            showToast(data,'<?php echo lang('error');?>','error');
                        }
                    });
               }
               else
               {
                   Metronic.unblockUI('.portlet');
               }

            });
        });


        <?php
        //if(($module == 'categories' && $controller == 'admin' && $method == 'index') ||($module == 'root' && $controller == 'controllers' && $method == 'index') || ($module == 'root' && $controller == 'module' && $method == 'index'))
        {?>
            $("#grid_table").rowSorter({

                onDrop: function(tbody, row, new_index, old_index)
                {
                    var id         = $('.sorting').eq(new_index).data('id');
                    var sort_state = $('#order_state').val()

                    var postData = {
                                     old_sort   : old_index ,
                                     new_sort   : new_index ,
                                     id         : id ,
                                     sort_state : sort_state
                                   }

                    $.post('<?php echo base_url().$module."/".$controller."/sorting";?>',postData ,function(data)
                    {
                        getAjaxData();
                    });

                    $("#log").html(old_index + ". row moved to " + new_index);
                },
                disabledRowClass : "nodrag"
            });
        <?php }?>

        <?php if($module == 'orders' && ($controller == 'admin_order' || $controller == 'admin_pending_order') && $method == 'index'){?>
            if($('#page_number').val() == 1)
            {
                new_orders_stream();
            }
       <?php }?>
    });



    var i;
    function new_orders_stream()
    {

        var page_number = parseInt($('#page_number').val());
        var limit       = parseInt($('#limit').val());

        var i=0;

        new_order = setInterval(function(){

            i ++;
             var last_row_id = $('.sorting').eq(0).data('id');

             var postData = {
                                last_row_id : last_row_id,
                                lang_id     : $('#lang_id').val(),
                                page_number : page_number,
                                limit       : limit
                            };

             <?php if(isset($index_method_id)){?>
                postData.index_method_id = '<?php echo $index_method_id;?>';
             <?php }?>

             <?php if(isset($seller_all_products)){?>
                postData.seller_all_products = '<?php echo $seller_all_products;?>';
             <?php }?>

             $.post('<?php echo base_url().$module."/".$controller."/stream"?>', postData, function(result)
             {
                if($.trim(result) != '0')
                {
                    var last_row = limit - 1;
                    showToast('<?php echo lang('new_order_added');?>', '<?php echo lang('new_order_added');?>', 'success');
                    $('#result_data').prepend(result);
                    $('.sorting').eq(last_row).remove();
                }
             });

        }, 10000);



    }

</script>
