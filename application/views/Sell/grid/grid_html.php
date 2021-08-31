<?php  $this->load->view('Sell/grid/grid_js'); ?>

<style>
    table.sorting-table {cursor: move;}
    table tr.sorting-row td {background-color: #E0FFE0;}
</style>

<div class="right-content">
    <div class="row portlet-body">
        <div class="col-12 col-sm-4">
            <div class="form-group">
                <div class="form-item">
                    <label><?php echo lang('search');?></label>
                    <input type="text" class="form-control"  name="search_word" id="search_word"  placeholder="<?php echo lang('search');?>">
                </div>
            </div>
        </div>

        <?php if(isset($search_fields_data)){?>
            <div class="col-12 col-sm-4">
                <div class="form-group">
                    <div class="form-item">
                        <label><?php echo lang('search_fields');?></label>

                        <select name="search_field" class="search_field_val form-control ">
                             <?php foreach($search_fields_data as $index=>$field){?>
                                <option value="<?php echo $index;?>"><?php echo $field;?></option>
                            <?php }?>
                        </select>
                    </div>
                </div>
            </div>
        <?php }?>


        <div class="col-12 col-sm-4">
            <div class="form-group">
                <div class="form-item">
                    <label><?php echo lang('display_language');?></label>
                    <select name="lang_id" id="lang_id" class="form-control ">
                        <?php foreach($data_language as $lang){?>
                    	<option value="<?php echo $lang->id?>"><?php echo $lang->name?></option>
                        <?php }?>
                    </select>
                </div>
            </div>
        </div>


        <?php if(isset($orders)){?>
            <div class="col-12 col-sm-4">
                <div class="form-group">
                    <div class="form-item">
                        <label><?php echo lang('order_by');?></label>

                        <select name="order_by" id="order_by" class="form-control">
                            <option value=""></option><!---->
                            <?php foreach($orders as $order){?>
                        	   <option value="<?php echo $order;?>"><?php echo $order;?></option>
                            <?php }?>

                        </select>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4">
                <div class="form-group">
                    <div class="form-item">
                        <label><?php echo lang('order_by');?></label>
                        <select name="order_state" id="order_state" class="form-control ">
                            <option value="desc"><?php echo lang('desc');?></option>
                           	<option value="asc"><?php echo lang('asc');?></option>
                        </select>
                    </div>
                </div>
            </div>
        <?php }?>

    </div>

    <table class="table table-striped ">
        <thead>
            <tr>
                <th width="5%">
                    <input type="checkbox" class="group-checkable" name="chkall" id="check_all" />
                </th>

                <?php foreach($columns as $column_name){ ?>
                <th>
					 <?php echo $column_name;?>
				</th>
                <?php }?>

                <?php if(! (isset($unset_view) && isset($unset_edit) && isset($unset_delete)) & !isset($unset_actions)){?>
                    <th>
    					 <?php echo lang('actions');?>
    				</th>
                <?php }?>

            </tr>
        </thead>
    <tbody id="result_data">

    </tbody>
</table>

<nav class="NavPageNum">
    <div class="row align-items-center">
        <?php if(isset($actions)){?>
            <div class="col-12 col-sm-4">
                <select id="checked_action" class="form-control">
                    	<option value=""><?php echo lang('select');?>...</option>
                        <?php foreach($actions as $value => $action){ ?>
						                  <option value="<?php echo $value;?>"><?php echo $action;?></option>
                        <?php }?>
                </select>
                <a class="btn btn-sm yellow table-group-action-submit delete_alert" id="submit_delete" href="#"><i class="fa fa-check"></i><?php echo lang('submit_action');?></a>
            </div>
            
        <?php }?>

        <div class="col-12 col-sm-4">
            <div class="pagination">
                <div>

                    <a href="#" id="prev_page" class="btn btn-sm default prev " title="Prev" disabled><span>

<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
</svg>
</span></a>
                    <input type="text" readonly="readonly" id="page_number" value="1" class="pagination-panel-input form-control input-mini input-inline input-sm" maxlenght="5" />
                    <a href="#" id="next_page" class="btn btn-sm default next " title="Next"><span >
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-left" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
</svg>
                        </span></a> <?php echo lang('of');?>
                    <span id="total_pages" class="pagination-panel-total"></span>

                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">

            <select name="limit" id="limit" aria-controls="datatable_invoices" class="form-control ">
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="150">150</option>
                <option value="300">300</option>
            </select> <?php //echo lang('record');?>
        </div>
    </div>

</nav>


</div>
