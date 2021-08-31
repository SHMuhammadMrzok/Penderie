<!--AmCharts-->
<script src="//www.amcharts.com/lib/3/amcharts.js"></script>
<script src="//www.amcharts.com/lib/3/pie.js"></script>
<script src="https://www.amcharts.com/lib/3/serial.js"></script>
<script src="https://www.amcharts.com/lib/3/xy.js"></script>
<script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
<link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
<script src="//www.amcharts.com/lib/3/themes/light.js"></script>
<!--End AmCharts-->

<div class="clearfix">
</div>

<?php /*
<div class="row">
    <!-------------------------------->
    <div class="col-md-12">
		<!-- Begin: life time stats -->
		<div class="portlet box blue-steel" style="background-color: #b09433; margin-top: 5px;">
			<div class="portlet-title" style="background-color: #b09433;">
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
			<div class="portlet-body" >
				<div class="tabbable-line">
					<ul class="nav nav-tabs" style="margin: 5px;">
						<li class="active">
							<a href="#overview_1" data-toggle="tab">
							<?php echo lang('top_selling');?> </a>
						</li>

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
                <?php /*
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
                        */?>
<?php /*					</ul>
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
                   $top_selling_products = $this->admin_bootstrap->get_top_selling_products($stores_ids);

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



						<div class="tab-pane" id="overview_4">
							<div class="table-responsive">
								<table class="table table-striped table-hover table-bordered">
									<thead>
									<tr>
										<th><?php echo lang('customer_name');?></th>
                                        <th><?php echo lang('order_id');?></th>
										<th><?php echo lang('date');?></th>
										<th><?php echo lang('amount');?></th>
										<th><?php echo lang('status');?></th>
										<th></th>
									</tr>
									</thead>
									<tbody>
										<?php
                       $latest_orders_data = $this->admin_bootstrap->get_latest_orders($stores_ids);

                       if(count($latest_orders_data) != 0){
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
										<?php }
                      }else
                      {?>
                          <tr>
                              <td colspan="7"><?php echo lang('no_data');?></td>
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
										<th><?php echo lang('customer_name');?></th>
                                        <th><?php echo lang('order_id');?></th>
										<th><?php echo lang('date');?></th>
										<th><?php echo lang('amount');?></th>
										<th><?php echo lang('status');?></th>
										<th></th>
									</tr>
									</thead>
									<tbody>
										<?php
                                         $conditions = array('order_status_id' => 2);
                                         $orders_data = $this->admin_bootstrap->get_conditioned_orders($conditions, $stores_ids);

                                         if(count($orders_data) != 0){
                                         foreach($orders_data as $order)
                                         {
                                        ?>
											<tr>
												<td>
													<?php echo $order->first_name.' '.$order->last_name;?>
												</td>
                                                <td><a target="_blank" href="<?php echo base_url().'orders/admin_order/view_order/'.$order->id;?>"><?php echo $order->id;?></a></td>
												<td><?php echo date('Y/m/d H:i', $order->unix_time);?></td>
												<td><?php echo $order->final_total;?></td>
												<td>
													<span class="label label-sm label-warning">
													<?php echo $order->status;?> </span>
												</td>
												<td>
													<a href="<?php echo base_url().'orders/admin_order/view_order/'.$order->id;?>" class="btn default btn-xs green-stripe">
													<?php echo lang('view');?> </a>
												</td>
											</tr>
										<?php }
                                        }else
                                        {?>
                                            <tr>
                                                <td colspan="7"><?php echo lang('no_data');?></td>
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
										<th><?php echo lang('customer_name');?></th>
                                        <th><?php echo lang('order_id');?></th>
										<th><?php echo lang('date');?></th>
										<th><?php echo lang('amount');?></th>
										<th><?php echo lang('status');?></th>
										<th></th>
									</tr>
									</thead>
									<tbody>
										<?php
                                         $conditions = array('order_status_id' => 1);
                                         $orders_data = $this->admin_bootstrap->get_conditioned_orders($conditions, $stores_ids);

                                         if(count($orders_data) != 0){
                                         foreach($orders_data as $order)
                                         {
                                        ?>
											<tr>
												<td><?php echo $order->first_name.' '.$order->last_name;?></td>
                                                <td>
                                                    <a target="_blank" href="<?php echo base_url().'orders/admin_order/view_order/'.$order->id;?>"><?php echo $order->id;?></a>
                                                </td>
												<td><?php echo date('Y/m/d H:i', $order->unix_time);?></td>
												<td><?php echo $order->final_total;?></td>
												<td>
													<span class="label label-sm label-success">
													<?php echo $order->status;?> </span>
												</td>
												<td>
													<a href="<?php echo base_url().'orders/admin_order/view_order/'.$order->id;?>" class="btn default btn-xs green-stripe">
													<?php echo lang('view');?> </a>
												</td>
											</tr>
										<?php }}else
                                        {?>
                                            <tr>
                                                <td colspan="7"><?php echo lang('no_data');?></td>
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
										<th><?php echo lang('customer_name');?></th>
                                        <th><?php echo lang('order_id');?></th>
										<th><?php echo lang('date');?></th>
										<th><?php echo lang('amount');?></th>
										<th><?php echo lang('status');?></th>
										<th></th>
									</tr>
									</thead>
									<tbody>
										<?php
                                         $conditions = array('order_status_id' => 3);
                                         $orders_data = $this->admin_bootstrap->get_conditioned_orders($conditions, $stores_ids);

                                         if(count($orders_data) != 0){
                                         foreach($orders_data as $order)
                                         {?>
											<tr>
												<td><?php echo $order->first_name.' '.$order->last_name;?></td>
                                                <td><a target="_blank" href="<?php echo base_url().'orders/admin_order/view_order/'.$order->id;?>"><?php echo $order->id;?></a></td>
												<td><?php echo date('Y/m/d H:i', $order->unix_time);?></td>
												<td><?php echo $order->final_total;?></td>
												<td>
													<span class="label label-sm label-danger">
													<?php echo $order->status;?> </span>
												</td>
												<td>
													<a href="<?php echo base_url().'orders/admin_order/view_order/'.$order->id;?>" class="btn default btn-xs green-stripe">
													<?php echo lang('view');?> </a>
												</td>
											</tr>
										<?php }
                                        }else
                                        {?>
                                            <tr>
                                                <td colspan="7"><?php echo lang('no_data');?></td>
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

<div class="clearfix"></div>

<div class="col-md-6">
    <div class="portlet light ">
        <div class="portlet-title tabbable-line">
            <div class="caption caption-md">
                <i class="icon-globe theme-font hide"></i>
                <span class="caption-subject font-blue-madison bold uppercase"><?php echo lang('cats_sales');?></span>
            </div>
        </div>

        <div class="portlet-body" dir="ltr">

            <?php if(count($cats_sales) != 0){?>
            <!-- Chart code -->
                <script>
                var chart = AmCharts.makeChart("cats_sales_chartdiv", {
                      "theme": "light",
                      "type": "serial",
                      "dataProvider": [
                      <?php foreach($cats_sales as $row){?>
                          {
                            "country": "<?php echo $row['cat_name'];?>",
                            "sales": <?php echo $row['cat_sales'];?>
                          },
                       <?php }?>
                       ],
                      "valueAxes": [{
                        "unit": " <?php echo $default_currency;?>",
                        "position": "left",
                        "title": "category sales",
                      }],
                      "rotate": true,
                      "startDuration": 1,
                      "graphs": [{
                        "balloonText": "category total sales [[category]] : <b>[[value]](<?php echo $default_currency;?>)</b>",
                        "fillAlphas": 0.9,
                        "lineAlpha": 0.2,
                        "title": "2004",
                        "type": "column",
                        "valueField": "sales"
                      }],
                      "plotAreaFillAlphas": 0.1,
                      "categoryField": "country",
                      "categoryAxis": {
                        "gridPosition": "start"
                      }
                    });
                </script>
        <?php }?>
        <!-- HTML -->
        <div id="cats_sales_chartdiv" class="chart"></div>
	</div>
    </div>
</div>

<div class="col-md-6">
    <div class="portlet light ">
        <div class="portlet-title tabbable-line">
            <div class="caption caption-md">
                <i class="icon-globe theme-font hide"></i>
                <span class="caption-subject font-blue-madison bold uppercase"><?php echo lang('products').' '.lang('top_selling');?></span>
            </div>
        </div>

        <div class="portlet-body" dir="ltr">

            <!-- Chart code -->
            <script>
            var chart = AmCharts.makeChart("most_products_chartdiv", {
                  "theme": "light",
                  "type": "serial",
                  "dataProvider": [
                  <?php foreach($most_products as $row){?>
                      {
                        "country": "<?php echo $row->title;?>",
                        "sales": <?php echo $row->count;?>
                      },
                   <?php }?>
                   ],
                  "valueAxes": [{
                    "unit": " ",
                    "position": "left",
                    "title": "Most Sold Products",
                  }],
                  "rotate": true,
                  "startDuration": 1,
                  "graphs": [{
                    "balloonText": "Most Sold Product [[category]] : <b>[[value]]</b>",
                    "fillAlphas": 0.9,
                    "lineAlpha": 0.2,
                    "title": "2004",
                    "type": "column",
                    "valueField": "sales"
                  }],
                  "plotAreaFillAlphas": 0.1,
                  "categoryField": "country",
                  "categoryAxis": {
                    "gridPosition": "start"
                  }
                });
            </script>

        <!-- HTML -->
        <div id="most_products_chartdiv" class="chart"></div>
	</div>
    </div>
</div>

<div class="clearfix"></div>

<div class="col-md-6">
    <div class="portlet light ">
        <div class="portlet-title tabbable-line">
            <div class="caption caption-md">
                <i class="icon-globe theme-font hide"></i>
                <span class="caption-subject font-blue-madison bold uppercase"><?php echo lang('month_sales');?></span>
            </div>
        </div>

        <div class="portlet-body" dir="ltr">

            <!-- Chart code -->
            <script>
            /**
             * Plugin: generate a Pareto line for a graph
             * Relies on `paretoGraph` object in graphs' definition
             */


            /**
             * Create a chart
             */
 /*            var chart = AmCharts.makeChart( "month_sales_chartdiv", {
              "type": "serial",
              "theme": "light",
              "dataProvider": [
              <?php foreach($month_sales as $month=>$row){;?>
              {
                "year": "<?php echo $month.' / '.$row['year'];?>",
                "sales": <?php echo $row['sales'];?>
              },
              <?php }?>  ],
              "valueAxes": [ {
                "id": "v1",
                "gridColor": "#FFFFFF",
                "gridAlpha": 0.2,
                "dashLength": 0
              } ],
              "gridAboveGraphs": true,
              "startDuration": 1,
              "graphs": [ {
                "balloonText": "[[year]]: <b>orders total = [[value]] <?php echo $default_currency;?></b>",
                "fillAlphas": 0.8,
                "lineAlpha": 0.2,
                "type": "column",
                "valueField": "sales",

              } ],
              "chartCursor": {
                "categoryBalloonEnabled": false,
                "cursorAlpha": 0,
                "zoomable": false
              },
              "categoryField": "year",
              "categoryAxis": {
                "gridPosition": "start",
                "gridAlpha": 0,
                "tickPosition": "start",
                "tickLength": 20
              }

            } );
            </script>


            <!-- HTML -->
            <div id="month_sales_chartdiv" class="chart"></div>
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="portlet light ">
        <div class="portlet-title tabbable-line">
            <div class="caption caption-md">
                <i class="icon-globe theme-font hide"></i>
                <span class="caption-subject font-blue-madison bold uppercase"><?php echo lang('status_sales');?></span>
            </div>
        </div>

        <div class="portlet-body" dir="ltr">

            <!-- Chart code -->
            <script>
            /**
             * Create a chart
             */
 /*           var chart = AmCharts.makeChart( "status_sales_chartdiv", {
              "type": "serial",
              "theme": "light",
              "dataProvider": [
              <?php foreach($status_sales as $row){;?>
              {
                "status": "<?php echo $row['status'];?>",
                "sales": <?php echo $row['sales'];?>
              },
              <?php }?>  ],
              "valueAxes": [ {
                "id": "v1",
                "gridColor": "#FFFFFF",
                "gridAlpha": 0.2,
                "dashLength": 0
              } ],
              "gridAboveGraphs": true,
              "startDuration": 1,
              "graphs": [ {
                "balloonText": "[[status]]: <b>orders total = [[value]] <?php echo $default_currency;?></b>",
                "fillAlphas": 0.8,
                "lineAlpha": 0.2,
                "type": "column",
                "valueField": "sales",

              } ],
              "chartCursor": {
                "categoryBalloonEnabled": false,
                "cursorAlpha": 0,
                "zoomable": false
              },
              "categoryField": "status",
              "categoryAxis": {
                "gridPosition": "start",
                "gridAlpha": 0,
                "tickPosition": "start",
                "tickLength": 20
              }

            } );
            </script>


            <!-- HTML -->
            <div id="status_sales_chartdiv" class="chart"></div>
        </div>
    </div>
</div>


<div class="clearfix"></div>

<div class="col-md-12">
    <div class="portlet light ">
        <div class="portlet-title tabbable-line">
            <div class="caption caption-md">
                <i class="icon-globe theme-font hide"></i>
                <span class="caption-subject font-blue-madison bold uppercase"><?php echo lang('countries_sales');?></span>
            </div>
        </div>

        <div class="portlet-body" dir="ltr">

        <!-- Chart code -->
        <script>
            var chart = AmCharts.makeChart( "countries_sales_chartdiv", {
              "type": "pie",
              "theme": "light",
              "legend":{
                   "position":"right",
                "marginRight":100,
                "autoMargins":true
              },
              "dataProvider": [
              <?php foreach($countries_sales as $row){?>
              {
                "country": "<?php echo $row->country_name.' ( '.$default_currency.' )';?>",
                "value": <?php echo $row->orders_total;?>
              },
              <?php }?>
               ],
              "valueField": "value",
              "titleField": "country",
              "outlineAlpha": 0.4,
              "depth3D": 15,
              "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
              "angle": 30,
              "export": {
                "enabled": false
              }
            } );
        </script>

    <!-- HTML -->
    <div id="countries_sales_chartdiv" class="chart"></div>
      </div>
    </div>
</div>

<div class="clearfix"></div>

<div class="col-md-12">
    <div class="portlet light ">
        <div class="portlet-title tabbable-line">
            <div class="caption caption-md">
                <i class="icon-globe theme-font hide"></i>
                <span class="caption-subject font-blue-madison bold uppercase"><?php echo lang('agents_sales');?></span>
            </div>
        </div>

        <div class="portlet-body" dir="ltr">

            <!-- Chart code -->
            <script>
                var chart = AmCharts.makeChart( "agents_sales_chartdiv", {
                  "type": "pie",
                  "theme": "light",
                  "legend":{
                       "position":"right",
                    "marginRight":100,
                    "autoMargins":true
                  },
                  "dataProvider": [
                  <?php foreach($agents_sales as $agent=>$value){?>
                  {
                    "agent": "<?php echo substr($agent, 0, 30);?>",
                    "value": <?php echo $value;?>
                  },
                  <?php }?>
                   ],
                  "valueField": "value",
                  "titleField": "agent",
                  "outlineAlpha": 0.4,
                  "depth3D": 15,
                  "balloonText": "Agent total sales[[title]]<br><span style='font-size:14px'><b>[[value]](<?php echo $default_currency;?>)</b> ([[percents]]%)</span>",
                  "angle": 30,
                  "export": {
                    "enabled": false
                  }
                } );
            </script>

            <!-- HTML -->
            <div id="agents_sales_chartdiv" class="chart"></div>
        </div>
    </div>
</div>

<div class="clearfix"></div>

<div class="col-md-12">
    <div class="portlet light ">
        <div class="portlet-title tabbable-line">
            <div class="caption caption-md">
                <i class="icon-globe theme-font hide"></i>
                <span class="caption-subject font-blue-madison bold uppercase"><?php echo lang('payment_methods_sales');?></span>
            </div>
        </div>

        <div class="portlet-body" dir="ltr">

            <!-- Chart code -->
            <script>
                var chart = AmCharts.makeChart( "payment_methods_sales_chartdiv", {
                  "type": "pie",
                  "theme": "light",
                  "legend":{
                       "position":"right",
                    "marginRight":100,
                    "autoMargins":true
                  },
                  "dataProvider": [
                  <?php foreach($payment_methods_sales as $row){?>
                  {
                    "payment_method": "<?php echo $row->payment_method;?>",
                    "value": <?php echo $row->orders_count;?>
                  },
                  <?php }?>
                   ],
                  "valueField": "value",
                  "titleField": "payment_method",
                  "outlineAlpha": 0.4,
                  "depth3D": 15,
                  "balloonText": "order count[[title]]<br><span style='font-size:14px'><b>[[value]] orders</b> ([[percents]]%)</span>",
                  "angle": 30,
                  "export": {
                    "enabled": false
                  }
                } );
            </script>

            <!-- HTML -->
            <div id="payment_methods_sales_chartdiv" class="chart"></div>
        </div>
    </div>
</div>
*/?>

<div class="clearfix"></div>
