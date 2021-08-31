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
                 <?php echo $row['orders'];?>
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
             <div class="table-responsive  table_select1 "><!--hide_table-->
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>
                             <?php echo 'order_id';?>
                        </th>
                        <th>
                             <?php echo lang('adding_date');?>
                        </th>
                        <th>
                            <?php echo 'customer_name';?>
                        </th>
                        <th>
                             <?php echo ('customer_email');?>
                        </th>
                        <th>
                             <?php echo ('customer_group');?>
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
                             <?php echo ('products_count');?>
                        </th>
                        <th>
                             <?php echo lang('total');?>
                        </th>
                        <th>
                             <?php echo ('Order Expenses');?>
                        </th>
                        <th>
                             <?php echo ('Order profit');?>
                        </th>
                        <th>
                             <?php echo ('Order profit');?> %
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
            
            <!--Products List-->
            <div class="table-responsive  table_select2 ">
                <table class="table table-bordered">
                <thead>
                <tr>
                    <th>
                         <?php echo 'order_id';?>
                    </th>
                    <th>
                         <?php echo lang('adding_date');?>
                    </th>
                    <th>
                        <?php echo ('product_id');?>
                    </th>
                    <th>
                        <?php echo ('model');?>
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
                         <?php echo ('product_cost');?>
                    </th>
                    <th>
                         <?php echo ('product_profit');?>
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
                            <td>
                                 <?php echo $produt->product_id;?>
                            </td>
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
            
            <!--Products List-->
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
                            <?php echo ('customer_id');?>
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
                                     <?php echo $customer->unix_time;?>
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
            
             
          </td>
        </tr>
<?php }
}
else{?>
    <tr><td colspan="12"><span style="text-align: center; display: block;"><?php echo lang('empty_table');?></span></td></tr>
<?php }?>