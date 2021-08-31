<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<h3 class="page-title">ADV Sales Report + Profit Reporting</h3>
<div class="tab-pane active" id="tab_1">
	
	<div class="portlet box blue margin-top-15">
	
		<div class="portlet-body">
          	<div class="table-scrollable">
            
                <table class="table  table-bordered table-hover">
                    <thead>
                        <tr>
                            <?php foreach($columns as $column_name){?>
                                <th scope="col">
                                     <?php echo $column_name;?> 
                                </th>
                            <?php }?>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                       
                        <?php 
                         if($data)
                         { 
                            foreach($data as $row){?>
                                <tr class="button_filter">
                                    <td>
                                         <?php //echo $row['details_icon'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['year'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['month'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['orders_count'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['users_count'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['products_count'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['reward_points'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['total'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['total_discount'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['coupons_discount'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['products_cost'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['total_profit'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['profit_percent'];?> %
                                    </td>
                                </tr>
                                
                        <?php }
                        }
                        else{?>
                            <tr><td colspan="12"><span style="text-align: center; display: block;"><?php echo lang('empty_table');?></span></td></tr>
                        <?php }?>
                       
                       
                       
                       
                       
                    </tbody>
                </table>
			</div><!--table-scrollable-->
        </div><!--form-->
        <div class="row no-margin bg_fff">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
           
		</div><!--col-->
        </div><!--row-->
    </div><!--portlet-->    
</div><!--tab-->

<script>
    //$.post('<?php echo base_url()."reports/dynamic_reports/ajax_list";?>','' , function(result){
        //$('#tbody').html(result);
    //});
    
    /*******Filters******/
    
    $('#filter2').click(function(e){
        e.preventDefault();
        
        var selected_payment = $('#payment_filter').find('option:selected');
        var payment_type     = selected_payment.data('type'); 
        
        var postData = {
                         country_id         : $('#country_id').val(),
                         payment_id         : $('#payment_filter').val(),
                         payment_type       : payment_type,
                         customer_group_id  : $('#customer_group_id').val(),
                         cat_id             : $('#cat_id').val(),
                         coupon_id          : $('#coupon_id').val(),
                         order_id_from      : $('#order_id_from').val(),
                         order_id_to        : $('#order_id_to').val(),
                         date_from          : $('#date_from').val(),
                         date_to            : $('#date_to').val(),
                         status_date_from   : $('#status_date_from').val(),
                         status_date_to     : $('#status_date_to').val(),
                         order_status_id    : $('#order_status_id').val(),
                         user_id            : $('#users_filter').val(),
                         user_email_id      : $('#emails').val(),
                         user_phone_id      : $('#phone').val(),
                         user_ip_address_id : $('#ip_address').val()
                       }
        $.post('<?php echo base_url()."reports/dynamic_reports/ajax_list";?>', postData, function(result){
            $('#tbody').html(result);            
        });
    });
    
    ///////////////////////////////////////////////////////
    $('#export').click(function(e){
        e.preventDefault();
        $('#export_div').toggle();
    });
    
</script>