<div class="form">
    <span class="error"><?php if(isset($validation_msg)){echo $validation_msg;}?></span>

    <?php $att=array('class'=> 'form-horizontal form-bordered cmxform');
      echo form_open_multipart($form_action, $att);?>
    
    <div class="form-body">
    
    <?php 
      foreach($purchase_order_data as $row){?>
      <div class="form-group">
          <label class="control-label col-md-3">
            <?php echo $row->title."  : ".$row->name;?><span class="required">*</span>
            <div><?php echo lang('serials_count').' : '.$row->quantity;?></div>
            <span><?php echo lang('remaining_serials_count').' : '.$row->remaining_serials;?></span>
          </label>
          
          <div class="col-md-4">
              <?php 
                  if($row->remaining_serials != 0)
                  {
                      echo form_error("serial[$row->product_id-$row->country_id-$row->selected_optional_fields]");
                      $serial_data = array('name'=>"serial[$row->product_id-$row->country_id-$row->selected_optional_fields]" , 'class'=>"form-control" , 'value'=> isset($product_serials[$row->product_id.'-'.$row->country_id.'-'.$row->selected_optional_fields])? implode("\n", $product_serials[$row->product_id.'-'.$row->country_id.'-'.$row->selected_optional_fields]) : set_value("serial[$row->product_id-$row->country_id-$row->selected_optional_fields]"));
                      echo form_textarea($serial_data);
                  }
                  else{?>
                      <span class=""><?php echo lang('all_serials_entered');?>
                  <?php }?>
              
          </div>
          <div class="col-md-4">
              <?php 
                  if($row->remaining_serials != 0)
                  {
                      $file_id=$row->product_id."_".$row->country_id."_".$row->selected_optional_fields;
                      $upload_data = array('name'=>"userfile_$file_id" , 'class'=>"form-control" , 'value'=>  set_value("userfile[$row->product_id-$row->country_id-$row->selected_optional_fields]"), 'accept'=>'.csv, .xlsx, .xls');
                      echo form_upload($upload_data); 
                  }
              ?>
          </div><!--file div -->
          
        
          <?php  echo  form_hidden('purchase_order_id',$row->id) ; ?> 
          <?php 
            if($row->remaining_serials != 0)
            {  
                $products_array = array(
                                      'product_id'=>$row->product_id,
                                      'country_id'=>$row->country_id,
                                      'store_id'  => $row->store_id,
                                      'optional_fields'  => str_replace( '_' , ',' ,$row->optional_fields), // we replace _ with , to be as it is in database
                                      'selected_optional_fields'  => str_replace( '_' , ',' ,$row->selected_optional_fields) // we replace _ with , to be as it is in database
                                      );
                
                echo  form_hidden("product_id[$row->product_id-$row->country_id-$row->selected_optional_fields]", $products_array) ;
                //echo  form_hidden("product_id[$row->product_id-$row->selected_optional_fields][$row->store_id]", $products_array) ;
                echo  form_hidden("product_quantity[$row->product_id-$row->country_id-$row->selected_optional_fields]", $row->quantity) ;
            } 
          ?>
          
      </div>

      <? /*
      // Basic Code
      <div class="form-group">
          <label class="control-label col-md-3">
            <?php echo $row->title."  : ".$row->name;?><span class="required">*</span>
            <div><?php echo lang('serials_count').' : '.$row->quantity;?></div>
            <span><?php echo lang('remaining_serials_count').' : '.$row->remaining_serials;?></span>
          </label>
          
          <div class="col-md-4">
              <?php 
                  if($row->remaining_serials != 0)
                  {
                      echo form_error("serial[$row->product_id][$row->country_id]");
                      $serial_data = array('name'=>"serial[$row->product_id][$row->country_id]" , 'class'=>"form-control" , 'value'=> isset($product_serials[$row->product_id][$row->country_id])? implode("\n", $product_serials[$row->product_id][$row->country_id]) : set_value("serial[$row->product_id][$row->country_id]"));
                      echo form_textarea($serial_data);
                  }
                  else{?>
                      <span class=""><?php echo lang('all_serials_entered');?>
                  <?php }?>
              
          </div>
          <div class="col-md-4">
              <?php 
                  if($row->remaining_serials != 0)
                  {
                      $file_id=$row->product_id."_".$row->country_id;
                      $upload_data = array('name'=>"userfile_$file_id" , 'class'=>"form-control" , 'value'=>  set_value("userfile[$row->product_id][$row->country_id]"), 'accept'=>'.csv, .xlsx, .xls');
                      echo form_upload($upload_data); 
                  }
              ?>
          </div><!--file div -->
          
        
          <?php  echo  form_hidden('purchase_order_id',$row->id) ; ?> 
          <?php 
            if($row->remaining_serials != 0)
            {  
                $products_array = array(
                                      'product_id'=>$row->product_id,
                                      'country_id'=>$row->country_id,
                                      'store_id'  => $row->store_id,
                                      );
                
                echo  form_hidden("product_id[$row->product_id][$row->country_id]", $products_array) ;
                echo  form_hidden("product_id[$row->product_id][$row->store_id]", $products_array) ;
                echo  form_hidden("product_quantity[$row->product_id][$row->country_id]", $row->quantity) ;
            } 
          ?>
          
      </div>
      <?php */ ?>
    <?php }?>  
        
    <div class="form-actions">
      <div class="row">
        <div class="col-md-offset-3 col-md-9">
          <?php  echo isset($id) ? form_hidden('id', $id) : ''; ?> 
            <button type="submit"  class="btn green"><i class="fa fa-check"></i><?php echo lang('submit');?></button>
            <a href="<?php echo base_url();?>products/admin_products_serials/download" class="btn blue"><i class="fa fa-cloud-download"></i> <?php echo lang('download_csv_sample');?> </a>
        </div>
      </div>
    </div>
        
    </div>
    <?php echo form_close();?>
  
</div>
