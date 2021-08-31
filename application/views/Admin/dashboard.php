
			<!-- BEGIN STYLE CUSTOMIZER -->
			<div class="theme-panel hidden-xs hidden-sm">
				<div class="toggler">
				</div>
				<div class="toggler-close">
				</div>
				<div class="theme-options">
					<div class="theme-option theme-colors clearfix">
						<span>
						THEME COLOR </span>
						<ul>
							<li class="color-default current tooltips" data-style="default" data-container="body" data-original-title="Default">
							</li>
							<li class="color-darkblue tooltips" data-style="darkblue" data-container="body" data-original-title="Dark Blue">
							</li>
							<li class="color-blue tooltips" data-style="blue" data-container="body" data-original-title="Blue">
							</li>
							<li class="color-grey tooltips" data-style="grey" data-container="body" data-original-title="Grey">
							</li>
							<li class="color-light tooltips" data-style="light" data-container="body" data-original-title="Light">
							</li>
							<li class="color-light2 tooltips" data-style="light2" data-container="body" data-html="true" data-original-title="Light 2">
							</li>
						</ul>
					</div>
					<div class="theme-option">
						<span>
						Theme Style </span>
						<select class="layout-style-option form-control input-sm">
							<option value="square" selected="selected">Square corners</option>
							<option value="rounded">Rounded corners</option>
						</select>
					</div>
					<div class="theme-option">
						<span>
						Layout </span>
						<select class="layout-option form-control input-sm">
							<option value="fluid" selected="selected">Fluid</option>
							<option value="boxed">Boxed</option>
						</select>
					</div>
					<div class="theme-option">
						<span>
						Header </span>
						<select class="page-header-option form-control input-sm">
							<option value="fixed" selected="selected">Fixed</option>
							<option value="default">Default</option>
						</select>
					</div>
					<div class="theme-option">
						<span>
						Top Menu Dropdown</span>
						<select class="page-header-top-dropdown-style-option form-control input-sm">
							<option value="light" selected="selected">Light</option>
							<option value="dark">Dark</option>
						</select>
					</div>
					<div class="theme-option">
						<span>
						Sidebar Mode</span>
						<select class="sidebar-option form-control input-sm">
							<option value="fixed">Fixed</option>
							<option value="default" selected="selected">Default</option>
						</select>
					</div>
					<div class="theme-option">
						<span>
						Sidebar Menu </span>
						<select class="sidebar-menu-option form-control input-sm">
							<option value="accordion" selected="selected">Accordion</option>
							<option value="hover">Hover</option>
						</select>
					</div>
					<div class="theme-option">
						<span>
						Sidebar Style </span>
						<select class="sidebar-style-option form-control input-sm">
							<option value="default" selected="selected">Default</option>
							<option value="light">Light</option>
						</select>
					</div>
					<div class="theme-option">
						<span>
						Sidebar Position </span>
						<select class="sidebar-pos-option form-control input-sm">
							<option value="left" selected="selected">Left</option>
							<option value="right">Right</option>
						</select>
					</div>
					<div class="theme-option">
						<span>
						Footer </span>
						<select class="page-footer-option form-control input-sm">
							<option value="fixed">Fixed</option>
							<option value="default" selected="selected">Default</option>
						</select>
					</div>
				</div>
			</div>
			<!-- END STYLE CUSTOMIZER -->
			<!-- BEGIN PAGE HEADER-->
			<h3 class="page-title"><?php echo lang('dashboard');?>
			<!--Dashboard <small>reports & statistics</small>-->
			</h3>
			<div class="page-bar">
				<ul class="page-breadcrumb">
					<li>
						<i class="fa fa-home"></i>
						<a href="index.html"><?php echo lang('home');?></a>
						<i class="fa fa-angle-right"></i>
					</li>
					<li>
						<a href="#"><?php echo lang('dashboard');?></a>
					</li>
				</ul>
				<!--<div class="page-toolbar">
					<div id="dashboard-report-range" class="pull-right tooltips btn btn-fit-height grey-salt" data-placement="top" data-original-title="Change dashboard date range">
						<i class="icon-calendar"></i>&nbsp; <span class="thin uppercase visible-lg-inline-block"></span>&nbsp; <i class="fa fa-angle-down"></i>
					</div>
				</div>-->
			</div>
			<!-- END PAGE HEADER-->
			<div class="clearfix">
			</div>


			<div class="row">
        <!-------------------------------->
				<?php if(! $this->data['is_driver']){?>
				<div class="col-md-12">
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
                         $top_selling_products = $this->admin_bootstrap->get_top_selling_products($this->data['stores_ids']);

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
                                 $latest_orders_data = $this->admin_bootstrap->get_latest_orders($this->data['stores_ids']);

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
			<?php }else{?>
				<div class="col-md-12">
				  <!-- Begin: life time stats -->
				  <div class="portlet box blue-steel">
				    <div class="portlet-title">
				      <div class="caption">
				        <i class="fa fa-thumb-tack"></i><?php echo lang ('overview');?>
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
				            <a href="#overview_10" data-toggle="tab">
				            <?php echo ('new_orders');?> </a>
				          </li>

				          <li>
				            <a href="#overview_11" data-toggle="tab">
				            <?php echo lang('completed_orders');?> </a>

				          </li>

				        </ul>
				        <div class="tab-content">

				          <div class="tab-pane" id="overview_11">
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
															$conditions = array('order_status_id' => 2, 'driver_id'=> $this->data['user_id']);
															$orders_data = $this->admin_bootstrap->get_conditioned_orders($conditions);

															foreach($orders_data as $order){
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
				                              <span class="label label-sm label-<?php echo 'success';?>">
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

				            <div class="tab-pane active" id="overview_10">
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
                                 $conditions = array('order_status_id ' => ' != 1', 'driver_id'=> $this->data['user_id']);
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

				        </div>
				      </div>
				    </div>
				  </div>
				  <!-- End: life time stats -->
				</div>

			<?php }?>
                <!-------------------------------->


			</div>


			<div class="clearfix">
			</div>

			<div class="clearfix">
			</div>

			<div class="clearfix">
			</div>

			<div class="clearfix">
			</div>
