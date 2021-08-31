<div class="portlet-body form">
	<form class="form-horizontal" role="form" method="post" action="<?php echo base_url();?>tickets/admin_tickets/save_replay" enctype="multipart/form-data">
		<div class="form-body">
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo lang('attached_files');?></label>
				<div class="col-md-4"><input type="file" name="userfile[]" multiple /></div>
			</div>
            <!--<input type="file" name="userfile[]" multiple />-->
			<div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('ticket_status');?></label>
                <div class="col-md-4">
                    <?php 
                         echo form_error("status_id");
                        $status_id = isset($ticket->status_id) ? $ticket->status_id : set_value('status_id') ;                   
                        
                        echo form_dropdown('status_id', $options,$status_id,'class="form-control select2" required="required"');

                    ?>
                </div><!--col-md-4 -->
            </div><!--form-group -->
            
            <div class="form-group">
				<label class="col-md-3 control-label"><?php echo lang('message_text');?></label>
				<div class="col-md-4">
                    <textarea class="form-control" name="message_text" placeholder="<?php echo lang('message_text');?>" ></textarea>
                </div>
			</div>
			
		</div><!--form-body -->
		<div class="form-actions">
			<div class="row">
				<div class="col-md-offset-3 col-md-9">
                     <?php  echo  form_hidden('ticket_id', $ticket_id) ; ?>
                      <?php echo isset($last_updated) ? form_hidden('last_updated', $last_updated) : ''; ?>
					<button type="submit" class="btn green"><?php echo lang('submit');?></button>
					<!--<button type="button" class="btn default">Cancel</button>-->
				</div>
			</div>
		</div>
	</form>
</div><!--form -->
    