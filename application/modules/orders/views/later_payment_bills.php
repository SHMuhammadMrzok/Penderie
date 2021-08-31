<div class="form">

        
    <?php
        if(isset($error_msg))
        {?>
        <div class="animated fadeInDown">
            <div class="card">
            
                    <div class="alert alert-danger">
                        <center><b><span class="error"><?php echo $error_msg; ?> </span></b></center>
                    </div>
                
            </div>
        </div>
            <!--<span class="error"><?php //echo $error_msg;?></span>-->
    <?php
        }else{
    ?>
    <span class="error"><?php if(isset($validation_msg)){echo $validation_msg;}?></span>

    <?php
        $att = array('class'=> 'form-horizontal form-bordered');
                      echo form_open_multipart($form_action, $att);?>
    <div class="tabbable-custom form">


	   <ul class="nav nav-tabs ">
	       <li class="active" >
    		<a href="#tab_general" data-toggle="tab">
                <span class="langname"><?php echo lang('general'); ?> </span>
            </a>
    	   </li>
	   </ul>
       
    	<div class="tab-content">
            <div class="tab-pane active" id="tab_general">
    	      <div class="form-body">

                <div class="form-group">
                   <label class="control-label col-md-3">
                     <?php echo lang('Amount_of_money');?><span class="required">*</span>
                   </label>
                   <div class="col-md-4">
                      <?php
                            echo form_error("amount");
                            $name_data = array('name'=>"amount" , 'class'=>"form-control" , 'value'=> '');
                            echo form_input($name_data);
                      ?>
                    </div>
                </div>
                
                <div class="form-group">
                   <label class="control-label col-md-3">
                     <?php echo lang('notes');?><span class="required">*</span>
                   </label>
                   <div class="col-md-4">
                      <?php
                            echo form_error("notes");
                            $notes = array('name'=>"notes" , 'class'=>"form-control" , 'value'=> '');
                            echo form_textarea($notes);
                      ?>
                    </div>
                </div>

              </div>
             </div>
            <?php  echo isset($order_id) ? form_hidden('order_id', $order_id) : ''; ?>
            <div class="form-actions">
    			<div class="row">
    				<div class="col-md-offset-3 col-md-9">
                        <?php
                            $submit_att= array('class'=>"btn green");
                        ?>
    					<button type="submit" class="btn green"><i class="fa fa-check"></i> <?php echo lang('submit');?></button>

    				</div>
    			</div>
            </div>

    	</div>

</div>

<?php echo form_close();?>
<?php } ?>
<center><h3><?php echo lang('last_bills_for_this_order_number') . ' ' . $order_id; ?></h3></center>
<?php if(isset($order_bills)){?>
  <table class="table table-striped table-hover table-bordered" id="table">
  	<tbody>
      <tr class="header_tr">
     	  <!--<td> <?php //echo lang('order_id');?></td>-->
          <td> <?php echo lang('currency');?></td>
          <td><?php echo lang('Amount_of_money');?></td>
          <td><?php echo lang('order_total');?></td>
          <td><?php echo lang('order_rest_amount');?></td>
          <td><?php echo lang('order_paid_amount');?></td>
          <td><?php echo lang('notes');?></td>
          <td><?php echo lang('unix_time');?></td>

        </tr>
        
      <?php foreach($order_bills as $row){?>
        <tr>
              <!--<td> <?php //echo $row->order_id;?></td>-->
              <td> <?php echo $row->currency_symbol;?></td>
              <td> <?php echo $row->amount;?></td>
              <td> <?php echo $row->order_total;?></td>
              <td> <?php echo $row->order_rest;?></td>
              <td> <?php echo $row->order_paid;?></td>
              <td> <?php echo $row->notes;?></td>
              <td> <?php echo date('Y/m/d H:i', $row->unix_time);?></td>
        </tr>
      <?php }?>


  </tbody>
  </table>
<?php }?>
</div>
