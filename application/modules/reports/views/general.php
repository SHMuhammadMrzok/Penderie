<div class="row">
                <!-------------------------------->
                <div class="col-md-6">
					<!-- Begin: life time stats -->
					<div class="portlet box blue-steel">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-thumb-tack"></i><?php echo lang ('overview')?>
							</div>
							<div class="tools">
								<a href="javascript:;" class="collapse"></a>
								<!--<a href="#portlet-config" data-toggle="modal" class="config"></a>
								<a href="javascript:;" class="reload"></a>-->
								<a href="javascript:;" class="remove"></a>
								
							</div>
						</div>
						<div class="portlet-body">
							<div class="tabbable-line">
								<ul class="nav nav-tabs">
									<li class="active">
										<a href="#overview_1" data-toggle="tab">
										<?php echo lang('top_selling');?> </a>
									</li>
									
									<li>
										<a href="#overview_3" data-toggle="tab">
										<?php echo lang('new_customers');?> </a>
                                        
									</li>
									<li class="dropdown">
										<a href="#" class="dropdown-toggle" data-toggle="dropdown">
										<?php echo lang('orders');?> <i class="fa fa-angle-down"></i>
										</a>
										<ul class="dropdown-menu" role="menu">
											<li>
												<a href="#overview_4" tabindex="-1" data-toggle="tab">
												<?php echo lang('latest_10_orders');?> </a>
											</li>
											<li>
												<a href="#overview_5" tabindex="-1" data-toggle="tab">
												<?php echo lang('pending_orders');?> </a>
											</li>
											<li>
												<a href="#overview_6" tabindex="-1" data-toggle="tab">
												<?php echo lang('completed_orders');?> </a>
											</li>
											<li>
												<a href="#overview_7" tabindex="-1" data-toggle="tab">
												<?php echo lang('rejected_orders');?> </a>
											</li>
										</ul>
									</li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane active" id="overview_1">
										<div class="table-responsive">
											<table class="table table-striped table-hover table-bordered">
											<thead>
											<tr>
												<th>
													 <?php echo lang('product_name');?>
												</th>
												
												<th>
													 <?php echo lang('sold_times');?>
												</th>
												
											</tr>
											</thead>
											<tbody>
                                            <?php
                                             $top_selling_products = $this->admin_bootstrap->get_top_selling_products();
                                                
                                             if(count($top_selling_products) != 0)
                                             {
                                                 foreach($top_selling_products as $product)
                                                 { ?>
        											<tr>
        												<td>
        													<a target="_blank" href="<?php echo base_url();?>products/admin_products/read/<?php echo $product['product_id'].'/'.$this->data['lang_id'];?>">
        													<?php echo $product['name'];?> </a>
        												</td>
        												
        												<td>
        													 <?php echo $product['count'];?>
        												</td>
        												
        											</tr>
                                                  <?php }
                                                  }else{?>
                                                    <tr><td colspan="2" style="text-align: center;"><?php echo lang('no_data');?></td></tr>
                                                  <?php }?>  
											
											</tbody>
											</table>
										</div>
									</div>
									
									<div class="tab-pane" id="overview_3">
										<div class="table-responsive">
											<table class="table table-striped table-hover table-bordered">
    											<thead>
                                                
    											<tr>
    												<th>
    													 <?php echo lang('customer_name');?>
    												</th>
    												<th>
    													 <?php echo lang('total_orders');?>
    												</th>
    												<th>
    													 <?php echo lang('total_amount');?>
    												</th>
    												<th>
    												</th>
    											</tr>
    											</thead>
                                                
    											<tbody>
    											<?php
                                                 $new_customers_data = $this->admin_bootstrap->get_new_customers();
                                                 
                                                 if(count($new_customers_data) != 0)
                                                 {
                                                     foreach($new_customers_data as $row)
                                                     {
                                                    ?>
                                                        <tr>
            												<td>
            													<a target="_blank" href="<?php echo base_url().'users/admin_users/read/'.$row['id'].'/'.$this->data['lang_id'];?>">
            													<?php echo $row['username'];?> </a>
            												</td>
            												<td>
            													<?php echo $row['orders_count'];?>
            												</td>
            												<td>
            													<?php echo $row['total_cost'];?>
            												</td>
            												<td>
            													<a target="_blank" href="<?php echo base_url().'users/admin_users/read/'.$row['id'].'/'.$this->data['lang_id'];?>" class="btn default btn-xs green-stripe">
            													<?php echo lang('view');?> </a>
            												</td>
            											</tr>
        											<?php }
                                                }else{?>
                                                <tr><td colspan="2" style="text-align: center;"><?php echo lang('no_data');?></td></tr>
                                              <?php }?>  
    											
    											</tbody>
											</table>
										</div>
									</div>
                                    
									<div class="tab-pane" id="overview_4">
										<div class="table-responsive">
											<table class="table table-striped table-hover table-bordered">
    											<thead>
    											<tr>
    												<th>
    													 <?php echo lang('customer_name');?>
    												</th>
                                                    <th>
    													 <?php echo lang('order_id');?>
    												</th>
    												<th>
    													 <?php echo lang('date');?>
    												</th>
    												<th>
    													 <?php echo lang('amount');?>
    												</th>
    												<th>
    													 <?php echo lang('status');?>
    												</th>
    												<th>
    												</th>
    											</tr>
    											</thead>
    											<tbody>
        											<?php
                                                     $latest_orders_data = $this->admin_bootstrap->get_latest_orders();
                                                     
                                                     foreach($latest_orders_data as $order)
                                                     {
                                                    ?>
            											<tr>
            												<td>
            													<?php echo $order->first_name.' '. $order->last_name;?> 
            												</td>
                                                            <td>
                                                                <a target="_blank" href="<?php echo base_url().'orders/admin_order/view_order/'.$order->id;?>"><?php echo $order->id;?></a>
                                                            </td>
            												<td>
            													 <?php echo date('Y/m/d H:i', $order->unix_time);?> 
            												</td>
            												<td>
            													 <?php echo $order->final_total;?>
            												</td>
            												<td>
            													<span class="label label-sm label-<?php echo $order->label;?>">
            													<?php echo $order->status;?> </span>
            												</td>
            												<td>
            													<a href="<?php echo base_url().'orders/admin_order/view_order/'.$order->id;?>" class="btn default btn-xs green-stripe">
            													<?php echo lang('view');?> </a>
            												</td>
            											</tr>
        											<?php }?>
    											
    											</tbody>
											</table>
										</div>
									</div>
                                    
                                    <div class="tab-pane" id="overview_5">
										<div class="table-responsive">
											<table class="table table-striped table-hover table-bordered">
    											<thead>
    											<tr>
    												<th>
    													 <?php echo lang('customer_name');?>
    												</th>
                                                    <th>
                                                        <?php echo lang('order_id');?>
                                                    </th>
    												<th>
    													 <?php echo lang('date');?>
    												</th>
    												<th>
    													 <?php echo lang('amount');?>
    												</th>
    												<th>
    													 <?php echo lang('status');?>
    												</th>
    												<th>
    												</th>
    											</tr>
    											</thead>
    											<tbody>
        											<?php
                                                     $conditions = array('order_status_id' => 2);
                                                     $orders_data = $this->admin_bootstrap->get_conditioned_orders($conditions);
                                                     
                                                     foreach($orders_data as $order)
                                                     {
                                                    ?>
            											<tr>
            												<td>
            													<?php echo $order->first_name.' '.$order->last_name;?>
            												</td>
                                                            <td>
                                                                <a target="_blank" href="<?php echo base_url().'orders/admin_order/view_order/'.$order->id;?>"><?php echo $order->id;?></a>
                                                            </td>
            												<td>
            													 <?php echo date('Y/m/d H:i', $order->unix_time);?> 
            												</td>
            												<td>
            													 <?php echo $order->final_total;?>
            												</td>
            												<td>
            													<span class="label label-sm label-warning">
            													<?php echo $order->status;?> </span>
            												</td>
            												<td>
            													<a href="<?php echo base_url().'orders/admin_order/view_order/'.$order->id;?>" class="btn default btn-xs green-stripe">
            													<?php echo lang('view');?> </a>
            												</td>
            											</tr>
        											<?php }?>
    											
    											</tbody>
											</table>
										</div>
									</div>
                                    
                                    <div class="tab-pane" id="overview_6">
										<div class="table-responsive">
											<table class="table table-striped table-hover table-bordered">
    											<thead>
    											<tr>
    												<th>
    													 <?php echo lang('customer_name');?>
    												</th>
                                                    <th>
                                                        <?php echo lang('order_id');?>
                                                    </th>
    												<th>
    													 <?php echo lang('date');?>
    												</th>
    												<th>
    													 <?php echo lang('amount');?>
    												</th>
    												<th>
    													 <?php echo lang('status');?>
    												</th>
    												<th>
    												</th>
    											</tr>
    											</thead>
    											<tbody>
        											<?php
                                                     $conditions = array('order_status_id' => 1);
                                                     $orders_data = $this->admin_bootstrap->get_conditioned_orders($conditions);
                                                     
                                                     foreach($orders_data as $order)
                                                     {
                                                    ?>
            											<tr>
            												<td>
            													<?php echo $order->first_name.' '.$order->last_name;?>
            												</td>
                                                            <td>
                                                                <a target="_blank" href="<?php echo base_url().'orders/admin_order/view_order/'.$order->id;?>"><?php echo $order->id;?></a>
                                                            </td>
            												<td>
            													 <?php echo date('Y/m/d H:i', $order->unix_time);?> 
            												</td>
            												<td>
            													 <?php echo $order->final_total;?>
            												</td>
            												<td>
            													<span class="label label-sm label-success">
            													<?php echo $order->status;?> </span>
            												</td>
            												<td>
            													<a href="<?php echo base_url().'orders/admin_order/view_order/'.$order->id;?>" class="btn default btn-xs green-stripe">
            													<?php echo lang('view');?> </a>
            												</td>
            											</tr>
        											<?php }?>
    											
    											</tbody>
											</table>
										</div>
									</div>
                                    
                                    <div class="tab-pane" id="overview_7">
										<div class="table-responsive">
											<table class="table table-striped table-hover table-bordered">
    											<thead>
    											<tr>
    												<th>
    													 <?php echo lang('customer_name');?>
    												</th>
                                                    <th>
                                                        <?php echo lang('order_id');?>
                                                    </th>
    												<th>
    													 <?php echo lang('date');?>
    												</th>
    												<th>
    													 <?php echo lang('amount');?>
    												</th>
    												<th>
    													 <?php echo lang('status');?>
    												</th>
    												<th>
    												</th>
    											</tr>
    											</thead>
    											<tbody>
        											<?php
                                                     $conditions = array('order_status_id' => 3);
                                                     $orders_data = $this->admin_bootstrap->get_conditioned_orders($conditions);
                                                     
                                                     foreach($orders_data as $order)
                                                     {
                                                    ?>
            											<tr>
            												<td>
            													<?php echo $order->first_name.' '.$order->last_name;?>
            												</td>
                                                            <td>
                                                                <a target="_blank" href="<?php echo base_url().'orders/admin_order/view_order/'.$order->id;?>"><?php echo $order->id;?></a>
                                                            </td>
            												<td>
            													 <?php echo date('Y/m/d H:i', $order->unix_time);?> 
            												</td>
            												<td>
            													 <?php echo $order->final_total;?>
            												</td>
            												<td>
            													<span class="label label-sm label-danger">
            													<?php echo $order->status;?> </span>
            												</td>
            												<td>
            													<a href="<?php echo base_url().'orders/admin_order/view_order/'.$order->id;?>" class="btn default btn-xs green-stripe">
            													<?php echo lang('view');?> </a>
            												</td>
            											</tr>
        											<?php }?>
    											
    											</tbody>
											</table>
										</div>
									</div>
                                    
								</div>
							</div>
						</div>
					</div>
					<!-- End: life time stats -->
				</div>
                <!-------------------------------->
                
				
			</div>