<div class="portlet-body form">
	<form class="form-horizontal" role="form" method="post" action="<?php echo base_url();?>contact_us/admin/send_replay">
		<div class="form-body">
			<div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('name');?></label>
                <div class="col-md-4"><input type="text" name="name" value="<?php echo $msg->name;?>" /></div>
			</div>
            <!--<input type="file" name="userfile[]" multiple />-->
			<div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('email');?></label>
                <div class="col-md-4"><input type="text" name="email" value="<?php echo $msg->email;?>" readonly="true"/></div>
            </div><!--form-group -->
            
            <div class="form-group">
				<label class="control-label col-md-3"><?php echo lang('mobile');?></label>
                <div class="col-md-4"><input type="text" name="mobile" value="<?php echo $msg->mobile;?>" readonly="true"/></div>
			</div>
			
            <div class="form-group">
				<label class="control-label col-md-3"><?php echo lang('message_title');?></label>
                <div class="col-md-4"><input type="text" name="title" value="<?php echo $msg->title;?>" readonly="true"/></div>
			</div>
            
            <div class="form-group">
				<label class="control-label col-md-3"><?php echo lang('unix_time');?></label>
                <div class="col-md-4"><input type="text" name="unix_time" value="<?php echo date('Y/m/d H:i',$msg->unix_time);?>"readonly="true"/></div>
			</div>
            <div class="form-group">
				<label class="control-label col-md-3"><?php echo lang('message');?></label>
                <div class="col-md-4"><textarea readonly="true"><?php echo $msg->message;?></textarea></div>
			</div>
            
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('replay');?></label>
                <div class="col-md-4"><textarea name="replay"></textarea></div>
            </div>
            
		</div><!--form-body -->
		<div class="form-actions">
			<div class="row">
				<div class="col-md-offset-3 col-md-9">
                      <?php  echo  form_hidden('msg_id',$msg->id); ?>
					<button type="submit" class="btn green"><?php echo lang('submit');?></button>
					<!--<button type="button" class="btn default">Cancel</button>-->
				</div>
			</div>
		</div>
	</form>
</div><!--form -->
    
   	