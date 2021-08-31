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
                                         <?php echo $row['year'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['month'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['orders_count'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['customers'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['products_count'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['total'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['reward_points'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['coupons'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['products_cost'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['total_expenses'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['total_profit'];?>
                                    </td>
                                    <td>
                                         <?php echo $row['profit_percent'];?> %
                                    </td>
                                </tr>
                                <tr  class="">
                                  <td colspan="10">
                                    <!--Orders Details-->
                                    <?php if(!empty($row['orders_details'])){?>
                                         <div class="table-responsive  table_select1 "><!--hide_table-->
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>
                                                         <?php echo lang('order_id');?>
                                                    </th>
                                                    <th>
                                                         <?php echo lang('adding_date');?>
                                                    </th>
                                                    <th>
                                                        <?php echo lang('customer_name');?>
                                                    </th>
                                                    <th>
                                                         <?php echo lang('customer_email');?>
                                                    </th>
                                                    <th>
                                                         <?php echo lang('customer_group');?>
                                                    </th>
                                                    <th>
                                                         <?php echo lang('payment_method');?>
                                                    </th>
                                                    <th>
                                                         <?php echo lang('status');?>
                                                    </th>
                                                    <th>
                                                         <?php echo lang('country');?>
                                                    </th>
                                                    <th>
                                                         <?php echo lang('currency');?>
                                                    </th>
                                                    <th>
                                                         <?php echo lang('products_count');?>
                                                    </th>
                                                    <th>
                                                         <?php echo lang('total');?>
                                                    </th>
                                                    <th>
                                                         <?php echo lang('order_expenses');?>
                                                    </th>
                                                    <th>
                                                         <?php echo lang('order_profit');?>
                                                    </th>
                                                    <th>
                                                         <?php echo lang('profit_percent');?> %
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($row['orders_details'] as $order){?>
                                                        <tr>
                                                            <td>
                                                                <?php echo $order->id;?>
                                                            </td>
                                                            <td>
                                                                 <?php echo date('Y/m/d H:i', $order->unix_time);?>
                                                            </td>
                                                            <td>
                                                                 <?php echo $order->username;?>
                                                            </td>
                                                            <td>
                                                                 <?php echo $order->email;?>
                                                            </td>
                                                            <td>
                                                                 <?php echo $order->customer_group;?>
                                                            </td>
                                                            <td>
                                                                 <?php echo $order->payment_method;?>
                                                            </td>
                                                            <td>
                                                                 <?php echo $order->status;?>
                                                            </td>
                                                            <td>
                                                                 <?php echo $order->country;?>
                                                            </td>
                                                            <td>
                                                                 <?php echo $order->currency;?>
                                                            </td>
                                                            <td>
                                                                 <?php echo $order->products_count;?>
                                                            </td>
                                                            <td>
                                                                 <?php echo $order->final_total;?>
                                                            </td>
                                                            <td>
                                                                 <?php echo $order->order_cost;?>
                                                            </td>
                                                            <td>
                                                                 <?php echo $order->profit;?>
                                                            </td>
                                                            <td>
                                                                 <?php echo $order->profit_percent;?> %
                                                            </td>
                                                        </tr>
                                                    <?php }?>
                                                </tbody>
                                            </table>
                                        </div><!--table-responsive-->
                                    <?php }?>
                                    
                                    <!--Products List-->
                                    <?php if(!empty($row['products_details'])){?>
                                        <div class="table-responsive  table_select2 ">
                                            <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>
                                                     <?php echo lang('order_id');?>
                                                </th>
                                                <th>
                                                     <?php echo lang('adding_date');?>
                                                </th>
                                                <!--<th>
                                                    <?php echo ('product_id');?>
                                                </th>-->
                                                <th>
                                                    <?php echo lang('model');?>
                                                </th>
                                                <th>
                                                    <?php echo lang('product_name');?>
                                                </th>
                                                <th>
                                                     <?php echo lang('category');?>
                                                </th>
                                                <th>
                                                     <?php echo lang('currency');?>
                                                </th>
                                                <th>
                                                     <?php echo lang('price');?>
                                                </th>
                                                <th>
                                                     <?php echo lang('quantity');?>
                                                </th>
                                                <th>
                                                     <?php echo lang('product_cost');?>
                                                </th>
                                                <th>
                                                     <?php echo lang('product_profit');?>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($row['products_details'] as $produt){?>
                                                    <tr>
                                                        <td>
                                                             <?php echo $row['order_id'];?>
                                                        </td>
                                                        <td>
                                                             <?php echo date('Y/m/d H:i', $produt->unix_time);?>
                                                        </td>
                                                        <!--<td>
                                                             <?php echo $produt->product_id;?>
                                                        </td>-->
                                                        <td>
                                                             <?php echo $produt->model;?>
                                                        </td>
                                                        <td>
                                                              <?php echo $produt->title;?>
                                                        </td>
                                                        <td>
                                                             <?php echo $produt->category;?>
                                                        </td>
                                                        <td>
                                                             <?php echo $produt->currency;?>
                                                        </td>
                                                        <td>
                                                             <?php echo $produt->price;?>
                                                        </td>
                                                        <td>
                                                             <?php echo $produt->qty;?>
                                                        </td>
                                                        <td>
                                                             <?php echo $produt->cost;?>
                                                        </td>
                                                        <td>
                                                             <?php echo $produt->profit;?>
                                                        </td>
                                                    </tr>
                                                <?php }?>
                                            
                                            </tbody>
                                        </table>
                                        </div><!--table-responsive-->
                                    <?php }?>
                                    
                                    <!--Products List-->
                                    <?php if(!empty($row['customer_details'])){?>
                                        <div class="table-responsive  table_select3">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>
                                                        <?php echo lang('order_id');?>
                                                    </th>
                                                    <th>
                                                        <?php echo lang('adding_date');?>
                                                    </th>
                                                    <th>
                                                        <?php echo lang('customer_id');?>
                                                    </th>
                                                    <th>
                                                        <?php echo lang('phone');?>
                                                    </th>
                                                    <th>
                                                        <?php echo lang('country');?>
                                                    </th>
                                                    
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($row['customer_details'] as $customer){?>
                                                        <tr>
                                                            <td>
                                                                 <?php echo $row['order_id'];?>
                                                            </td>
                                                            <td>
                                                                 <?php echo date('Y/m/d', $customer->unix_time);?>
                                                            </td>
                                                            <td>
                                                                 <?php echo $customer->user_id;?>
                                                            </td>
                                                            <td>
                                                                 <?php echo $customer->phone;?>
                                                            </td>
                                                            <td>
                                                                 <?php echo $customer->country;?>
                                                            </td>
                                                            
                                                        </tr>
                                                    <?php }?>
                                                
                                                </tbody>
                                            </table>
                                        </div><!--table-responsive-->
                                    <?php }?>
                                     
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
            <?php if($pagination){?>
    			<ul class="pagination pagination-sm">
                    <?php echo $pagination;?>
    			</ul>    
            <?php }?>
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