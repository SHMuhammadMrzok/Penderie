<?php  require(APPPATH . 'includes/grid_js.php'); ?>

<div class="row">
	<div class="col-md-12 col-sm-12">	
        <div class="portlet-body">
            <div class="table-responsive">
                
        	   <div class="pull-right"> 
                    <label>
                        <span class="col-md-3 control-label"><?php echo lang('search');?> </span> 
                        <div class="col-md-9">
                            <input class="form-control" name="search_word" id="search_word" />
                        </div>
                    </label>
        		</div>
                
                
                   <div class="pull-left" style="margin: 0 10px;">
                        <label>
                            <span><?php echo lang('display_language');?> :</span>
                			<select name="lang_id" id="lang_id" class="form-control select2  input-inline input-small">
                			 <?php foreach($data_language as $lang){?>
                            	<option value="<?php echo $lang->id?>"><?php echo $lang->name?></option>
                                <?php }?>
                				
                				
                			</select>
                        </label>
            		</div>
                    
                
                <div style="height: 9px;"></div>
    			<table class="table table-hover table-bordered table-striped">
        			<thead>
            			<tr>
                            <th width="5%">
                                <input type="checkbox" class="group-checkable" name="chkall" id="check_all" />
                            </th>
                            <th>
            					 <?php echo lang('country');?>
            				</th>
            				<th>
            					 <?php echo lang('currency');?> 
            				</th>
            				<th>
            					 <?php echo lang('flag');?>
            				</th>
            				<th>
            					 <?php echo lang('actions');?>
            				</th>
                           
            				
            			</tr>
        			</thead>
        			<tbody id="result_data"></tbody>
    			</table>
                
                
                <!--Start Table Options-->
                <div class="table-container">
					<div id="datatable_invoices_wrapper" class="dataTables_wrapper dataTables_extended_wrapper no-footer">
                        <div>
                            <div class="col-md-4 col-sm-12">
                                <div class="table-group-actions">
                                    <span></span>
        						    <select id="checked_action" class="table-group-action-input form-control input-inline input-small input-sm">
        								<option value=""><?php echo lang('select');?>...</option>
        								<option value="delete"><?php echo lang('delete');?></option>
        							</select>
                                    
        							<a class="btn btn-sm yellow table-group-action-submit delete_alert" id="submit_delete" href="#"><i class="fa fa-check"></i><?php echo lang('submit_action');?></a>
        					   </div>
                            </div>
                            <div class="col-md-8 col-sm-12">
                                <div class="dataTables_paginate paging_bootstrap_extended" id="datatable_invoices_paginate">
                                    <div class="pagination-panel"> <?php echo lang('page');?> 
                                        <a href="#" id="prev_page" class="btn btn-sm default prev " title="Prev" disabled><i class="fa fa-angle-left"></i></a>
                                        <input type="text" id="page_number" value="1" class="pagination-panel-input form-control input-mini input-inline input-sm" maxlenght="5" style="text-align:center; margin: 0 5px;">
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
