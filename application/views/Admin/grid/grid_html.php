<?php  $this->load->view('Admin/grid/grid_js'); ?>

<style>
    table.sorting-table {cursor: move;}
    table tr.sorting-row td {background-color: #E0FFE0;}
</style>

<div class="row">
	<div class="col-md-12 col-sm-12">
        <div class="portlet-body">
            <div class="table-responsive">
               <div class="">
            	   <div class="pull-right">
                        <label>
                            <span class="col-md-3 control-label"><?php echo lang('search');?> </span>
                            <div class="col-md-9">
                                <input class="form-control" name="search_word" id="search_word" />
                                <?php if(isset($search_fields)){?>
                                    <span class="search_fieds_span"><?php echo lang('search_fields');?> : </span>
                                    <span class="search_fields">
                                    <?php foreach($search_fields as $field){
                                        echo $field.' , ';
                                    }?>
                                    </span>
                                <?php }?>
                            </div>
                        </label>
            		</div>

                    <?php if(isset($search_fields_data)){?>
                        <div class="pull-right">
                            <label>
                                <span class="col-md-3 control-label"><?php echo lang('search_fields');?> </span>
                                <div class="col-md-9">
                                    <select name="search_field" class="search_field_val form-control select2  input-inline input-small">
                                        <?php foreach($search_fields_data as $index=>$field){?>
                                            <option value="<?php echo $index;?>"><?php echo $field;?></option>
                                        <?php }?>
                                    </select>

                                </div>
                            </label>
                		</div>
                  <?php }?>


                   <div class="pull-left" style="margin: 0 20px;">
                        <label>
                            <span><?php echo lang('display_language');?> :</span>
                      			<select name="lang_id" id="lang_id" class="form-control select2  input-inline input-small">
                      			 <?php foreach(array_reverse($data_language) as $lang){?>
                              	<option value="<?php echo $lang->id?>"><?php echo $lang->name?></option>
                              <?php }?>
                      			</select>
                        </label>
            		   </div>

                    <?php
                        if(isset($orders))
                        {?>
                            <div class="pull-left" style="margin: 0 20px;">
                                <label>
                                    <span><?php echo lang('order_by');?> :</span>
                        			<select name="order_by" id="order_by" class="form-control select2  input-inline input-small">
                        			    <option value=""><?php //echo 'id';?></option><!---->
                                        <?php foreach($orders as $order){?>
                                    	   <option value="<?php echo $order;?>"><?php echo $order;?></option>
                                        <?php }?>


                        			</select>
                                </label>
                    		</div>

                            <div class="pull-left" style="margin: 0 20px;">
                                <label>
                                    <span></span>
                        			<select name="order_state" id="order_state" class="form-control select2  input-inline input-small">

                                    	<option value="desc"><?php echo lang('desc');?></option>
                                       	<option value="asc"><?php echo lang('asc');?></option>

                        			</select>
                                </label>
                    		</div>
                      <?php }?>

                </div>

                <div class="clearfix"></div>

                <div class="">

                    <?php
                     if(isset($filters))
                     {
                        foreach($filters as $filter)
                        {?>
                            <div class="pull-left" style="margin: 0 20px;">
                                <label>
                                    <span><?php echo $filter['filter_title']; ?> :</span>
                        			<?php //echo  form_dropdown($filter['filter_name'], $filter['filter_data'], 0, 'class="form-control select2"');?>

                                    <select name="<?php echo $filter['filter_name']; ?>" id="<?php echo $filter['filter_name']; ?>" class="form-control select2  input-inline input-small grid-filter">
                        			    <option value="0">-----------------</option>

                                        <?php if(isset($filter['custom_filter']))
                                              {
                                                echo $filter['custom_filter'];
                                              }
                                              else
                                              {
                                                foreach($filter['filter_data'] as $data)
                                                {?>
                                                     <option value="<?php echo $data->id; ?>"><?php echo $data->name; ?></option>
                                          <?php }
                                        }?>

                        			</select>
                                </label>
                		  </div>
                    <?php
                       }
                    }
                    ?>

                </div>
                <div class="clearfix"></div>

                 <?php if(isset($date_filter)){?>
                    <div>
                        <span><?php echo lang('date_filter');?> :</span>
                        <div class="input-group input-large date-picker input-daterange" data-date="11-10-2012" data-date-format="dd-mm-yyyy">

                            <?php
                                $start_data = array('class'=>"form-control", 'id'=>'date_from' );
                                echo form_input($start_data);
                            ?>

                            <span class="input-group-addon"><?php echo lang('to');?> </span>

                            <?php
                                $end_data = array('class'=>"form-control", 'id'=>'date_to');
                                echo form_input($end_data);
                            ?>
        				</div>
                    </div>
                <?php }?>

                <div style="height: 9px;"></div>
                <div style="overflow-x: scroll;">
        			<table class="table table-hover table-bordered table-striped" id="grid_table">
            			<thead>
                			<tr>
                                <th width="5%">
                                    <input type="checkbox" class="group-checkable" name="chkall" id="check_all" />
                                </th>
                                <?php foreach($columns as $column_name){ ?>
                                <th style="text-align: center;">
                					 <?php echo $column_name;?>
                				</th>
                                <?php }?>

                                <?php //if (!isset($unset_actions)){?>
                                <?php if(! (isset($unset_view) && isset($unset_edit) && isset($unset_delete)) & !isset($unset_actions)){?>
                                    <th>
                    					 <?php echo lang('actions');?>
                    				</th>
                                <?php }?>
                			</tr>
            			</thead>
            			<tbody id="result_data"></tbody>
        			</table>
                </div>

                <!--Start Table Options-->
                <div class="table-container">
					<div id="datatable_invoices_wrapper" class="dataTables_wrapper dataTables_extended_wrapper no-footer">
                        <div>
                            <?php if(isset($actions)){?>
                                <div class="col-md-4 col-sm-12">
                                    <div class="table-group-actions">
                                        <span></span>
            						    <select id="checked_action" class="table-group-action-input form-control input-inline input-small input-sm">
            								<option value=""><?php echo lang('select');?>...</option>
                                            <?php foreach($actions as $value => $action){ ?>
            								<option value="<?php echo $value;?>"><?php echo $action;?></option>
                                            <?php }?>
            							</select>

            							<a class="btn btn-sm yellow table-group-action-submit delete_alert" id="submit_delete" href="#"><i class="fa fa-check"></i><?php echo lang('submit_action');?></a>
            					   </div>
                                </div>
                            <?php }?>
                            <div class="col-md-8 col-sm-12">
                                <div class="dataTables_paginate paging_bootstrap_extended" id="datatable_invoices_paginate">
                                    <div class="pagination-panel"> <?php echo lang('page');?>
                                        <a href="#" id="prev_page" class="btn btn-sm default prev " title="Prev" disabled><i class="fa fa-angle-left"></i></a>
                                        <input type="text" readonly="readonly" id="page_number" value="1" class="pagination-panel-input form-control input-mini input-inline input-sm" maxlenght="5" style="text-align:center; margin: 0 5px;">
                                        <a href="#" id="next_page" class="btn btn-sm default next " title="Next"><i class="fa fa-angle-right"></i></a> <?php echo lang('of');?>
                                        <span id="total_pages" class="pagination-panel-total"></span>
                                    </div>
                                </div>
                                <div class="dataTables_length" id="datatable_invoices_length">
                                    <label><span class="seperator">|</span><?php echo lang('view');?>
                                        <select name="limit" id="limit" aria-controls="datatable_invoices" class="form-control input-xsmall input-sm input-inline">
                                            <option value="20">20</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="150">150</option>
                                            <option value="300">300</option>
                                        </select> <?php echo lang('record');?>
                                    </label>
                                </div>


                            </div>

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <!--END Table Options-->
    		</div>
    	</div>

	</div>
</div>
<div id="log"></div>
<!--
<script type="text/javascript">
    $(function(){
       get_grid_data('<?php echo base_url().'users/admin_users/'.$method.'/'; ?>');

       $('body').on('click', '.page_links', function(e){
            e.preventDefault();
            get_grid_data($(this).attr('href'));
       });

    });

    function get_grid_data(url)
    {
        $.post(url, null, function(result){
            $('#grid_data').html(result);
        });
    }

</script>
-->
