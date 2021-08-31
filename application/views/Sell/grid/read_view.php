    <div class="row">
        <div class="col-md-12">
            <?php if(isset($error_msg)){?>
                <span class="error"><?php echo $error_msg;?></span>
            <?php }else{
                if($controller == 'admin_products_discounts'){
                    if(!empty($read_data))
                    { ?>
                        <div class="table-area">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
            						<tr class="header-ta">
            							<td> <?php echo lang('product_name');?></td>
                                        <td> <?php echo lang('country');?></td>
                                        <td> <?php echo lang('price');?></td>
                                        <td> <?php echo lang('discount_start_unix_time');?> </td>
                                        <td> <?php echo lang('discount_end_unix_time');?></td>
                                        <td> <?php echo lang('max_units_customers');?></td>
                                        <td> <?php echo lang('active');?></td>
            						</tr>
        						</thead>
                                <tbody>
                                    <?php foreach ($read_data as $key=>$row){?>
                                        <tr>
                                            <td><?php echo $row->product_name;?></td>
                                            <td><?php echo $row->country;?></td>
                                            <td><?php echo $row->price;?></td>
                                            <td><?php echo date('Y-m-d ',$row->discount_start_unix_time);?></td>
                                            <td><?php echo date('Y-m-d ',$row->discount_end_unix_time);?></td>
                                            <td><?php echo $row->max_units_customers;?></td>
                                            <td><?php echo $row->active_value;?></td>
                                        </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                        </div>
                    <?php }else{?>
                            <div class="title">
                                <h3><?php echo lang('no_data');?></h3>
                            </div>
                    <?php }// if !empty?>
                <?php } else {
                    if(!empty($row_data))
                    { ?>
                        <div class="table-area">
                            <table class="table table-striped table-bordered table-hover">
                                <tbody>
                                    <?php 
                                    foreach($row_data as $column_name => $column_data)
                                    {
                                        if(!empty($column_data))
                                        {
                                            ?>
                                        <tr>
                                            <td><?php echo $column_name;?></td>
                                            <td><?php echo $column_data;?></td>
                                        </tr>
                                    <?php }
                                    }?>
                                </tbody>
                            </table>
                        </div>
                    <?php }else{?>
                            <div class="title">
                                <h3><?php echo lang('no_data');?></h3>
                            </div>
                    <?php }// if !empty?>
            <?php }
            } ?>
        </div><!--col-->
    </div><!--row-->